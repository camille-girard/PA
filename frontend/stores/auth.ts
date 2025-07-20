import { defineStore } from 'pinia';

type User = {
    id: number;
    firstName: string;
    lastName: string;
    bio: string;
    email: string;
    phone?: string;
    address?: string;
    preferences?: string[];
    roles: string[];
    avatar: string;
    isTwoFactorEnabled?: boolean;
};

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as User | null,
        isAuthenticated: false,
        isLoading: false,
    }),

    actions: {
        async fetchUser() {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                const { data, error } = await useAuthFetch<User>($api('/api/me'), {
                    method: 'GET',
                    credentials: 'include',
                });

                if (data.value && !error.value) {
                    this.user = data.value;
                    this.isAuthenticated = true;
                } else {
                    this.user = null;
                    this.isAuthenticated = false;
                }
            } catch (error) {
                this.user = null;
                this.isAuthenticated = false;
                console.error('Error fetching user:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async updateUser(updatedData: Partial<User>) {
            this.isLoading = true;
            try {
                const { $api } = useNuxtApp();

                const { data, error } = await useAuthFetch<User>($api('/api/me'), {
                    method: 'PUT',
                    body: updatedData,
                    credentials: 'include',
                });

                if (data.value && !error.value) {
                    this.user = data.value;
                    return { success: true };
                } else {
                    return {
                        success: false,
                        error: error.value?.data?.message || 'Erreur lors de la mise à jour du profil',
                    };
                }
            } catch (error) {
                console.error('Update user error:', error);
                return {
                    success: false,
                    error: 'Une erreur est survenue lors de la mise à jour du profil',
                };
            } finally {
                this.isLoading = false;
            }
        },

        async deleteAccount() {
            this.isLoading = true;
            try {
                const { $api } = useNuxtApp();

                const { error } = await useFetch($api('/api/me'), {
                    method: 'DELETE',
                    credentials: 'include',
                });

                if (!error.value) {
                    this.user = null;
                    this.isAuthenticated = false;
                    return { success: true };
                }

                return {
                    success: false,
                    error: error.value?.data?.message || 'Erreur lors de la suppression',
                };
            } catch (error) {
                console.error('Delete account error:', error);
                return {
                    success: false,
                    error: 'Une erreur est survenue',
                };
            } finally {
                this.isLoading = false;
            }
        },

        async register(userData: { firstName: string; lastName: string; email: string; password: string }) {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                const requestData = {
                    ...userData,
                    username: userData.email,
                };

                const { error } = await useFetch($api('/api/register'), {
                    method: 'POST',
                    body: requestData,
                    credentials: 'include',
                });

                if (!error.value) {
                    return { success: true };
                }

                let errorMessage = 'Inscription échouée';

                if (error.value?.data) {
                    const errorData = error.value.data;

                    if (errorData.error) {
                        errorMessage = errorData.error;
                    } else if (errorData.message) {
                        errorMessage = errorData.message;
                    } else if (errorData.errors) {
                        const firstError = Object.values(errorData.errors)[0];
                        errorMessage = Array.isArray(firstError) ? firstError[0] : String(firstError);
                    }
                }

                return {
                    success: false,
                    message: errorMessage,
                    errors: error.value?.data?.errors || {},
                    statusCode: error.value?.statusCode,
                };
            } catch (error) {
                console.error('Register error:', error);
                return {
                    success: false,
                    message: "Une erreur est survenue lors de l'inscription",
                };
            } finally {
                this.isLoading = false;
            }
        },

        async login(email: string, password: string) {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                const { data, error } = await useFetch($api('/api/login'), {
                    method: 'POST',
                    body: { username: email, password },
                    credentials: 'include',
                });

                if (error.value) {
                    return { success: false, error: error.value?.message || 'Login failed' };
                }

                // Vérifier si la 2FA est requise
                if (data.value && typeof data.value === 'object' && 'requiresTwoFactor' in data.value) {
                    const twoFactorData = data.value as { requiresTwoFactor: boolean; email: string };
                    return { 
                        success: false, 
                        requiresTwoFactor: true, 
                        email: twoFactorData.email 
                    };
                }

                // Connexion réussie sans 2FA
                await this.fetchUser();
                return { success: true };
            } catch (error) {
                console.error('Login error:', error);
                return { success: false, error: 'Authentication failed' };
            } finally {
                this.isLoading = false;
            }
        },

        async loginWith2FA(email: string, password: string, code: string) {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                const { data, error } = await useFetch($api('/api/2fa/login-verify'), {
                    method: 'POST',
                    body: { email, password, code },
                    credentials: 'include',
                });

                if (error.value) {
                    return { success: false, error: error.value?.message || '2FA verification failed' };
                }

                if (data.value) {
                    await this.fetchUser();
                    return { success: true };
                }

                return { success: false, error: 'Invalid 2FA code' };
            } catch (error) {
                console.error('2FA Login error:', error);
                return { success: false, error: '2FA authentication failed' };
            } finally {
                this.isLoading = false;
            }
        },

        async logout(redirect = true) {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                // Appeler l'endpoint de logout (mais ne pas bloquer si ça échoue)
                await useFetch($api('/api/logout'), {
                    method: 'POST',
                    credentials: 'include',
                });
            } catch (error) {
                console.error('Logout API error:', error);
                // Continuer même si l'API échoue
            }

            // Toujours nettoyer l'état local
            this.user = null;
            this.isAuthenticated = false;

            // Rediriger vers la page de login si demandé et côté client
            if (redirect && import.meta.client) {
                console.log('Redirecting to login after logout...');
                await navigateTo('/login');
            }

            this.isLoading = false;
        },

        async refreshToken(): Promise<boolean> {
            try {
                const { $api } = useNuxtApp();
                
                // Utiliser useFetch directement pour éviter la référence circulaire avec useAuthFetch
                const { error } = await useFetch($api('/api/token/refresh'), {
                    method: 'POST',
                    credentials: 'include',
                    // Désactiver le cache pour cette requête critique
                    key: `refresh-token-${Date.now()}`,
                });

                if (error.value) {
                    console.log('Token refresh failed:', error.value);
                    // Nettoyer l'état d'authentification si le refresh échoue
                    this.user = null;
                    this.isAuthenticated = false;
                    return false;
                }

                console.log('Token refresh successful');
                // Si le refresh a réussi, maintenir l'état authentifié
                // Le nouveau JWT est automatiquement stocké dans le cookie httpOnly
                return true;
            } catch (error) {
                console.error('Token refresh error:', error);
                // En cas d'erreur, nettoyer l'état
                this.user = null;
                this.isAuthenticated = false;
                return false;
            }
        },
    },

    getters: {
        isAdmin(state) {
            return state.user?.roles?.includes('ROLE_ADMIN') ?? false;
        },
        
        isTwoFactorEnabled(state) {
            return state.user?.isTwoFactorEnabled ?? false;
        },
    },

    persist: true,
});
