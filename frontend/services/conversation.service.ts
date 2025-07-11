import type { Conversation, Message } from '~/types/conversation';

class ConversationService {
    private baseUrl = '/api/conversations';

    async getConversations(): Promise<Conversation[]> {
        const { $api } = useNuxtApp();
        const { data } = await useAuthFetch<{ conversations: Conversation[] }>($api(this.baseUrl));
        return data.value?.conversations || [];
    }

    async getConversation(id: number): Promise<Conversation> {
        const { $api } = useNuxtApp();
        const { data } = await useAuthFetch<{ conversation: Conversation }>($api(`${this.baseUrl}/${id}`));
        return data.value?.conversation as Conversation;
    }

    async createConversation(clientId: number, ownerId: number): Promise<Conversation> {
        const { $api } = useNuxtApp();
        const { data } = await useAuthFetch<{ conversation: Conversation }>($api(this.baseUrl), {
            method: 'POST',
            body: { clientId, ownerId },
        });
        return data.value?.conversation as Conversation;
    }

    async sendMessage(conversationId: number, content: string): Promise<Message> {
        const { $api } = useNuxtApp();
        const { data } = await useAuthFetch<{ message: Message }>($api(`${this.baseUrl}/${conversationId}/messages`), {
            method: 'POST',
            body: { content },
        });
        return data.value?.message as Message;
    }
}

export default new ConversationService();
