import { useToast } from '~/composables/useToast';

interface Setup2FAResponse {
    qrCode: string;
    secret: string;
}

interface Verify2FAResponse {
    valid: boolean;
    message: string;
}

export const use2FA = () => {
    const toast = useToast();
    const authStore = useAuthStore();

    const user = computed(() => authStore.user);

    // States
    const showQrCodeModal = ref(false);
    const isLoading = ref(false);
    const qrCodeDataUrl = ref<string>('');
    const secret = ref<string>('');
    const verificationToken = ref<string>('');

    /**
     * Configure le TOTP pour l'utilisateur actuel
     */
    const setup2FA = async (): Promise<void> => {
        if (!user.value) {
            toast.error('Erreur', 'Utilisateur non connecté');
            return;
        }

        isLoading.value = true;
        try {
            console.log('🚀 Début setup 2FA...');
            const response = await useAuthFetch<Setup2FAResponse>('/api/2fa/setup', {
                method: 'POST',
            });

            console.log('📡 Réponse API reçue:', response);
            console.log('📊 Data:', response.data.value);
            console.log('❌ Error:', response.error.value);

            if (response.error.value) {
                console.error('❌ Erreur API:', response.error.value);
                const errorMessage = response.error.value?.data?.message || 'Erreur inconnue';
                toast.error('Erreur', errorMessage);
                return;
            }

            if (response.data.value) {
                // Le QR code est déjà en format data URL base64
                qrCodeDataUrl.value = response.data.value.qrCode;
                secret.value = response.data.value.secret;
                showQrCodeModal.value = true;
                console.log('✅ QR code configuré:', qrCodeDataUrl.value ? 'Oui' : 'Non');
                console.log('🔑 Secret configuré:', secret.value ? 'Oui' : 'Non');
                console.log('🎯 Modal ouverte:', showQrCodeModal.value);
            } else {
                console.error("❌ Aucune donnée reçue de l'API");
                toast.error('Erreur', 'Aucune donnée reçue du serveur');
            }
        } catch (error: unknown) {
            console.error('Erreur lors de la configuration 2FA:', error);
            const errorMessage =
                error instanceof Error ? error.message : "Impossible de configurer l'authentification à deux facteurs";
            toast.error('Erreur', errorMessage);
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Vérifie le code TOTP et active la 2FA
     */
    const enable = async (): Promise<void> => {
        if (!verificationToken.value || verificationToken.value.length !== 6) {
            toast.error('Erreur', 'Veuillez saisir un code à 6 chiffres');
            return;
        }

        isLoading.value = true;
        try {
            const { data, _error } = await useAuthFetch<Verify2FAResponse>('/api/2fa/verify', {
                method: 'POST',
                body: { code: verificationToken.value },
            });

            if (data.value && data.value.valid) {
                toast.success('Succès', 'Authentification à deux facteurs activée avec succès');
                showQrCodeModal.value = false;
                verificationToken.value = '';
                // Actualiser les données utilisateur
                await authStore.fetchUser();
            } else {
                toast.error('Erreur', data?.message || 'Code de vérification invalide');
            }
        } catch (error: unknown) {
            console.error('Erreur lors de la vérification 2FA:', error);
            const errorMessage = error instanceof Error ? error.message : 'Impossible de vérifier le code';
            toast.error('Erreur', errorMessage);
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Vérifie un code TOTP (utilisé pour le login)
     */
    const verify2FA = async (code: string): Promise<boolean> => {
        if (!code || code.length !== 6) {
            toast.error('Erreur', 'Veuillez saisir un code à 6 chiffres');
            return false;
        }

        try {
            const response = await useAuthFetch<Verify2FAResponse>('/api/2fa/verify', {
                method: 'POST',
                body: { code },
            });

            if (response.data?.valid) {
                return true;
            } else {
                toast.error('Erreur', response.data?.message || 'Code de vérification invalide');
                return false;
            }
        } catch (error: unknown) {
            console.error('Erreur lors de la vérification 2FA:', error);
            const errorMessage = error instanceof Error ? error.message : 'Impossible de vérifier le code';
            toast.error('Erreur', errorMessage);
            return false;
        }
    };

    /**
     * Désactive la 2FA pour l'utilisateur
     */
    const disable2FA = async (): Promise<void> => {
        // TODO: Implémenter côté backend d'abord
        toast.info('Information', 'Fonctionnalité en cours de développement');
    };

    /**
     * Ferme la modal et remet à zéro les données
     */
    const closeModal = (): void => {
        showQrCodeModal.value = false;
        verificationToken.value = '';
        qrCodeDataUrl.value = '';
        secret.value = '';
    };

    return {
        // States
        showQrCodeModal,
        isLoading,
        qrCodeDataUrl,
        secret,
        verificationToken,
        user: readonly(user),

        // Methods
        setup2FA,
        enable,
        verify2FA,
        disable2FA,
        closeModal,
    };
};
