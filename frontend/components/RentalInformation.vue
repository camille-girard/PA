<script setup>
const informations = [
  { icon: '/travelers.svg', text: '5 personnes max' },
  { icon: '/bed.svg', text: '2 chambres · 4 lits' },
  { icon: '/location.svg', text: 'Bretagne, France' },
  { icon: '/pet_friendly.svg', text: 'Animaux acceptés' },
  { icon: '/chimney.svg', text: 'Cheminée' },
  { icon: '/swimmingpool.svg', text: 'Piscine' },
]

const host = {
  name: 'Jean-François',
  details: 'Hôte depuis 3 ans',
  rating: 4.6,
  image: '/Patrick.jpg',
}
</script>

<template>
  <section id="rental-informations" class="space-y-10">
    <div>
      <h2 class="text-h2 mb-4">Logement entier : hébergement – Bretagne, France</h2>
      <p class="text-gray-700">
        Plongez dans l'univers marécageux et chaleureux de Shrek. <br />
        Cette cabane atypique vous propose un séjour coupé du monde, bercé par les chants des grenouilles et le crépitement du feu de bois...
      </p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
      <div
          v-for="(info, index) in informations"
          :key="index"
          class="flex items-center gap-3"
      >
        <img :src="info.icon" alt="Icone" class="w-8 h-8" />
        <span class="text-body-md text-gray-800 font-medium">{{ info.text }}</span>
      </div>
    </div>

    <div class="flex items-center gap-4 mt-8">
      <img :src="host.image" alt="Photo Hôte" class="w-12 h-12 rounded-full object-cover" />
      <div>
        <p class="text-body-md font-semibold text-gray-800">{{ host.name }} · <span class="font-normal text-sm text-gray-500">{{ host.details }}</span></p>
        <div class="flex items-center gap-1">
          <svg
              class="w-5 h-5 text-orange-500"
              fill="currentColor"
              viewBox="0 0 20 20"
          >
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.293c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.54-1.118l1.07-3.293a1 1 0 00-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <span class="text-body-sm text-gray-700">{{ host.rating }}</span>
        </div>
      </div>
    </div>
  </section>
</template>
