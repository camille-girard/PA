<script setup lang="ts">
import Header from '~/components/Header.vue';
import Footer from '~/components/Footer.vue';
import SearchBar from '~/components/SearchBar.vue';
import RentalCards from '~/components/RentalCards.vue';


const trending = [
  { title: 'Friends', image: '/friends.png' },
  { title: 'Star Wars', image: '/StarWars.png' },
  { title: 'Le Seigneurs des anneaux', image: '/Seingeur_des_anneaux.png' },
  { title: 'Les Simpsons', image: '/simspons.png' },
  { title: 'Shrek', image: '/shrek.png' },
  { title: 'Harry Potter', image: '/Harry_Potter.png' },
];


</script>

<template>
  <main class="w-full h-full">
    <Header />
    <div class="max-w-7xl w-full mx-auto">
      <section class="w-full pt-32">
        <div class="py-20 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-4xl font-semibold">
              Séjourner dans un lieu inspiré de films
            </h1>
            <SearchBar />
          </div>
        </div>
      </section>
      <section class="w-full pt-12">
        <div class="text-center mb-10">
          <h2 class="text-2xl font-semibold">Les tendances du moment</h2>
        </div>
        <RentalCards :items="trending" />
        <div class="mt-10 text-center">
          <UButton class="mx-auto">Voir plus</UButton>
        </div>
      </section>
      <section class="w-full pt-12">
        <div class="text-center mb-10">
          <h2 class="text-2xl font-semibold">
            Parcourez un lieu unique digne d’un film ou d’une série culte
          </h2>
        </div>
        <div class="flex justify-center">
          <img
              src="/Map.png"
              alt="Carte des lieux à Paris"
              class="rounded-2xl max-w-full w-[800px]"
          />
        </div>
        <div class="mt-10 text-center">
          <UButton class="mx-auto">Explorez</UButton>
        </div>
      </section>
    </div>
    <Footer />
  </main>
</template>

