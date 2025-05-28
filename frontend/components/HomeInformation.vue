<script setup lang="ts">
import { ref } from 'vue'
import UInput from '~/components/atoms/UInput.vue'
import UInputNumber from '~/components/atoms/UInputNumber.vue'
import { nanoid } from 'nanoid'

const bedrooms = ref(0)
const bathrooms = ref(0)
const capacity = ref(1)
const pricePerNight = ref(0)
const minStay = ref(1)
const maxStay = ref(7)

const images = ref<{ id: string; url: string }[]>([])
const fileInputRef = ref<HTMLInputElement | null>(null)

function handleImageUpload(event: Event) {
  const files = (event.target as HTMLInputElement).files
  if (!files) return

  const remainingSlots = 10 - images.value.length
  const selectedFiles = Array.from(files).slice(0, remainingSlots)

  selectedFiles.forEach(file => {
    const reader = new FileReader()
    reader.onload = () => {
      images.value.push({
        id: nanoid(),
        url: reader.result as string,
      })
    }
    reader.readAsDataURL(file)
  })

  // Reset input to allow re-uploading same file
  if (fileInputRef.value) {
    fileInputRef.value.value = ''
  }
}

function removeImage(id: string) {
  images.value = images.value.filter(img => img.id !== id)
}
</script>

<template>
  <form class="space-y-16 max-w-4xl mx-auto">
    <!-- Informations du logement -->
    <section>
      <h2 class="text-h2 font-bold mb-6">Informations du logement</h2>
      <UInput type="text" label="Titre de l'annonce *" placeholder="Titre de l'annonce" />
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <UInput type="text" label="Thème *" placeholder="Thème" />
        <UInput type="text" label="Type de location *" placeholder="Type de location" />
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        <UInputNumber v-model="bedrooms" label="Chambres *" :min="0" class="w-full" />
        <UInputNumber v-model="bathrooms" label="Salles de bain" :min="0" class="w-full" />
        <UInputNumber v-model="capacity" label="Capacité *" :min="1" class="w-full" />
      </div>
      <div class="mt-4">
        <label for="description" class="text-body-sm font-medium block mb-2">Description *</label>
        <textarea
            id="description"
            name="description"
            rows="6"
            maxlength="20000"
            placeholder="Décrivez votre logement"
            class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-400 text-gray-700 px-4 py-3 shadow-xs resize-none"
        ></textarea>
        <p class="text-body-sm mt-1">Maximum 20 000 caractères.</p>
      </div>
    </section>

    <!-- Adresse -->
    <section>
      <h2 class="text-h2 mb-6">Adresse</h2>
      <UInput type="text" label="Adresse complète *" placeholder="Adresse complète" />
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <UInput type="text" label="Ville *" placeholder="Ville" />
        <UInput type="text" label="Code postal *" placeholder="Code postal" />
      </div>
      <div class="mt-4">
        <UInput type="text" label="Pays *" placeholder="Pays" />
      </div>
    </section>

    <!-- Ajout de photos -->
    <section>
      <h2 class="text-h2 mb-6">Ajouter vos photos</h2>
      <input
          type="file"
          ref="fileInputRef"
          class="hidden"
          accept="image/*"
          multiple
          @change="handleImageUpload"
      />

      <div
          @click="fileInputRef?.click()"
          class="w-full h-52 rounded-2xl bg-gray-100 flex items-center justify-center relative cursor-pointer hover:bg-gray-200 transition"
      >
        <img src="/icon.svg" alt="Ajouter" class="w-10 h-10 opacity-40" />
        <p class="ml-2 text-sm text-gray-400">Cliquez pour importer jusqu'à 10 images</p>
      </div>

      <!-- Affichage des images -->
      <div v-if="images.length" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
        <div v-for="img in images" :key="img.id" class="relative group">
          <img :src="img.url" class="w-full h-40 object-cover rounded-xl" />
          <button
              type="button"
              @click="removeImage(img.id)"
              class="absolute top-2 right-2 bg-black/60 text-white w-6 h-6 flex items-center justify-center rounded-full text-sm opacity-0 group-hover:opacity-100 transition"
          >
            ×
          </button>
        </div>
      </div>
    </section>

    <!-- Prix et disponibilité -->
    <section>
      <h2 class="text-h2 mb-6">Prix et disponibilité</h2>
      <UInputNumber v-model="pricePerNight" label="Prix par nuit *" :min="1" class="w-full" />
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <UInputNumber v-model="minStay" label="Séjour minimum" :min="1" class="w-full" />
        <UInputNumber v-model="maxStay" label="Séjour maximum" :min="1" class="w-full" />
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
