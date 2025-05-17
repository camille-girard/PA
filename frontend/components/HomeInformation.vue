<script setup lang="ts">
import { ref } from 'vue'
import UInput from '~/components/atoms/UInput.vue'
import UInputNumber from '~/components/atoms/UInputNumber.vue'

const bedrooms = ref(0)
const bathrooms = ref(0)
const capacity = ref(1)
const pricePerNight = ref(0)
const minStay = ref(1)
const maxStay = ref(7)
</script>

<template>
  <form class="space-y-16 max-w-4xl mx-auto">
    <!-- Informations logement -->
    <section>
      <h2 class="text-2xl font-bold mb-6">Informations du logement</h2>

      <UInput type="text" label="Titre de l'annonce *" placeholder="Titre de l'annonce" />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <UInput type="text" label="Thème *" placeholder="Thème" />
        <UInput type="text" label="Type de location *" placeholder="Type de location" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        <UInputNumber v-model="bedrooms" label="Chambres *" placeholder="Nombre de chambres" :min="0" class="w-full" />
        <UInputNumber v-model="bathrooms" label="Salles de bain" placeholder="Nombre de salles de bain" :min="0" class="w-full" />
        <UInputNumber v-model="capacity" label="Capacité *" placeholder="Nombre de personnes" :min="1" class="w-full" />
      </div>

      <div class="mt-4">
        <label for="description" class="text-secondary text-sm font-medium block mb-2">Description *</label>
        <textarea
            id="description"
            name="description"
            rows="6"
            maxlength="20000"
            placeholder="Décrivez votre logement"
            class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-solid focus:border-brand-solid placeholder:text-gray-400 text-gray-700 px-4 py-3 shadow-xs resize-none"
        ></textarea>
        <p class="text-sm text-tertiary mt-1">Maximum 20 000 caractères.</p>
      </div>

    </section>

    <!-- Adresse -->
    <section>
      <h2 class="text-2xl font-bold mb-6">Adresse</h2>

      <UInput type="text" label="Adresse complète *" placeholder="Adresse complète" />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <UInput type="text" label="Ville *" placeholder="Ville" />
        <UInput type="text" label="Code postal *" placeholder="Code postal" />
      </div>

      <div class="mt-4">
        <UInput type="text" label="Pays *" placeholder="Pays" />
      </div>
    </section>

    <!-- Photos -->
    <section>
      <h2 class="text-2xl font-bold mb-6">Ajouter vos photos</h2>
      <div class="w-full h-52 rounded-2xl bg-gray-100 flex items-center justify-center">
        <img src="/" alt="Placeholder" class="w-10 h-10 opacity-40" />
      </div>
    </section>

    <!-- Prix et dispo -->
    <section>
      <h2 class="text-2xl font-bold mb-6">Prix et disponibilité</h2>
      <UInputNumber v-model="pricePerNight" label="Prix par nuit *" placeholder="Prix par nuit" :min="1" class="w-full" />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <UInputNumber v-model="minStay" label="Séjour minimum" placeholder="Nombre de jours" :min="1" class="w-full" />
        <UInputNumber v-model="maxStay" label="Séjour maximum" placeholder="Nombre de jours" :min="1" class="w-full" />
      </div>
    </section>

    <!-- Submit -->
    <div class="flex justify-center pt-10">
      <button
          type="submit"
          class="bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-orange-700 transition w-full max-w-md"
      >
        Enregistrer et continuer
      </button>
    </div>
  </form>
</template>
