<script setup>
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
    <main class="w-full h-full flex justify-center pt-40">
        <UGridBackgroundPattern class="absolute top-0" />
        <section class="flex flex-col items-center space-y-8 relative">
            <div class="text-center space-y-3">
                <h1 class="text-primary font-semibold text-3xl">Welcome back</h1>
                <p class="text-tertiary font-normal">Welcome back! Please enter your details</p>
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
        </section>
    </main>
</template>
