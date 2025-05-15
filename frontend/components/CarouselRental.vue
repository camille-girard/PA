<script setup>
const images = [
  { id: 1, src: '/shrek.png', alt: 'Maison principale' },
  { id: 2, src: '/Shrek_livingroom.png', alt: 'Salon' },
  { id: 3, src: '/Shrek_bedroom.png', alt: 'Chambre' },
  { id: 4, src: '/Shrek_bedroom_2.png', alt: 'Chambre 2' },
  { id: 5, src: '/Shrek_livingroom_2.png', alt: 'Salle à manger' },
  { id: 6, src: '/Shrek_kitchen.png', alt: 'Cuisine' },
  { id: 7, src: '/Shrek_outdoor.png', alt: 'Extérieur' },
  { id: 8, src: '/Shrek_outdoor.png', alt: 'Extérieur' }
]
</script>

<template>
  <section class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-4xl font-bold mb-12 text-center">Maison dans l’univers de Shrek 500m2</h1>

    <div class="flex flex-col md:flex-row gap-8 w-full mx-auto">
      <div class="flex-1">
        <img
            :src="images[0].src"
            :alt="images[0].alt"
            class="w-full h-[500px] object-cover rounded-3xl shadow-lg hover:scale-105 transition-all duration-300"
        />
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 flex-1">
        <div
            v-for="(image, index) in images.slice(1)"
            :key="index"
            class="relative group overflow-hidden rounded-2xl shadow-md hover:shadow-lg transition-all duration-300"
        >
          <img
              :src="image.src"
              :alt="image.alt"
              class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300"
          />
          <!-- Overlay -->
          <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition duration-300 rounded-2xl"></div>
        </div>
      </div>
    </div>
  </section>
</template>
