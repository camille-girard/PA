/**
 * Composable pour gérer l'authentification avec 2FA
 */
export const useAuth2FA = () => {
    const authStore = useAuthStore();
    const { verify2FA: _verify2FA } = use2FA();
    const toast = useToast();

    // States
    const showTwoFactorVerification = ref(false);
    const loginCredentials = ref<{ email: string; password: string } | null>(null);
    const isLoginLoading = ref(false);

    /**
     * Première étape du login - vérification email/password
     */
    const initiateLogin = async (email: string, password: string): Promise<void> => {
        isLoginLoading.value = true;

        try {
            // Tentative de connexion normale
            const loginResult = await authStore.login(email, password);

            if (loginResult.success) {
                // Connexion réussie sans 2FA
                navigateTo('/');
                return;
            }

            if (loginResult.requiresTwoFactor) {
                // 2FA requise - afficher le composant de vérification
                loginCredentials.value = { email, password };
                showTwoFactorVerification.value = true;
                return;
            }

            // Erreur de connexion
            toast.error('Erreur de connexion', loginResult.error || 'Identifiants incorrects');
        } catch (error: unknown) {
            console.error('Erreur lors de la connexion:', error);
            const errorMessage =
                error instanceof Error ? error.message : 'Une erreur est survenue lors de la connexion';
            toast.error('Erreur', errorMessage);
        } finally {
            isLoginLoading.value = false;
        }
    };

    /**
     * Deuxième étape du login - vérification 2FA
     */
    const handleTwoFactorVerification = async (): Promise<void> => {
        toast.success('Succès', 'Authentification réussie');
        showTwoFactorVerification.value = false;
        loginCredentials.value = null;
        navigateTo('/');
    };

    /**
     * Annulation de la vérification 2FA
     */
    const cancelTwoFactorVerification = (): void => {
        showTwoFactorVerification.value = false;
        loginCredentials.value = null;
        toast.info('Information', 'Connexion annulée');
    };

    /**
     * Reset des états
     */
    const resetAuthState = (): void => {
        showTwoFactorVerification.value = false;
        loginCredentials.value = null;
        isLoginLoading.value = false;
    };

    return {
        // States
        showTwoFactorVerification: readonly(showTwoFactorVerification),
        loginCredentials: readonly(loginCredentials),
        isLoginLoading: readonly(isLoginLoading),

        // Methods
        initiateLogin,
        handleTwoFactorVerification,
        cancelTwoFactorVerification,
        resetAuthState,
    };
};
