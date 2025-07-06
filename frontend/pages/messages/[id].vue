<script setup lang="ts">
import { useConversationStore } from '~/stores/conversation';
import { useAuthStore } from '~/stores/auth';
import { useMercure } from '~/composables/useMercure';

definePageMeta({
  middleware: ['auth']
});

const route = useRoute();
const conversationStore = useConversationStore();
const { subscribeToConversation, unsubscribeFromConversation } = useMercure();
const isLoading = ref(true);
const conversationId = Number(route.params.id);
const debugMessages = ref<string[]>([]);

// Function pour journaliser les messages de debug
function addDebugMessage(message: string) {
  console.log(`DEBUG [${new Date().toISOString()}]: ${message}`);
  debugMessages.value.push(`${new Date().toISOString()}: ${message}`);
  // Garder seulement les 10 derniers messages
  if (debugMessages.value.length > 10) {
    debugMessages.value.shift();
  }
}

// Get the partner name (owner if user is client, client if user is owner)
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
    addDebugMessage(`Fetching conversation ${conversationId}`);
    await conversationStore.fetchConversation(conversationId);
    addDebugMessage('Conversation fetched successfully');
    
    // Subscribe to this conversation's updates
    addDebugMessage(`Subscribing to updates for conversation ${conversationId}`);
    await subscribeToConversation(conversationId);
    addDebugMessage('Subscription to conversation updates complete');
    
    // Get user info to log
    const authStore = useAuthStore();
    const userRole = authStore.user?.roles.includes('ROLE_CLIENT') ? 'CLIENT' : 'OWNER';
    addDebugMessage(`Current user role: ${userRole}, ID: ${authStore.user?.id}`);
  } catch (error) {
    console.error(`Error loading conversation ${conversationId}:`, error);
    addDebugMessage(`Error: ${error instanceof Error ? error.message : String(error)}`);
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

const refreshMercureConnection = async () => {
  addDebugMessage('Refreshing Mercure connection...');
  unsubscribeFromConversation(conversationId);
  addDebugMessage('Unsubscribed from conversation');
  
  setTimeout(async () => {
    await subscribeToConversation(conversationId);
    addDebugMessage('Resubscribed to conversation');
  }, 1000);
};
</script>

<template>
  <NuxtLayout name="default">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center">
          <NuxtLink to="/messages" class="text-blue-500 mr-4">
            <span class="inline-block transform rotate-180">&rarr;</span> Retour
          </NuxtLink>
          <h1 class="text-2xl font-bold">{{ getPartnerName() }}</h1>
        </div>
      </div>
      
      <!-- Main Content -->
      <div class="border rounded-lg overflow-hidden bg-white dark:bg-gray-900 shadow h-[70vh]">
        <div v-if="isLoading" class="flex justify-center items-center h-full">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>
        
        <template v-else-if="conversationStore.currentConversation">
          <MessagesList 
            :conversation="conversationStore.currentConversation" 
            @send="handleSendMessage"
          />
        </template>
        
        <div v-else class="flex justify-center items-center h-full">
          <p class="text-gray-500">Conversation non trouvée</p>
        </div>
      </div>
      
      <!-- Debug panel -->
      <div class="mt-4 p-3 border rounded-lg bg-gray-100 dark:bg-gray-800">
        <details>
          <summary class="cursor-pointer font-semibold text-blue-600">Informations de débogage</summary>
          <div class="mt-2">
            <button class="mb-3 px-3 py-1 bg-blue-500 text-white rounded-md text-sm" @click="refreshMercureConnection">
              Rafraîchir la connexion Mercure
            </button>
            <h3 class="font-semibold">Messages de debug:</h3>
            <ul class="list-disc pl-5 text-sm">
              <li v-for="(msg, index) in debugMessages" :key="index" class="text-gray-700 dark:text-gray-300">
                {{ msg }}
              </li>
            </ul>
          </div>
        </details>
      </div>
    </div>
  </NuxtLayout>
</template>
