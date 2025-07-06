<template>
    <main class="flex flex-col flex-grow">
        <UHeader />
        <div class="my-20 py-20 rounded-2xl flex items-center justify-center relative flex-grow">
            <UGridBackgroundPattern class="absolute top-0 opacity-20" />
            <section class="flex flex-col items-center space-y-8 relative">
                <div class="text-center space-y-3">
                    <h1 class="text-h1">Réinitialiser votre mot de passe</h1>
                    <p class="text-body-md">Entrez votre nouveau mot de passe</p>
                </div>

                <form v-if="!success" class="w-96" @submit.prevent="resetPassword">
                    <div class="space-y-5">
                        <UInput
                            v-model="password"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            label="Nouveau mot de passe"
                            :hint-text="errors.password"
                            :destructive="Boolean(errors.password)"
                            required
                        />
                        <UInput
                            v-model="confirmPassword"
                            type="password"
                            name="confirmPassword"
                            placeholder="••••••••"
                            label="Confirmer le mot de passe"
                            :hint-text="errors.confirmPassword"
                            :destructive="Boolean(errors.confirmPassword)"
                            required
                        />
                    </div>
                    <UButton class="mt-6 w-full justify-center" type="submit" :loading="loading" :disabled="loading">
                        {{ loading ? 'Réinitialisation en cours...' : 'Réinitialiser le mot de passe' }}
                    </UButton>
                </form>

                <div v-else class="text-center">
                    <div class="space-y-4">
                        <div class="text-green-600">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <p class="text-body-md">Mot de passe réinitialisé avec succès !</p>
                        <p class="text-body-sm text-gray-600">Vous allez être redirigé vers la page de connexion...</p>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-body-md flex items-center justify-center gap-1">
                        Vous vous souvenez de votre mot de passe ?
                        <ULink to="/login">Se connecter</ULink>
                    </p>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>

<script setup lang="ts">
    definePageMeta({
        middleware: 'guest',
    });

    useSeoMeta({
        title: 'Réinitialiser votre mot de passe - PopnBed',
        meta: [
            {
                name: 'description',
                content: 'Définissez un nouveau mot de passe pour votre compte PopnBed.',
            },
        ],
    });

    const route = useRoute();
    const router = useRouter();
    const toast = useToast();

    const token = route.query.token as string;
    const password = ref('');
    const confirmPassword = ref('');
    const loading = ref(false);
    const success = ref(false);

    const errors = reactive({
        password: '',
        confirmPassword: '',
    });

    // Vérifier si le token est présent
    onMounted(() => {
        if (!token) {
            toast.error('Token invalide', 'Token de réinitialisation manquant ou invalide.');
            router.push('/forgot-password');
        }
    });

    const resetErrors = () => {
        errors.password = '';
        errors.confirmPassword = '';
    };

    const resetPassword = async () => {
        resetErrors();

        let isValid = true;

        if (!password.value) {
            errors.password = 'Le mot de passe est requis.';
            isValid = false;
        } else if (password.value.length < 8) {
            errors.password = 'Le mot de passe doit contenir au moins 8 caractères.';
            isValid = false;
        }

        if (password.value !== confirmPassword.value) {
            errors.confirmPassword = 'Les mots de passe ne correspondent pas.';
            isValid = false;
        }

        if (!isValid) return;

        loading.value = true;

        try {
            const response = await $fetch('/api/reset-password/reset', {
                method: 'POST',
                body: {
                    token: token,
                    password: password.value,
                },
            });

            if (response.success) {
                success.value = true;
                toast.success('Succès', 'Votre mot de passe a été réinitialisé avec succès !');
                setTimeout(() => {
                    router.push('/login');
                }, 2000);
            } else {
                toast.error('Erreur', response.message || 'Une erreur est survenue lors de la réinitialisation.');
            }
        } catch (error: any) {
            const errorMessage = error.data?.message || 'Une erreur est survenue. Veuillez réessayer.';
            toast.error('Erreur', errorMessage);
        } finally {
            loading.value = false;
        }
    };
</script>
