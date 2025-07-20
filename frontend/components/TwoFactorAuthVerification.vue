<script setup lang="ts">
    import LockIcon from '~/components/atoms/icons/LockIcon.vue';

    interface Props {
        email?: string;
        password?: string;
        onVerificationSuccess?: () => void;
        onCancel?: () => void;
    }

    const props = withDefaults(defineProps<Props>(), {
        email: '',
        password: '',
        onVerificationSuccess: () => {},
        onCancel: () => {},
    });

    const authStore = useAuthStore();
    const verificationCode = ref<string>('');
    const isLoading = ref(false);
    const errorMessage = ref<string>('');

    /**
     * Vérifie le code 2FA
     */
    const handleVerification = async (): Promise<void> => {
        if (!verificationCode.value || verificationCode.value.length !== 6) {
            errorMessage.value = 'Veuillez saisir un code à 6 chiffres';
            return;
        }

        if (!props.email) {
            errorMessage.value = 'Email manquant';
            return;
        }

        isLoading.value = true;
        errorMessage.value = '';

        try {
            if (!props.email || !props.password) {
                errorMessage.value = 'Informations de connexion manquantes';
                return;
            }

            const result = await authStore.loginWith2FA(props.email, props.password, verificationCode.value);

            if (result.success) {
                props.onVerificationSuccess();
            } else {
                errorMessage.value = result.error || 'Code de vérification invalide';
                verificationCode.value = '';
            }
        } catch {
            errorMessage.value = 'Erreur lors de la vérification';
            verificationCode.value = '';
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Annule la vérification 2FA
     */
    const handleCancel = (): void => {
        verificationCode.value = '';
        errorMessage.value = '';
        props.onCancel();
    };

    /**
     * Gère l'appui sur Entrée
     */
    const handleKeydown = (event: KeyboardEvent): void => {
        if (event.key === 'Enter' && verificationCode.value.length === 6) {
            handleVerification();
        }
    };
</script>

<template>
    <div class="w-full max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <!-- En-tête -->
        <div class="text-center mb-6">
            <UFeaturedIcon :icon="LockIcon" size="lg" color="gray" class="mx-auto mb-4" />
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Vérification en deux étapes</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                <span v-if="props.email"> Saisissez le code envoyé pour {{ props.email }} </span>
                <span v-else> Saisissez le code généré par votre application d'authentification </span>
            </p>
        </div>

        <!-- Input code de vérification -->
        <div class="space-y-4">
            <UDigitsInput
                v-model="verificationCode"
                label="Code de vérification"
                :disabled="isLoading"
                class="w-full"
                @keydown="handleKeydown"
            />

            <!-- Message d'erreur -->
            <div v-if="errorMessage" class="text-red-600 text-sm text-center">
                {{ errorMessage }}
            </div>

            <!-- Instructions -->
            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                Le code expire dans quelques minutes. Si vous ne l'avez pas reçu, vérifiez votre application
                d'authentification.
            </p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col space-y-3 mt-6">
            <UButton
                :disabled="isLoading || verificationCode.length !== 6"
                :loading="isLoading"
                size="lg"
                class="w-full"
                @click="handleVerification"
            >
                Vérifier
            </UButton>

            <UButton variant="secondary" size="lg" class="w-full" :disabled="isLoading" @click="handleCancel">
                Annuler
            </UButton>
        </div>

        <!-- Aide -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Problème avec votre authentificateur ?
                <ULink to="/contact" class="text-orange-600 hover:text-orange-700"> Contactez le support </ULink>
            </p>
        </div>
    </div>
</template>
