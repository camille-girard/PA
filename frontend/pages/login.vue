<script setup lang="ts">
    useSeoMeta({
        title: 'Login - PopnBed',
        description: 'Accédez à votre compte PopnBed pour réserver des hébergements inspirés de films.',
    });

    definePageMeta({
        middleware: 'auth',
    });

    const authStore = useAuthStore();
    const router = useRouter();
    const toast = useToast();

    const email = ref<string>('');
    const password = ref<string>('');
    const isLoading = ref<boolean>(false);

    const login = async () => {
        isLoading.value = true;
        try {
            const result = await authStore.login(email.value, password.value);

            if (result.success) {
                toast.success('Connexion réussie', 'Heureux de vous revoir sur PopnBed !');
                if (authStore.isAdmin) {
                    return router.push('/backoffice');
                }
                return router.push('/');
            } else {
                toast.error(
                    'Échec de connexion',
                    result.error || 'Identifiants incorrects. Veuillez vérifier votre email et mot de passe.'
                );
            }
        } catch (error: unknown) {
            const errorMessage =
                error instanceof Error
                    ? error.message
                    : "Une erreur inattendue s'est produite. Veuillez réessayer ultérieurement.";
            toast.error('Problème de connexion', errorMessage);
        } finally {
            isLoading.value = false;
        }
    };
</script>

<template>
    <main class="flex flex-col flex-grow">
        <UHeader />
        <div class="my-20 py-20 rounded-2xl flex items-center justify-center flex-grow">
            <UGridBackgroundPattern class="absolute top-0 opacity-20" />
            <section class="flex flex-col items-center space-y-8 relative">
                <div class="text-center space-y-3">
                    <h1 class="text-h1">Bon retour parmi nous</h1>
                    <p class="text-body-md">Connectez-vous pour accéder à votre espace PopnBed</p>
                </div>
                <form class="w-96" @submit.prevent="login">
                    <div class="space-y-5">
                        <UInput v-model="email" type="email" name="email" placeholder="Email" label="Email" required />
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
                    <UButton class="mt-6 w-full justify-center" type="submit" :loading="isLoading">
                        {{ isLoading ? 'Connexion en cours...' : 'Se connecter' }}
                    </UButton>
                </form>
                <div class="text-center">
                    <p class="text-body-md flex items-center justify-center gap-1">
                        Vous n'avez pas de compte ?
                        <ULink to="/register">S'enregistrer</ULink>
                    </p>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
