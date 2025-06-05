<script setup lang="ts">
    import '~/types/logement';

    const trending: Logement[] = [
        { title: 'Maison Harry Potter', image: '/Home_HP.jpeg' },
        { title: 'Appartement Star Wars', image: '/StarWars.png' },
        { title: 'Friends Apartment', image: '/friends.png' },
        { title: 'Tiny House Alien', image: '/Home_Alien.png' },
        { title: 'Maison Hobbit', image: '/Home_hobbit.png' },
        { title: 'Maison La-Haut', image: '/Home_La_Haut.png' },
    ];
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center relative">
                    <div class="text-center z-10">
                        <h1 class="text-h1">Tendances du moment</h1>
                        <p class="text-body-md mt-4">Les logements les plus populaires cette semaine</p>
                    </div>
                </div>
            </section>
            <section class="w-full pt-12">
                <div class="text-center mb-10">
                    <h2 class="text-h2">Les coups de coeur du moment</h2>
                </div>
                <RentalCards :items="trending" />
                <div class="flex justify-end hover:underline mt-5">
                    <NuxtLink to="#" class="inline-flex items-center text-orange-600 text-sm font-medium">
                        Voir plus
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
        <UFooter />
    </main>
</template>
