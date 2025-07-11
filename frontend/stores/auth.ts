import { defineStore } from 'pinia';

type User = {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    address?: string;
    preferences?: string[];
    roles: string[];
    avatar: string;
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

                const { error } = await useFetch($api('/api/login'), {
                    method: 'POST',
                    body: { username: email, password },
                    credentials: 'include',
                });

                if (!error.value) {
                    await this.fetchUser();
                    return { success: true };
                }

                return { success: false, error: error.value?.message || 'Login failed' };
            } catch (error) {
                console.error('Login error:', error);
                return { success: false, error: 'Authentication failed' };
            } finally {
                this.isLoading = false;
            }
        },

        async logout() {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                await useFetch($api('/api/logout'), {
                    method: 'POST',
                    credentials: 'include',
                });

                this.user = null;
                this.isAuthenticated = false;
            } catch (error) {
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },

        async refreshToken(): Promise<boolean> {
            try {
                const { $api } = useNuxtApp();
                const { error } = await useFetch($api('/api/token/refresh'), {
                    method: 'POST',
                    credentials: 'include',
                });

                return !error.value;
            } catch {
                return false;
            }
        },
    },

    getters: {
        isAdmin(state) {
            return state.user?.roles?.includes('ROLE_ADMIN') ?? false;
        },
    },

    persist: true,
});
