<script setup lang="ts">
    import LockIcon from '~/components/atoms/icons/LockIcon.vue';

    const { showQrCodeModal, user, setup2FA, isLoading, qrCodeDataUrl, secret, enable, verificationToken, closeModal } =
        use2FA();
</script>

<template>
    <div class="space-y-4">
        <!-- Bouton d'activation/désactivation -->
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Authentification à deux facteurs</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Renforcez la sécurité de votre compte avec l'authentification à deux facteurs
                </p>
            </div>
            <UButton v-if="!user?.isTwoFactorEnabled" :disabled="isLoading" @click="setup2FA()">
                <template #leading>
                    <LockIcon class="w-4 h-4" />
                </template>
                Activer la 2FA
            </UButton>
            <UBadge v-else color="success"> 2FA Activée </UBadge>
        </div>

        <!-- Modal de configuration -->
        <UBaseModal :is-open="showQrCodeModal" @close="closeModal">
            <div class="px-6 py-6">
                <UFeaturedIcon :icon="LockIcon" size="lg" color="gray" />
                <div class="space-y-0.5 mt-4">
                    <h3 class="font-semibold text-primary">Configuration de l'authentification à deux facteurs</h3>
                    <p class="text-tertiary text-sm">
                        Scannez ce QR code avec votre application d'authentification (Google Authenticator, Authy, etc.)
                    </p>
                </div>
            </div>

            <div class="px-6">
                <!-- QR Code -->
                <div class="bg-secondary rounded-lg w-full flex items-center justify-center p-5">
                    <div v-if="qrCodeDataUrl" class="flex flex-col items-center space-y-4">
                        <img :src="qrCodeDataUrl" alt="QR Code pour authentification 2FA" class="rounded-lg" />
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
                            Impossible de scanner ? Saisissez manuellement cette clé dans votre application
                        </p>
                        <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded break-all">
                            {{ secret }}
                        </code>
                    </div>
                    <div v-else class="flex items-center justify-center h-48">
                        <ULoading />
                    </div>
                </div>

                <!-- Input pour le code de vérification -->
                <UDigitsInput
                    v-model="verificationToken"
                    class="mt-6 w-full"
                    label="Code de vérification"
                    :disabled="isLoading"
                />

                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    Saisissez le code à 6 chiffres généré par votre application d'authentification
                </p>
            </div>

            <div class="pt-8">
                <div class="flex items-center justify-between gap-3 px-6 pb-6">
                    <UButton variant="secondary" class="w-full" :disabled="isLoading" @click="closeModal">
                        Annuler
                    </UButton>
                    <UButton
                        class="w-full"
                        :disabled="isLoading || !verificationToken || verificationToken.length !== 6"
                        :loading="isLoading"
                        @click="enable"
                    >
                        Confirmer et activer
                    </UButton>
                </div>
            </div>
        </UBaseModal>
    </div>
</template>
