<script setup lang="ts">
import { useConversationStore } from '~/stores/conversation';
import { useAuthStore } from '~/stores/auth';
import { useMercure } from '~/composables/useMercure';

definePageMeta({
  middleware: ['auth']
});

const conversationStore = useConversationStore();
const authStore = useAuthStore();
const { subscribeToUserMessages } = useMercure();
const isLoading = ref(true);
const showNewConversationModal = ref(false);

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

const handleConversationCreated = (conversation: { id: number }) => {
  navigateTo(`/messages/${conversation.id}`);
};
</script>

<template>
  <NuxtLayout name="default">
    <div class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Messages</h1>
        
        <button 
          class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
          @click="showNewConversationModal = true"
        >
          Nouvelle conversation
        </button>
      </div>
      
      <div v-if="isLoading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
      
      <div v-else-if="conversationStore.conversations.length === 0" class="text-center p-8 bg-white dark:bg-gray-900 rounded-lg shadow">
        <h2 class="text-xl mb-4">Aucune conversation</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Vous n'avez pas encore de conversations. Contactez un propriétaire ou un client pour commencer à échanger.
        </p>
        
        <button 
          class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
          @click="showNewConversationModal = true"
        >
          Démarrer une conversation
        </button>
      </div>
      
      <div v-else class="border rounded-lg shadow overflow-hidden bg-white dark:bg-gray-900">
        <ConversationList 
          :conversations="conversationStore.sortedConversations"
          @select="(id: number) => navigateTo(`/messages/${id}`)"
        />
      </div>
    </div>
    
    <NewConversationModal
      v-model:show="showNewConversationModal"
      @created="handleConversationCreated"
    />
  </NuxtLayout>
</template>