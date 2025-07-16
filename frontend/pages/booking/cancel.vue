<script setup lang="ts">
    import { useRouter } from 'vue-router';

    const router = useRouter();

    const _goBack = () => {
        router.push('/profile/booking');
    };

    const goToAccount = () => {
        router.push('/profile');
    };
</script>

<template>
    <main class="flex flex-col min-h-screen">
        <UHeader />
        <div class="flex-1 flex items-center justify-center px-4">
            <section
                class="w-full max-w-xl text-center bg-red-50 border border-red-200 rounded-xl p-8 shadow-md space-y-6"
            >
                <div>
                    <h1 class="text-h1 text-red-600 mb-4">Paiement annulé</h1>
                    <p>
                        Vous avez annulé la procédure de paiement.<br />
                        Si c'était une erreur, vous pouvez réessayer depuis la page de réservation.
                    </p>
                </div>
                <div class="flex justify-center">
                    <UButton size="lg" variant="primary" @click="goToAccount"> Accéder à mes réservations</UButton>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
