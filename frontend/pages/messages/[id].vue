<script setup lang="ts">
    import { useConversationStore } from '~/stores/conversation';
    import { useAuthStore } from '~/stores/auth';
    import { useMercure } from '~/composables/useMercure';
    import ArrowLeftIcon from '~/components/atoms/icons/ArrowLeftIcon.vue';

    definePageMeta({
        middleware: ['auth'],
    });

    const route = useRoute();
    const conversationStore = useConversationStore();
    const { subscribeToConversation, unsubscribeFromConversation } = useMercure();
    const isLoading = ref(true);
    const conversationId = Number(route.params.id);

    const getPartnerName = () => {
        if (!conversationStore.currentConversation) return '';

        const authStore = useAuthStore();
        const isClient = authStore.user?.roles.includes('ROLE_CLIENT');

        if (isClient) {
            const owner = conversationStore.currentConversation.owner;
            return `${owner.firstName} ${owner.lastName}`;
        } else {
            const client = conversationStore.currentConversation.client;
            return `${client.firstName} ${client.lastName}`;
        }
    };

    onMounted(async () => {
        isLoading.value = true;
        try {
            await conversationStore.fetchConversation(conversationId);
            await subscribeToConversation(conversationId);
        } catch (error) {
            console.error(`Error loading conversation ${conversationId}:`, error);
        } finally {
            isLoading.value = false;
        }
    });

    onBeforeUnmount(() => {
        unsubscribeFromConversation(conversationId);
    });

    const handleSendMessage = async (content: string) => {
        if (!conversationStore.currentConversation) return;

        try {
            await conversationStore.sendMessage(conversationId, content);
        } catch (error) {
            console.error('Error sending message:', error);
        }
    };
</script>

<template>
    <NuxtLayout name="default">
        <main class="w-full h-full flex-grow">
            <UHeader />

            <div class="container mx-auto px-4 pb-8 pt-20">
                <div class="mb-6 flex items-center justify-between pt-8">
                    <div class="flex items-center">
                        <NuxtLink to="/messages" class="text-blue-500 mr-4">
                            <UButton variant="secondary" :icon="ArrowLeftIcon" icon-position="leading">
                                Retour
                            </UButton>
                        </NuxtLink>
                        <h1 class="text-2xl font-bold">{{ getPartnerName() }}</h1>
                    </div>
                </div>

                <div class="border rounded-lg overflow-hidden bg-white dark:bg-gray-900 shadow h-[70vh]">
                    <div v-if="isLoading" class="flex justify-center items-center h-full">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                    </div>

                    <template v-else-if="conversationStore.currentConversation">
                        <MessagesList :conversation="conversationStore.currentConversation" @send="handleSendMessage" />
                    </template>

                    <div v-else class="flex justify-center items-center h-full">
                        <p class="text-gray-500">Conversation non trouvée</p>
                    </div>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>
