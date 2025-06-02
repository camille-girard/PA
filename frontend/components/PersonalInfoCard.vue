<script setup lang="ts">
import UButton from '~/components/atoms/UButton.vue'
import EditableField from '@/components/EditableField.vue'
import { useAuthStore } from '@/stores/auth'
import { onMounted } from 'vue'

const auth = useAuthStore()

onMounted(() => {
  if (!auth.user) {
    auth.fetchUser()
  }
})

function saveField(field: string, value: string) {
  auth.updateUser({ [field]: value })
}

function handleDelete() {
  const confirmDelete = confirm('Es-tu sûr de vouloir supprimer ton compte ? Cette action est irréversible.')
  if (!confirmDelete) return

  auth.deleteAccount().then((res) => {
    if (res.success) {
      navigateTo('/login')
    } else {
      alert(res.error)
    }
  })
}
</script>


<template>
  <div class="max-w-5xl mx-auto p-6 flex flex-col md:flex-row gap-10 items-start">
    <div class="bg-orange-100 rounded-3xl p-6 flex flex-col items-center w-full md:w-1/4">
      <img
          src="/Patrick.jpg"
          alt="Avatar"
          class="w-24 h-24 rounded-full object-cover mb-4"
      />
      <p class="text-body-lg font-bold">{{ auth.user?.firstName }}</p>
    </div>

    <div class="w-full md:w-3/4 space-y-6">
      <div class="space-y-4" v-if="auth.user">
        <EditableField
            label="Nom officiel"
            :modelValue="auth.user.lastName"
            field="lastName"
            @save="saveField"
        />

        <EditableField
            label="Prénom"
            :modelValue="auth.user.firstName"
            field="firstName"
            @save="saveField"
        />

        <EditableField
            label="Adresse e-mail"
            :modelValue="auth.user.email"
            field="email"
            @save="saveField"
        />

        <EditableField
            label="Numéro de téléphone"
            :modelValue="auth.user.phone ?? ''"
            field="phone"
            @save="saveField"
        />

        <EditableField
            label="Adresse"
            :modelValue="auth.user.address ?? ''"
            field="address"
            @save="saveField"
        />
      </div>
    </div>
  </div>

  <div class="flex flex-col items-center mt-10 gap-2">
    <UButton
        size="lg"
        variant="primary"
        class="w-full max-w-2xl"
    >
      Ajouter un nouveau bien
    </UButton>

    <UButton
        size="sm"
        variant="outline"
        class="text-red-600 border-orange-300 hover:bg-orange-50 hover:text-red-700"
        @click="handleDelete"
    >
      Supprimer mon compte
    </UButton>
  </div>

</template>
