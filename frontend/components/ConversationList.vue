<script setup lang="ts">
import type { Conversation } from '~/types/conversation';
import { useAuthStore } from '~/stores/auth';

const _props = defineProps<{
  conversations: Conversation[];
  selectedId?: number;
}>();

const emit = defineEmits(['select']);

const authStore = useAuthStore();

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  // Si c'est aujourd'hui, montrer l'heure
  if (date >= today) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }
  
  // Si c'est hier, montrer "Hier"
  if (date >= yesterday && date < today) {
    return 'Hier';
  }
  
  // Sinon, montrer la date
  return date.toLocaleDateString();
};

const getPartnerName = (conversation: Conversation) => {
  const isClient = authStore.user?.roles.includes('ROLE_CLIENT');
  
  if (isClient) {
    return `${conversation.owner.firstName} ${conversation.owner.lastName}`;
  } else {
    return `${conversation.client.firstName} ${conversation.client.lastName}`;
  }
};

const getAvatar = (conversation: Conversation) => {
  const isClient = authStore.user?.roles.includes('ROLE_CLIENT');
  
  if (isClient) {
    return conversation.owner.avatar || '/images/default-avatar.png';
  } else {
    return conversation.client.avatar || '/images/default-avatar.png';
  }
};
</script>

<template>
  <div class="conversation-list">
    <div v-if="conversations.length === 0" class="no-conversations">
      <p class="text-gray-500 text-center p-4">Aucune conversation</p>
    </div>

    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
      <li 
        v-for="conversation in conversations" 
        :key="conversation.id"
        :class="[
          'p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors', 
          { 'bg-blue-50 dark:bg-blue-900/20': conversation.id === selectedId }
        ]"
        @click="emit('select', conversation.id)"
      >
        <div class="flex items-center">
          <div class="relative">
            <img 
              :src="getAvatar(conversation)" 
              :alt="getPartnerName(conversation)" 
              class="w-12 h-12 rounded-full object-cover" 
            />
            <div 
              v-if="conversation.hasNewMessages" 
              class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white dark:border-gray-800"
            ></div>
          </div>
          
          <div class="ml-4 flex-grow">
            <div class="flex justify-between items-center">
              <h3 class="text-sm font-semibold">{{ getPartnerName(conversation) }}</h3>
              <span class="text-xs text-gray-500">{{ formatDate(conversation.updatedAt) }}</span>
            </div>
            <p 
              class="text-sm text-gray-600 dark:text-gray-300 truncate"
              :class="{ 'font-semibold text-black dark:text-white': conversation.hasNewMessages }"
            >
              {{ conversation.lastMessagePreview || 'Aucun message' }}
            </p>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<style scoped>
.conversation-list {
  height: 100%;
  overflow-y: auto;
}
</style>
