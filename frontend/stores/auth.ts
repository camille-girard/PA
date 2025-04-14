import { defineStore } from "pinia";

type User = {
    id: number,
    fullName: string,
    email: string,
    createdAt: string,
    updatedAt: string
}

type TokenPayload = {
    accessToken: string,
    refreshToken: string,
    expiresIn: number
}

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null as User | null,
        accessToken: null as string | null,
        refreshToken: null as string | null,
        tokenExpiry: null as number | null,
        isAuthenticated: false,
        isLoading: false,
        refreshPromise: null as Promise<boolean> | null,
    }),

    getters: {
        isTokenExpired(): boolean {
            if (!this.tokenExpiry) return true;
            // Add a 10-second buffer to handle potential timing issues
            return Date.now() >= (this.tokenExpiry - 10000);
        }
    },

    actions: {
        setTokens(payload: TokenPayload) {
            this.accessToken = payload.accessToken;
            this.refreshToken = payload.refreshToken;
            this.tokenExpiry = Date.now() + (payload.expiresIn * 1000);
            this.isAuthenticated = true;
        },

        clearTokens() {
            this.accessToken = null;
            this.refreshToken = null;
            this.tokenExpiry = null;
            this.isAuthenticated = false;
            this.user = null;
        },

        async refreshAccessToken(): Promise<boolean> {
            // If there's already a refresh in progress, return that promise
            if (this.refreshPromise) {
                return this.refreshPromise;
            }

            // No refresh token, can't refresh
            if (!this.refreshToken) {
                this.clearTokens();
                return false;
            }

            // Create a promise that will resolve with the result of the refresh
            const promise = new Promise<boolean>((resolve) => {
                const doRefresh = async () => {
                    try {
                        const { $api } = useNuxtApp();

                        const { data, error } = await useFetch<TokenPayload>($api("/auth/refresh"), {
                            method: "POST",
                            body: { refreshToken: this.refreshToken },
                        });

                        if (data.value && !error.value) {
                            this.setTokens(data.value);
                            resolve(true);
                        } else {
                            this.clearTokens();
                            resolve(false);
                        }
                    } catch (error) {
                        console.error("Token refresh error:", error);
                        this.clearTokens();
                        resolve(false);
                    } finally {
                        this.refreshPromise = null;
                    }
                };

                // Execute the refresh
                doRefresh();
            });

            this.refreshPromise = promise;
            return promise;
        },

        async ensureValidToken(): Promise<boolean> {
            if (!this.isAuthenticated) return false;
            if (!this.isTokenExpired) return true;
            return this.refreshAccessToken();
        },

        async fetchUser() {
            this.isLoading = true;

            try {
                // Ensure we have a valid token before making the request
                if (!(await this.ensureValidToken())) {
                    this.clearTokens();
                    return false;
                }

                const { $api } = useNuxtApp();

                const { data, error } = await useFetch<User>($api("/me"), {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${this.accessToken}`
                    }
                });

                if (data.value && !error.value) {
                    this.user = data.value;
                    return true;
                } else {
                    this.clearTokens();
                    return false;
                }
            } catch (error) {
                this.clearTokens();
                console.error("Error fetching user:", error);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async login(email: string, password: string) {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                const { data, error } = await useFetch<TokenPayload>($api("/auth/login"), {
                    method: "POST",
                    body: { email, password },
                });

                if (data.value && !error.value) {
                    this.setTokens(data.value);
                    await this.fetchUser();
                    return { success: true };
                }

                return { success: false, error: error.value?.message || "Login failed" };
            } catch (error) {
                console.error("Login error:", error);
                return { success: false, error: "Authentication failed" };
            } finally {
                this.isLoading = false;
            }
        },

        async logout() {
            this.isLoading = true;

            try {
                if (this.accessToken) {
                    const { $api } = useNuxtApp();

                    await useFetch($api('/auth/logout'), {
                        method: 'POST',
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        },
                        body: { refreshToken: this.refreshToken }
                    });
                }
            } catch(error) {
                console.error("Logout error:", error);
            } finally {
                this.clearTokens();
                this.isLoading = false;
            }
        }
    },

    persist: true
});
