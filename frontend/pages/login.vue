<script setup>
    useSeoMeta({
        title: 'Login - PopnBed',
        description: 'Accédez à votre compte PopnBed pour réserver des hébergements inspirés de films.',
    });
    definePageMeta({
        middleware: 'auth',
    });

    const authStore = useAuthStore();
    const router = useRouter();

    const email = ref('');
    const password = ref('');

    const login = async () => {
        const result = await authStore.login(email.value, password.value);

        if (result.success) {
            router.push('/');
        }
    };
</script>

<template>
    <main>
        <UHeader />
        <div class="my-20 py-20 rounded-2xl flex items-center justify-center relative">
            <UGridBackgroundPattern class="absolute top-0 opacity-20" />
            <section class="flex flex-col items-center space-y-8 relative">
                <div class="text-center space-y-3">
                    <h1 class="text-h1">Welcome back</h1>
                    <p class="text-body-md">Welcome back! Please enter your details</p>
                </div>
                <form class="w-96" @submit.prevent="login">
                    <div class="space-y-5">
                        <UInput v-model="email" type="email" name="email" placeholder="Email" label="Email" required />
                        <UInput
                            v-model="password"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            label="Password"
                            required
                        />
                    </div>
                    <UButton class="mt-6 w-full justify-center" type="submit">Submit</UButton>
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
