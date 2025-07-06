<script setup lang="ts">
    import { ref, onMounted, watch, nextTick, computed } from 'vue';
    import type { Conversation, Message } from '~/types/conversation';
    import { useAuthStore } from '~/stores/auth';
    import UButton from './atoms/UButton.vue';
import UInput from './atoms/UInput.vue';

    const props = defineProps<{
        conversation: Conversation;
    }>();

    const emit = defineEmits(['send']);

    const authStore = useAuthStore();
    const messageInput = ref('');
    const messagesContainer = ref<HTMLDivElement | null>(null);
    const isSending = ref(false);

    const sortedMessages = computed(() => {
        if (!props.conversation.messages) return [];
        return [...props.conversation.messages].sort(
            (a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime()
        );
    });

    const isCurrentUser = (message: Message) => {
        return message.sender.id === authStore.user?.id;
    };

    const formatTime = (dateString: string) => {
        const date = new Date(dateString);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return date.toLocaleDateString(undefined, {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    const shouldShowDate = (index: number, message: Message) => {
        if (index === 0) return true;

        const currentDate = new Date(message.createdAt);
        const previousMessage = sortedMessages.value[index - 1];
        const previousDate = new Date(previousMessage.createdAt);

        return currentDate.toDateString() !== previousDate.toDateString();
    };

    const sendMessage = async () => {
        if (!messageInput.value.trim()) return;

        isSending.value = true;

        try {
            await emit('send', messageInput.value);
            messageInput.value = '';
        } catch (error) {
            console.error('Error sending message:', error);
        } finally {
            isSending.value = false;
        }
    };

    const scrollToBottom = async () => {
        await nextTick();
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    };

    watch(() => props.conversation.messages, scrollToBottom, { deep: true });

    onMounted(scrollToBottom);
</script>

<template>
    <div class="flex flex-col h-full">
        <div ref="messagesContainer" class="flex-grow overflow-y-auto p-4">
            <div
                v-if="!conversation.messages || conversation.messages.length === 0"
                class="flex h-full items-center justify-center"
            >
                <p class="text-gray-500">Aucun message. Commencez la conversation!</p>
            </div>

            <template v-else>
                <div v-for="(message, index) in sortedMessages" :key="message.id" class="mb-4">
                    <div v-if="shouldShowDate(index, message)" class="flex justify-center my-4">
                        <div class="bg-gray-100 dark:bg-gray-800 px-4 py-1 rounded-full text-xs text-gray-500">
                            {{ formatDate(message.createdAt) }}
                        </div>
                    </div>

                    <div
                        :class="[
                            'max-w-[80%] rounded-lg px-4 py-2 mb-1',
                            isCurrentUser(message)
                                ? 'bg-brand-solid text-white ml-auto rounded-br-none'
                                : 'bg-gray-200 dark:bg-gray-700 dark:text-gray-100 mr-auto rounded-bl-none',
                        ]"
                    >
                        <p>{{ message.content }}</p>
                        <div
                            :class="[
                                'text-xs mt-1',
                                isCurrentUser(message) ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400',
                            ]"
                        >
                            {{ formatTime(message.createdAt) }}
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="border-t dark:border-gray-700 p-4">
            <form class="flex gap-2" @submit.prevent="sendMessage">
                <UInput
                    v-model="messageInput"
                    type="text"
                    class="w-full"
                    placeholder="Écrivez votre message..."
                    :disabled="isSending"
                />
                <UButton type="submit" :disabled="!messageInput.trim() || isSending">
                    <span v-if="isSending"> Envoi... </span>
                    <span v-else> Envoyer </span>
                </UButton>
            </form>
        </div>
    </div>
</template>

