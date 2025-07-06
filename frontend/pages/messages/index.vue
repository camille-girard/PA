<script setup lang="ts">
    import { useConversationStore } from '~/stores/conversation';
    import { useAuthStore } from '~/stores/auth';
    import { useMercure } from '~/composables/useMercure';

    definePageMeta({
        middleware: ['auth'],
    });

    const conversationStore = useConversationStore();
    const authStore = useAuthStore();
    const { subscribeToUserMessages } = useMercure();
    const isLoading = ref(true);

    onMounted(async () => {
        try {
            await conversationStore.fetchConversations();

            // Subscribe to user messages via Mercure if user is authenticated
            if (authStore.user) {
                await subscribeToUserMessages(authStore.user.id);
            }
        } finally {
            isLoading.value = false;
        }
    });
</script>

<template>
    <NuxtLayout name="default">
        <main class="w-full h-full flex-grow">
            <UHeader />

            <div class="max-w-7xl w-full mx-auto pt-8 px-4">
                <section id="information-profile" class="w-full pt-8">
                    <div class="py-20 rounded-2xl flex items-center justify-center relative">
                        <div class="text-center z-10">
                            <h1 class="text-h1">Messagerie</h1>
                        </div>
                    </div>
                </section>

                <div v-if="isLoading" class="flex justify-center items-center h-64">
                    <ULoading />
                </div>

                <div
                    v-else-if="conversationStore.conversations.length === 0"
                    class="text-center p-8 bg-white dark:bg-gray-900 rounded-lg shadow"
                >
                    <h2 class="text-xl mb-4">Aucune conversation</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Vous n'avez pas encore de conversations. Contactez un propriétaire ou un client pour commencer à
                        échanger.
                    </p>
                </div>

                <div v-else class="border rounded-lg shadow overflow-hidden bg-white dark:bg-gray-900">
                    <ConversationList
                        :conversations="conversationStore.sortedConversations"
                        @select="(id: number) => navigateTo(`/messages/${id}`)"
                    />
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>

