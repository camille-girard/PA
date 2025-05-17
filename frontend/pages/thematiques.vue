<script setup lang="ts">
import Footer from "~/components/Footer.vue";
import Header from "~/components/Header.vue";
import thematicGrid from '~/components/RentalCards.vue';
import SearchBar from "~/components/SearchBar.vue";

interface Logement {
  title: string;
  image: string;
}

interface ThemeSection {
  title: string;
  items: Logement[];
  slug: string;
}

const sections: ThemeSection[] = [
  {
    title: 'Science-fiction',
    slug: 'science-fiction',
    items: [
      { title: 'Appartement Star Wars', image: '/StarWars.png' },
      { title: 'Tiny House Alien', image: '/Home_Alien.png'},
      { title: 'Maison Sunshine', image: '/Home_Sunshine.png' },
    ],
  },
  {
    title: 'Séries TV',
    slug: 'series-tv',
    items: [
      { title: 'Stranger Things House', image: '/Home_ST.png' },
      { title: 'Maison Breaking Bad Jesse Pinkman', image: '/Home_BB.png' },
      { title: 'Friends Apartment', image: '/friends.png' },
    ],
  },
  {
    title: 'Dessin animé',
    slug: 'dessin-anime',
    items: [
      { title: 'Maison La-Haut', image: '/Home_La_Haut.png' },
      { title: 'Maisonnette Ghibli', image: '/Home_Ghibli.png' },
      { title: 'Cabane Bob l’éponge', image: '/Home_Bob.png' },
    ],
  },
  {
    title: 'Fantastique',
    slug: 'fantastique',
    items: [
      { title: 'Maison Harry Potter', image: '/Home_HP.jpeg' },
      { title: 'Maison Hobbit', image: '/Home_hobbit.png' },
      { title: 'Villa Barbie', image: '/Home_Barbie.png' },
    ],
  },
  {
    title: 'Halloween',
    slug: 'Halloween',
    items: [
      { title: 'Maison Twilight', image: '/Home_Twilight.png' },
      { title: 'Villa Scream', image: '/Home_Scream.png' },
      { title: 'Cabane Hurlante', image: '/Home_Cabane_hurlante.png' },
    ],
  },
];
</script>

<template>
  <main>
    <Header />
    <div class="max-w-7xl w-full mx-auto">
      <section class="w-full pt-32">
        <div class="py-20 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-4xl font-semibold">Explorez nos thématiques</h1>
            <p class="mt-4">Trouvez le lieu parfait inspiré de vos films et séries préférés.</p>
            <SearchBar />
          </div>
        </div>
      </section>
        <section v-for="section in sections" :key="section.title" class="w-full pt-12">
          <div class="text-center mb-10">
            <h2 class="text-2xl font-semibold">{{ section.title }}</h2>
          </div>
          <thematicGrid :items="section.items" />
          <div class="flex justify-end hover:underline mt-5">
            <NuxtLink :to="`/recherche?theme=${section.slug}`" class="inline-flex items-center text-orange-600 text-sm font-medium">Voir plus
              <svg
                  class="w-4 h-4 ml-1"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </NuxtLink>
          </div>
        </section>
    </div>
    <Footer />
  </main>
</template>


