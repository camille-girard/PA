<script setup lang="ts">
    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import UHeader from '~/components/UHeader.vue';
    import UFooter from '~/components/UFooter.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import UTextarea from '~/components/atoms/UTextarea.vue';
    import { useRuntimeConfig } from '#app';

    definePageMeta({
        middleware: 'owner',
    });

    const router = useRouter();
    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const title = ref('');
    const description = ref('');
    const message = ref('');

    const loading = ref(false);
    const success = ref('');
    const error = ref('');

    async function submitTicket() {
        error.value = '';
        success.value = '';
        if (!title.value || !message.value) {
            error.value = 'Le titre et le premier message sont obligatoires.';
            return;
        }

        loading.value = true;

        try {
            await useAuthFetch('/api/tickets', {
                method: 'POST',
                baseURL: apiUrl,
                body: {
                    title: title.value,
                    description: description.value,
                    message: message.value,
                },
            });

            success.value = 'Ticket créé avec succès.';
            router.push('/my-tickets');
        } catch (e: any) {
            error.value = e?.data?.message || 'Erreur lors de la création du ticket.';
        } finally {
            loading.value = false;
        }
    }
</script>

<template>
    <main class="w-full h-full">
        <UHeader />

        <div class="max-w-4xl mx-auto mt-20 px-4 py-16">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold mb-2">Créer un ticket de support</h1>
                <p class="text-gray-600">Expliquez votre problème pour obtenir de l'aide plus rapidement</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-lg space-y-8">
                <div class="space-y-2">
                    <label class="block text-base font-medium"
                        >Titre du ticket <span class="text-red-500">*</span></label
                    >
                    <input
                        v-model="title"
                        type="text"
                        class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        placeholder="Exemple : Problème de réservation"
                    />
                </div>

                <div class="space-y-2">
                    <label class="block text-base font-medium">Description détaillée (optionnelle)</label>
                    <UTextarea
                        v-model="description"
                        placeholder="Décrivez votre problème plus en détail si besoin..."
                        rows="4"
                    />
                </div>

                <div class="space-y-2">
                    <label class="block text-base font-medium"
                        >Premier message <span class="text-red-500">*</span></label
                    >
                    <UTextarea v-model="message" placeholder="Expliquez votre demande ou problème..." rows="5" />
                </div>

                <div class="flex justify-end">
                    <UButton :disabled="loading" @click="submitTicket">
                        {{ loading ? 'Envoi en cours...' : 'Créer le ticket' }}
                    </UButton>
                </div>

                <div v-if="success" class="text-green-600 text-sm">{{ success }}</div>
                <div v-if="error" class="text-red-600 text-sm">{{ error }}</div>
            </div>
        </div>

        <UFooter />
    </main>
</template>
