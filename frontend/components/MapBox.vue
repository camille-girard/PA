<script setup lang="ts">
import { ref, onMounted } from 'vue'
import mapboxgl from 'mapbox-gl'
import 'mapbox-gl/dist/mapbox-gl.css'

const mapContainer = ref<HTMLDivElement | null>(null)
const accommodations = ref<any[]>([])

const loadMap = async () => {
  if (process.client && mapContainer.value) {
    const config = useRuntimeConfig()
    mapboxgl.accessToken = config.public.mapboxToken

    try {
      const response = await fetch(`${config.public.apiUrl}/api/accommodations`)
      const raw = JSON.parse(JSON.stringify(await response.json()))

      const rawData = Array.isArray(raw) ? raw : raw.accommodations || []

      const safeData = rawData.map(acc => {
        const clone = { ...acc }
        if (typeof clone.hasOwnProperty !== 'undefined') {
          delete clone.hasOwnProperty
        }
        return clone
      })

      accommodations.value = safeData

      const map = new mapboxgl.Map({
        container: mapContainer.value,
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [2.2137, 46.2276],
        zoom: 5,
      })

      map.on('load', () => map.resize())

      accommodations.value.forEach(acc => {
        const lat = parseFloat(acc.latitude)
        const lng = parseFloat(acc.longitude)
        const image = acc.image || acc.images?.[0]?.url || 'https://via.placeholder.com/300x200?text=Photo'

        if (!isNaN(lat) && !isNaN(lng)) {
          const popupContent = `
            <a href="/accommodation/${acc.id}" target="_self" class="relative block bg-white rounded-xl overflow-hidden max-w-xs shadow-xl text-sm no-underline hover:opacity-90 transition">
              <button class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl z-10 close-popup" type="button"
                onclick="event.stopPropagation(); event.preventDefault(); this.closest('.mapboxgl-popup').remove();">
                &times;
              </button>
              <img src="${image}" alt="${acc.name}" class="w-full h-36 object-cover" />
              <div class="p-4">
                <div class="font-semibold text-gray-800 text-base">${acc.name}</div>
                <div class="text-sm text-gray-500">${acc.price} €/nuit</div>
              </div>
            </a>
          `

          const popup = new mapboxgl.Popup({
            offset: 25,
            closeButton: false,
          }).setHTML(popupContent)

          new mapboxgl.Marker({ color: '#e04f16' })
              .setLngLat([lng, lat])
              .setPopup(popup)
              .addTo(map)
        } else {
          console.warn('Coordonnées invalides pour', acc.name, acc)
        }
      })
    } catch (err) {
      console.error('Erreur chargement carte/données:', err)
    }
  }
}

onMounted(loadMap)
</script>

<template>
  <div class="w-full h-[500px] rounded-2xl overflow-hidden shadow-lg">
    <div ref="mapContainer" class="w-full h-full" />
  </div>
</template>

<style>
.mapboxgl-popup-content {
  background: transparent;
  border-radius: 0;
  box-shadow: none;
  padding: 0;
  pointer-events: auto;
}
</style>
