<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();

const form = ref({
  message: '',
});

const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

async function submitRequest() {
  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    const payload = { message: form.value.message };

    const { $api } = useNuxtApp();
    const { error } = await useFetch($api('/api/owner-request'), {
      method: 'POST',
      body: payload,
      credentials: 'include',
    });

    if (!error.value) {
      successMessage.value = 'Votre demande a été envoyée avec succès.';
      form.value.message = '';
    } else {
      console.error('API Error:', error.value);
      errorMessage.value =
          error.value?.data?.error || 'Une erreur est survenue lors de l’envoi de la demande.';
    }
  } catch (e) {
    console.error(e);
    errorMessage.value = 'Erreur réseau ou serveur.';
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <main class="w-full h-full">
    <UHeader />

    <div class="max-w-7xl w-full mx-auto pt-8 px-4">
      <section class="w-full pt-8">
        <div class="py-20 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-h1">Demande pour devenir propriétaire</h1>
            <p class="mt-4">
              Expliquez pourquoi vous souhaitez proposer un hébergement sur notre plateforme.
            </p>
          </div>
        </div>

        <form @submit.prevent="submitRequest" class="max-w-2xl mx-auto space-y-6 mt-12">
          <div>
            <label class="block mb-2">Parlez-nous de vous et de votre hébergement</label>
            <textarea
                v-model="form.message"
                class="w-full p-3 border border-gray-300 rounded-lg"
                rows="6"
                placeholder="Écrivez ici votre message"
            />
          </div>

          <UButton :loading="loading" type="submit" size="lg" variant="primary" class="w-full">
            Envoyer la demande
          </UButton>

          <div v-if="successMessage" class="text-green-600 text-center">{{ successMessage }}</div>
          <div v-if="errorMessage" class="text-red-600 text-center">{{ errorMessage }}</div>
        </form>
      </section>
    </div>

    <UFooter />
  </main>
</template>
