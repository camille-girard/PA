<script setup lang="ts">
    useSeoMeta({
        title: 'Login - PopnBed',
        description: 'Accédez à votre compte PopnBed pour réserver des hébergements inspirés de films.',
    });

    definePageMeta({
        middleware: 'auth',
    });

    const _authStore = useAuthStore();
    const _router = useRouter();
    const _toast = useToast();

    // Utilisation du composable 2FA
    const {
        showTwoFactorVerification,
        loginCredentials,
        isLoginLoading,
        initiateLogin,
        handleTwoFactorVerification,
        cancelTwoFactorVerification,
        resetAuthState,
    } = useAuth2FA();

    const email = ref<string>('');
    const password = ref<string>('');

    const login = async () => {
        await initiateLogin(email.value, password.value);
    };

    // Cleanup au démontage
    onUnmounted(() => {
        resetAuthState();
    });
</script>

<template>
    <main class="flex flex-col flex-grow">
        <UHeader />
        <div class="my-20 py-20 rounded-2xl flex items-center justify-center flex-grow">
            <UGridBackgroundPattern class="absolute top-0 opacity-20" />
            <section class="flex flex-col items-center space-y-8 relative">
                <!-- Formulaire de connexion principal -->
                <div v-if="!showTwoFactorVerification" class="flex flex-col items-center space-y-8">
                    <div class="text-center space-y-3">
                        <h1 class="text-h1">Bon retour parmi nous</h1>
                        <p class="text-body-md">Connectez-vous pour accéder à votre espace PopnBed</p>
                    </div>
                    <form class="w-96" @submit.prevent="login">
                        <div class="space-y-5">
                            <UInput
                                v-model="email"
                                type="email"
                                name="email"
                                placeholder="Email"
                                label="Email"
                                required
                            />
                            <UInput
                                v-model="password"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                label="Mot de passe"
                                required
                            />
                        </div>
                        <div class="flex justify-end mt-2">
                            <ULink to="/forgot-password"> Mot de passe oublié ? </ULink>
                        </div>
                        <UButton class="mt-6 w-full justify-center" type="submit" :loading="isLoginLoading">
                            {{ isLoginLoading ? 'Connexion en cours...' : 'Se connecter' }}
                        </UButton>
                    </form>
                    <div class="text-center">
                        <p class="text-body-md flex items-center justify-center gap-1">
                            Vous n'avez pas de compte ?
                            <ULink to="/register">S'enregistrer</ULink>
                        </p>
                    </div>
                </div>

                <!-- Composant de vérification 2FA -->
                <div v-if="showTwoFactorVerification">
                    <TwoFactorAuthVerification
                        :email="loginCredentials?.email"
                        :password="loginCredentials?.password"
                        :on-verification-success="handleTwoFactorVerification"
                        :on-cancel="cancelTwoFactorVerification"
                    />
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
