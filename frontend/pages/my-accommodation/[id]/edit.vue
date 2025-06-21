<script setup lang="ts">
const route = useRoute()

// On récupère l'ID de l'hébergement depuis l'URL
const accommodationId = route.params.id as string
</script>

<template>
  <main>
    <UHeader />

    <div class="max-w-7xl w-full mx-auto pt-8 px-4">
      <section class="w-full pt-8">
        <div class="py-20 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-h1">Modifier mon logement</h1>
          </div>
        </div>
      </section>

      <section id="information-home">
        <HomeInformation :accommodation-id="accommodationId" :is-edit="true" />
      </section>
    </div>

    <UFooter />
  </main>
</template>
