import { defineStore } from 'pinia';
import conversationService from '~/services/conversation.service';
import type { Conversation, Message } from '~/types/conversation';

export const useConversationStore = defineStore('conversation', {
    state: () => ({
        conversations: [] as Conversation[],
        currentConversation: null as Conversation | null,
        isLoading: false,
        error: null as string | null,
        unreadCount: 0,
    }),

    getters: {
        sortedConversations: (state) => {
            return [...state.conversations].sort(
                (a, b) => new Date(b.updatedAt).getTime() - new Date(a.updatedAt).getTime()
            );
        },
    },

    actions: {
        async fetchConversations() {
            this.isLoading = true;
            try {
                this.conversations = await conversationService.getConversations();
                this.unreadCount = this.conversations.filter((conv) => conv.hasNewMessages).length;
            } catch (error) {
                console.error('Error fetching conversations:', error);
                this.error = 'Impossible de récupérer les conversations';
            } finally {
                this.isLoading = false;
            }
        },

        async fetchConversation(id: number) {
            this.isLoading = true;
            try {
                const conversation = await conversationService.getConversation(id);
                this.currentConversation = conversation;

                // Update conversation in the list if it exists
                const index = this.conversations.findIndex((conv) => conv.id === id);
                if (index !== -1) {
                    this.conversations[index] = {
                        ...this.conversations[index],
                        hasNewMessages: false,
                    };

                    // Update unread count
                    this.unreadCount = this.conversations.filter((conv) => conv.hasNewMessages).length;
                }
            } catch (error) {
                console.error(`Error fetching conversation ${id}:`, error);
                this.error = 'Impossible de récupérer la conversation';
            } finally {
                this.isLoading = false;
            }
        },

        async createConversation(clientId: number, ownerId: number) {
            this.isLoading = true;
            try {
                const conversation = await conversationService.createConversation(clientId, ownerId);

                // Check if conversation already exists in our list
                const exists = this.conversations.some((conv) => conv.id === conversation.id);
                if (!exists) {
                    this.conversations.push(conversation);
                }

                return conversation;
            } catch (error) {
                console.error('Error creating conversation:', error);
                this.error = 'Impossible de créer la conversation';
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        async sendMessage(conversationId: number, content: string) {
            try {
                const message = await conversationService.sendMessage(conversationId, content);

                // Update current conversation if we're viewing it
                if (this.currentConversation && this.currentConversation.id === conversationId) {
                    if (!this.currentConversation.messages) {
                        this.currentConversation.messages = [];
                    }
                    this.currentConversation.lastMessagePreview =
                        content.length > 50 ? `${content.substring(0, 47)}...` : content;
                    this.currentConversation.updatedAt = message.createdAt;
                }

                // Update conversation in the list
                const index = this.conversations.findIndex((conv) => conv.id === conversationId);
                if (index !== -1) {
                    this.conversations[index] = {
                        ...this.conversations[index],
                        lastMessagePreview: content.length > 50 ? `${content.substring(0, 47)}...` : content,
                        updatedAt: message.createdAt,
                    };
                }

                return message;
            } catch (error) {
                console.error('Error sending message:', error);
                this.error = "Impossible d'envoyer le message";
                throw error;
            }
        },

        addMessage(message: Message) {
            console.log('addMessage called with:', message);

            // Si le message ne contient pas d'ID de conversation complet, essayons de le récupérer
            const conversationId =
                message.conversation?.id || (typeof message.conversation === 'number' ? message.conversation : null);

            if (!conversationId) {
                console.error('Cannot add message: no conversation ID found', message);
                return;
            }

            // Assurons-nous que l'objet message a toutes les propriétés nécessaires
            this.normalizeMessageObject(message);

            // If the conversation is the current one, add the message to it
            if (this.currentConversation && this.currentConversation.id === conversationId) {
                console.log('Adding message to current conversation');
                if (!this.currentConversation.messages) {
                    this.currentConversation.messages = [];
                }

                // Check if message already exists to avoid duplicates
                // if (!this.currentConversation.messages.some(m => m.id === message.id)) {
                // //   this.currentConversation.messages.push(message);
                // }
            } else {
                console.log(
                    'Message is for conversation',
                    conversationId,
                    'but current conversation is',
                    this.currentConversation ? this.currentConversation.id : 'none'
                );
            }

            // Update the conversation in the list
            const index = this.conversations.findIndex((conv) => conv.id === conversationId);
            console.log('Conversation index in list:', index);

            if (index !== -1) {
                // Only mark as having new messages if we're not currently viewing it
                const hasNewMessages = !this.currentConversation || this.currentConversation.id !== conversationId;

                console.log('Updating conversation in list, hasNewMessages:', hasNewMessages);
                this.conversations[index] = {
                    ...this.conversations[index],
                    lastMessagePreview:
                        message.content.length > 50 ? `${message.content.substring(0, 47)}...` : message.content,
                    updatedAt: message.createdAt,
                    hasNewMessages: hasNewMessages,
                };

                // Update unread count
                if (hasNewMessages) {
                    this.unreadCount = this.conversations.filter((conv) => conv.hasNewMessages).length;
                }
            } else {
                // La conversation n'existe pas encore dans notre liste
                // Essayons de la récupérer du serveur
                console.log('Conversation not in list, fetching from server');
                this.fetchConversation(conversationId).catch((err) =>
                    console.error('Error fetching conversation after message:', err)
                );
            }
        },

        normalizeMessageObject(message: Message) {
            // Vérifie si le message a un sender valide
            if (!message.sender || (typeof message.sender === 'object' && Object.keys(message.sender).length === 0)) {
                console.log('Message with empty sender, trying to reconstruct', message);

                // Essayons de déterminer l'expéditeur à partir du client ou du propriétaire
                if (
                    message.client &&
                    typeof message.client === 'object' &&
                    message.client.id &&
                    message.owner &&
                    typeof message.owner === 'object' &&
                    message.owner.id
                ) {
                    // Reconstruire un expéditeur valide (hypothèse: c'est le client)
                    message.sender = {
                        id: message.client.id,
                        email: message.client.email || '',
                        firstName: message.client.firstName || '',
                        lastName: message.client.lastName || '',
                        roles: ['ROLE_CLIENT'],
                    };
                }
            }

            // Si la conversation est juste un ID
            if (typeof message.conversation === 'number') {
                const conversationId = message.conversation;
                const existingConversation = this.conversations.find((c) => c.id === conversationId);

                if (existingConversation) {
                    message.conversation = { ...existingConversation };
                }
            }

            return message;
        },

        resetError() {
            this.error = null;
        },
    },
});
