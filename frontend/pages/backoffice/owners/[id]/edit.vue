<script setup lang="ts">
  import { useRoute } from 'vue-router'
  import Input from '~/components/atoms/UInput.vue'
  import { useRuntimeConfig } from '#app'
  import { useAuthFetch } from '~/composables/useAuthFetch'


  definePageMeta({
    layout: 'backoffice',
  })

  const route = useRoute()
  const id = route.params.id

  const { public: { apiUrl } } = useRuntimeConfig()

  const { data: owner, refresh, pending } = await useAuthFetch(`/api/owners/${id}`, {
    baseURL: apiUrl,
  })

  const form = reactive({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    isVerified: false,
  })

  watch(owner, (newOwner) => {
    if (newOwner) {
      form.firstName = newOwner.firstName
      form.lastName = newOwner.lastName
      form.email = newOwner.email
      form.phone = newOwner.phone
      form.isVerified = newOwner.isVerified
    }
  }, { immediate: true })

  const saving = ref(false)
  const success = ref(false)
  const errorMsg = ref('')

  async function save() {
    saving.value = true
    success.value = false
    errorMsg.value = ''

    try {
      await $fetch(`/api/owners/${id}`, {
        method: 'PUT',
        baseURL: apiUrl,
        body: {
          firstName: form.firstName,
          lastName: form.lastName,
          email: form.email,
          phone: form.phone,
          isVerified: form.isVerified,
        },
      })

      success.value = true
      await refresh()
    } catch (error: any) {
      errorMsg.value = error?.data?.message || 'Erreur lors de l’enregistrement.'
    } finally {
      saving.value = false
    }
  }
</script>

<template>
  <div class="max-w-3xl space-y-6">
    <h1 class="text-2xl font-semibold">Modifier l’hôte</h1>

    <form @submit.prevent="save" class="grid gap-6 pt-6 md:grid-cols-2" :aria-busy="saving || pending">
      <Input
          v-model="form.firstName"
          label="Prénom"
          name="firstName"
          type="text"
          required
      />
      <Input
          v-model="form.lastName"
          label="Nom"
          name="lastName"
          type="text"
          required
      />
      <Input
          class="md:col-span-2"
          v-model="form.email"
          label="Email"
          name="email"
          type="email"
          required
      />
      <Input
          class="md:col-span-2"
          v-model="form.phone"
          label="Téléphone"
          name="phone"
          type="tel"
      />

      <div class="md:col-span-2 flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
        <UButton
            :disabled="saving"
            :isLoading="saving"
            size="lg"
            variant="primary"
            type="submit"
        >
          {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
        </UButton>

        <span v-if="success" class="text-green-600 text-sm">Modifications enregistrées</span>
        <span v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</span>
      </div>
    </form>
  </div>
</template>
