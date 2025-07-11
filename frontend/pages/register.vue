<script setup lang="ts">
    definePageMeta({
        middleware: 'guest',
    });
    useSeoMeta({
        title: 'Créer un compte - PopnBed',
        meta: [
            {
                name: 'description',
                content: 'Créez un compte sur PopnBed pour réserver des hébergements inspirés de films.',
            },
        ],
    });
    const authStore = useAuthStore();
    const router = useRouter();

    const form = reactive({
        email: '',
        firstName: '',
        lastName: '',
        password: '',
        confirmPassword: '',
    });

    const errors = reactive({});
    const isSubmitting = ref(false);

    const resetErrors = () => {
        const keys = Object.keys(errors);
        keys.forEach((key) => {
            errors[key] = undefined;
        });
    };

    const register = async () => {
        resetErrors();

        let isValid = true;

        if (!form.email) {
            errors.email = "L'email est requis.";
            isValid = false;
        } else if (!/\S+@\S+\.\S+/.test(form.email)) {
            errors.email = "L'email n'est pas valide.";
            isValid = false;
        }

        if (!form.firstName) {
            errors.firstName = 'Le prénom est requis.';
            isValid = false;
        }

        if (!form.lastName) {
            errors.lastName = 'Le nom est requis.';
            isValid = false;
        }

        if (!form.password) {
            errors.password = 'Le mot de passe est requis.';
            isValid = false;
        }

        if (form.password !== form.confirmPassword) {
            errors.confirmPassword = 'Les mots de passe ne correspondent pas';
            isValid = false;
        }

        if (!isValid) return;

        isSubmitting.value = true;

        try {
            const result = await authStore.register({
                email: form.email,
                firstName: form.firstName,
                lastName: form.lastName,
                password: form.password,
            });

            if (result.success) {
                router.push('/login');
            } else {
                if (result.errors) {
                    Object.assign(errors, result.errors);
                } else if (result.message) {
                    errors.email = result.message;
                }
            }
        } catch (error) {
            console.error("Erreur d'inscription:", error);
            errors.email = "Une erreur inattendue s'est produite. Veuillez réessayer.";
        } finally {
            isSubmitting.value = false;
        }
    };
</script>

<template>
    <main class="w-full h-full flex justify-center pt-20">
        <UGridBackgroundPattern class="absolute top-0" />
        <section class="flex flex-col items-center space-y-6 relative">
            <div class="text-center space-y-3">
                <h1 class="text-primary font-semibold text-3xl">Créer un compte</h1>
                <p class="text-tertiary font-normal">Veuillez entrer vos informations personnelles</p>
            </div>
            <form class="w-96" @submit.prevent="register">
                <div class="space-y-4">
                    <UInput
                        v-model="form.email"
                        type="email"
                        name="email"
                        placeholder="Email"
                        label="Email"
                        :hint-text="errors.email"
                        :destructive="Boolean(errors.email)"
                        required
                    />
                    <UInput
                        v-model="form.firstName"
                        type="text"
                        name="firstName"
                        placeholder="Prénom"
                        label="Prénom"
                        :hint-text="errors.firstName"
                        :destructive="Boolean(errors.firstName)"
                        required
                    />
                    <UInput
                        v-model="form.lastName"
                        type="text"
                        name="lastName"
                        placeholder="Nom"
                        label="Nom"
                        :hint-text="errors.lastName"
                        :destructive="Boolean(errors.lastName)"
                        required
                    />
                    <UInput
                        v-model="form.password"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        label="Mot de passe"
                        :hint-text="errors.password"
                        :destructive="Boolean(errors.password)"
                        required
                    />
                    <UInput
                        v-model="form.confirmPassword"
                        type="password"
                        name="confirmPassword"
                        placeholder="••••••••"
                        label="Confirmer le mot de passe"
                        :hint-text="errors.confirmPassword"
                        :destructive="Boolean(errors.confirmPassword)"
                        required
                    />
                </div>
                <UButton
                    class="mt-6 w-full justify-center"
                    type="submit"
                    :loading="isSubmitting"
                    :disabled="isSubmitting"
                >
                    S'inscrire
                </UButton>

                <div class="text-center mt-4">
                    <p class="text-tertiary flex items-center justify-center gap-1">
                        Vous avez déjà un compte ? <ULink to="/login" :variant="'primary'">Se connecter</ULink>
                    </p>
                </div>
            </form>
        </section>
    </main>
</template>
