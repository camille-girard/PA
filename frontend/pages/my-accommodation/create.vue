<script setup lang="ts">
import { ref } from 'vue'
import UHeader from '~/components/UHeader.vue'
import UFooter from '~/components/UFooter.vue'
import HomeInformation from '~/components/HomeInformation.vue'
import { useAuthStore } from '~/stores/auth'

const auth = useAuthStore()

const newAccommodationId = ref(null)
</script>

<template>
  <main>
    <UHeader />
    <div class="max-w-7xl w-full mx-auto pt-8 px-4">
      <section class="w-full pt-8">
        <div class="py-20 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-h1">Ajouter un nouvel hébergement</h1>
          </div>
        </div>
      </section>

      <section id="information-home">
        <HomeInformation />
      </section>
    </div>
    <UFooter />
  </main>
</template>
