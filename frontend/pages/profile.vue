<script setup>
import Footer from "~/components/Footer.vue";
import Header from "~/components/Header.vue";
import RentalCards from "~/components/RentalCards.vue";
import PersonalInfoCard from '~/components/PersonalInfoCard.vue'

const rental = [
  { title: 'Friends', image: '/friends.png' },
  { title: 'Star Wars', image: '/StarWars.png' },
  { title: 'Le Seigneur des anneaux', image: '/Seingeur_des_anneaux.png' },
];
</script>

<template>
  <main>
    <Header />
    <div class="max-w-7xl mx-auto w-full pt-4 px-4">
      <section id="information-profile" class="w-full pt-32">
        <h2 class="text-3xl font-bold mb-10">Mes informations personelles</h2>
        <PersonalInfoCard />

      </section>
      <section id="client-rentals" class="w-full pt-32">
        <h2 class="text-3xl font-bold mb-10">Consultés récemment</h2>
        <RentalCards :items="rental" />
      </section>
      <section id="owner-rentals" class="space-y-6">
        <h2 class="text-3xl font-bold mb-10">Mes biens publiés</h2>
        <RentalCards :items="rental" />
      </section>
    </div>
    <Footer />
  </main>
</template>

