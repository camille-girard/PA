<script setup lang="ts">
    import { useRouter } from 'vue-router';

    const router = useRouter();

    const goToAccount = () => {
        router.push('/profile/booking');
    };
</script>

<template>
    <main class="flex flex-col min-h-screen">
        <UHeader />

        <div class="flex-1 flex items-center justify-center px-4 pt-32">
            <section
                class="w-full max-w-xl text-center bg-green-50 border border-green-200 rounded-xl p-8 shadow-md space-y-6"
            >
                <div>
                    <h1 class="text-h1 text-green-700 mb-4">Réservation confirmée 🎉</h1>
                    <p class="text-lg text-gray-700">
                        Merci pour votre réservation ! Un email de confirmation vous a été envoyé.<br />
                        Vous pouvez maintenant consulter vos réservations dans votre espace personnel.
                    </p>
                </div>

                <div class="flex justify-center">
                    <UButton size="lg" variant="primary" @click="goToAccount"> Accéder à mes réservations </UButton>
                </div>
            </section>
        </div>

        <UFooter />
    </main>
</template>
