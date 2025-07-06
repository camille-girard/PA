import { ref } from 'vue';
import { useConversationStore } from '~/stores/conversation';
import type { Message } from '~/types/conversation';

export function useMercure() {
  const isConnected = ref(false);
  const eventSources = ref<Record<string, EventSource>>({});
  const conversationStore = useConversationStore();
  const { $api } = useNuxtApp();
  const mercureHubUrl = ref('');

  const mercureToken = ref('');

  const fetchMercureInfo = async () => {
    try {
      const { data: tokenData } = await useAuthFetch<{ token: string, mercureHubUrl: string }>($api('/api/mercure-token'));
      
      if (tokenData.value?.token) {
        mercureToken.value = tokenData.value.token;
        mercureHubUrl.value = tokenData.value.mercureHubUrl;
        console.log('Using Mercure token and URL from API');
        return;
      }
      
      const { data } = await useAuthFetch<{ mercureHubUrl: string }>($api('/api/mercure-info'));
      if (data.value?.mercureHubUrl) {
        mercureHubUrl.value = data.value.mercureHubUrl;
      } else {
        mercureHubUrl.value = 'http://localhost:1337/.well-known/mercure';
        console.log('Using fallback Mercure URL:', mercureHubUrl.value);
      }
    } catch (error) {
      mercureHubUrl.value = 'http://localhost:1337/.well-known/mercure';
      console.log('Using fallback Mercure URL after error:', mercureHubUrl.value);
      console.error('Failed to fetch Mercure info:', error);
    }
  };

  /**
   * Subscribe to updates for a specific conversation
   */
  const subscribeToConversation = async (conversationId: number) => {
    const key = `conversation-${conversationId}`;
    
    if (eventSources.value[key]) {
      return;
    }

    if (!mercureHubUrl.value) {
      await fetchMercureInfo();
    }

    if (!mercureHubUrl.value) {
      console.error('Could not get Mercure hub URL');
      return;
    }

    try {
      const url = new URL(mercureHubUrl.value);
      url.searchParams.append('topic', `conversation/${conversationId}`);
      
      if (mercureToken.value) {
        url.searchParams.append('auth', mercureToken.value);
        console.log(`Using JWT token for conversation ${conversationId}`);
      }
      
      console.log(`Connecting to Mercure URL for conversation ${conversationId}:`, url.toString());
      
      const eventSource = new EventSource(url.toString(), { withCredentials: false });
      
      const connectionTimeout = setTimeout(() => {
        if (eventSource.readyState !== EventSource.OPEN) {
          console.error(`Connection to Mercure hub for conversation ${conversationId} is taking too long`);
          clearTimeout(connectionTimeout);
        }
      }, 10000);
      
      eventSource.onopen = () => {
        clearTimeout(connectionTimeout);
        console.log(`Connected to Mercure hub for conversation ${conversationId}`);
        isConnected.value = true;
      };
      
      eventSource.onmessage = (event) => {
        try {
          console.log(`Received message event for conversation ${conversationId}:`, event.data);
          const message = JSON.parse(event.data) as Message;
          console.log('Parsed message:', message);
          conversationStore.addMessage(message);
          console.log('Store after adding message:', conversationStore.conversations);
        } catch (error) {
          console.error('Error parsing message data:', error);
        }
      };
      
      eventSource.onerror = (error) => {
        console.error(`Mercure connection error for conversation ${conversationId}:`, error);
        eventSource.close();
        const updatedSources: Record<string, EventSource> = {};
        Object.entries(eventSources.value).forEach(([k, v]) => {
          if (k !== key) updatedSources[k] = v;
        });
        eventSources.value = updatedSources;
        isConnected.value = false;
        
        setTimeout(() => subscribeToConversation(conversationId), 10000);
      };
      
      eventSources.value[key] = eventSource;
      
    } catch (error) {
      console.error(`Error setting up Mercure for conversation ${conversationId}:`, error);
    }
  };

  /**
   * Subscribe to user messages notifications
   */
  const subscribeToUserMessages = async (userId: number) => {
    const key = `user-${userId}`;
    
    if (eventSources.value[key]) {
      return;
    }

    if (!mercureHubUrl.value) {
      await fetchMercureInfo();
    }

    if (!mercureHubUrl.value) {
      console.error('Could not get Mercure hub URL');
      return;
    }

    try {
      const url = new URL(mercureHubUrl.value);
      url.searchParams.append('topic', `user/${userId}/messages`);
      
      if (mercureToken.value) {
        url.searchParams.append('auth', mercureToken.value);
        console.log(`Using JWT token for user ${userId}`);
      }
      
      console.log(`Connecting to Mercure URL for user ${userId}:`, url.toString());
      const eventSource = new EventSource(url.toString(), { withCredentials: false });
      
      const connectionTimeout = setTimeout(() => {
        if (eventSource.readyState !== EventSource.OPEN) {
          console.error(`Connection to Mercure hub for user ${userId} is taking too long`);
          clearTimeout(connectionTimeout);
        }
      }, 10000);
      
      eventSource.onopen = () => {
        clearTimeout(connectionTimeout);
        console.log(`Connected to Mercure hub for user ${userId} messages`);
        isConnected.value = true;
      };
      
      eventSource.onmessage = (event) => {
        try {
          console.log(`Received user message event for user ${userId}:`, event.data);
          const message = JSON.parse(event.data) as Message;
          console.log('Parsed user message:', message);
          
          conversationStore.addMessage(message);
          console.log('Store after adding user message:', conversationStore.conversations);
          
          if (document.hidden) {
            if ('Notification' in window && Notification.permission === 'granted') {
              const notification = new Notification('Nouveau message', {
                body: message.content.length > 50 
                  ? `${message.content.substring(0, 47)}...` 
                  : message.content,
              });
              
              notification.onclick = () => {
                window.focus();
                const conversationId = typeof message.conversation === 'object' 
                  ? message.conversation.id 
                  : message.conversation;
                navigateTo(`/messages/${conversationId}`);
              };
            } else if ('Notification' in window && Notification.permission !== 'denied') {
              Notification.requestPermission();
            }
          }
        } catch (error) {
          console.error('Error parsing message data:', error);
        }
      };
      
      eventSource.onerror = (error) => {
        console.error(`Mercure connection error for user ${userId}:`, error);
        eventSource.close();
        const updatedSources: Record<string, EventSource> = {};
        Object.entries(eventSources.value).forEach(([k, v]) => {
          if (k !== key) updatedSources[k] = v;
        });
        eventSources.value = updatedSources;
        isConnected.value = false;
        
        setTimeout(() => subscribeToUserMessages(userId), 10000);
      };
      
      eventSources.value[key] = eventSource;
      
    } catch (error) {
      console.error(`Error setting up Mercure for user ${userId}:`, error);
    }
  };

  /**
   * Unsubscribe from a conversation
   */
  const unsubscribeFromConversation = (conversationId: number) => {
    const key = `conversation-${conversationId}`;
    
    if (eventSources.value[key]) {
      eventSources.value[key].close();
      const updatedSources: Record<string, EventSource> = {};
      Object.entries(eventSources.value).forEach(([k, v]) => {
        if (k !== key) updatedSources[k] = v;
      });
      eventSources.value = updatedSources;
    }
  };

  /**
   * Unsubscribe from all topics
   */
  const unsubscribeAll = () => {
    Object.values(eventSources.value).forEach(es => es.close());
    eventSources.value = {};
    isConnected.value = false;
  };

  onBeforeUnmount(unsubscribeAll);

  fetchMercureInfo();

  return {
    isConnected,
    subscribeToConversation,
    subscribeToUserMessages,
    unsubscribeFromConversation,
    unsubscribeAll
  };
}
