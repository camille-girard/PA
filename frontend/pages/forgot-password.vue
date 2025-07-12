<script setup lang="ts">
    definePageMeta({
        middleware: 'guest',
    });

    useSeoMeta({
        title: 'Mot de passe oublié - PopnBed',
        description: 'Réinitialisez votre mot de passe PopnBed en recevant un lien par email.',
    });

    const toast = useToast();

    const form = reactive({
        email: '',
    });

    const loading = ref(false);

    const submitForm = async () => {
        if (!form.email) return;

        loading.value = true;

        try {
            const response = await $fetch('/api/reset-password/request', {
                method: 'POST',
                body: {
                    email: form.email,
                },
            });

            toast.success(
                'Email envoyé',
                response.message ||
                    'Si un compte avec cette adresse email existe, vous recevrez un lien de réinitialisation.'
            );
            form.email = '';
        } catch (err: unknown) {
            const errorMessage = (err as unknown).data?.error || 'Une erreur est survenue. Veuillez réessayer.';
            toast.error('Erreur', errorMessage);
        } finally {
            loading.value = false;
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
                    <h1 class="text-h1">Mot de passe oublié</h1>
                    <p class="text-body-md">Entrez votre adresse email pour recevoir un lien de réinitialisation</p>
                </div>
                <form class="w-96" @submit.prevent="submitForm">
                    <div class="space-y-5">
                        <UInput
                            v-model="form.email"
                            type="email"
                            name="email"
                            placeholder="votre.email@exemple.com"
                            label="Adresse email"
                            required
                            :disabled="loading"
                        />
                    </div>
                    <UButton
                        class="mt-6 w-full justify-center"
                        type="submit"
                        :loading="loading"
                        :disabled="!form.email"
                    >
                        {{ loading ? 'Envoi en cours...' : 'Envoyer le lien de réinitialisation' }}
                    </UButton>
                </form>
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
