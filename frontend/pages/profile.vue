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
    <div class="max-w-7xl w-full mx-auto pt-32 px-4">
      <section id="information-profile" class="mb-20">
        <h1 class="text-4xl font-semibold text-center mb-12">Mes informations personnelles</h1>
        <PersonalInfoCard />
      </section>
      <section id="client-rentals" class="mb-20">
        <h2 class="text-center text-3xl font-bold mb-10">Consultés récemment</h2>
        <RentalCards :items="rental" />
      </section>
      <section id="owner-rentals" class="mb-20">
        <h2 class="text-center text-3xl font-bold mb-10">Mes biens publiés</h2>
        <RentalCards :items="rental" />
      </section>
    </div>
    <Footer />
  </main>
</template>
