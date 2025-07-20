<script setup lang="ts">
import LockIcon from '~/components/atoms/icons/LockIcon.vue'
import CheckCircleIcon from '~/components/atoms/icons/CheckCircleIcon.vue'

const authStore = useAuthStore()

const is2FAEnabled = computed(() => authStore.isTwoFactorEnabled)
</script>

<template>
  <div class="flex items-center space-x-3 p-4 rounded-lg border">
    <div class="flex-shrink-0">
      <CheckCircleIcon 
        v-if="is2FAEnabled" 
        class="w-6 h-6 text-green-600" 
      />
      <LockIcon 
        v-else 
        class="w-6 h-6 text-gray-400" 
      />
    </div>
    
    <div class="flex-1">
      <h4 class="text-sm font-medium text-gray-900 dark:text-white">
        Authentification à deux facteurs
      </h4>
      <p class="text-sm text-gray-600 dark:text-gray-400">
        <span v-if="is2FAEnabled" class="text-green-600">
          Activée - Votre compte est protégé
        </span>
        <span v-else class="text-gray-500">
          Désactivée - Recommandée pour la sécurité
        </span>
      </p>
    </div>
    
    <div class="flex-shrink-0">
      <UBadge 
        v-if="is2FAEnabled" 
        color="green" 
        variant="soft"
      >
        Activée
      </UBadge>
      <UBadge 
        v-else 
        color="yellow" 
        variant="soft"
      >
        Recommandée
      </UBadge>
    </div>
  </div>
</template>