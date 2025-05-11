<script setup lang="ts">
    useSeoMeta({
        title: 'À propos de nous - PopnBed',
        meta: [
            {
                name: 'description',
                content:
                    "Découvrez PopnBed, la plateforme d'hébergement qui vous propose des séjours dans des lieux inspirés de vos films et séries préférés.",
            },
        ],
    });
</script>

<template>
    <main class="max-w-7xl mx-auto px-4 py-12 pt-24">
        <div class="relative p-8 rounded-xl shadow-lg mb-12 mx-auto">
            <h1 class="text-4xl font-bold text-primary mb-6">À propos de PopnBed</h1>

            <div class="space-y-6 text-lg">
                <p class="text-white mt-8">
                    Bienvenue chez PopnBed, la première plateforme qui transforme vos rêves de cinéphile en expériences
                    de séjour inoubliables.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">Notre concept</h2>
                <p class="text-white">
                    Chez PopnBed, nous croyons que les films et les séries ont le pouvoir de nous transporter dans des
                    univers extraordinaires. Notre mission est simple : vous permettre de vivre ces univers en réalité
                    en séjournant dans des hébergements inspirés de vos œuvres préférées.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">Notre sélection</h2>
                <p class="text-white">
                    De l'appartement new-yorkais de Friends au mythique château de Poudlard, en passant par la maison
                    colorée de Toy Story, nos hébergements sont soigneusement sélectionnés et aménagés pour vous offrir
                    une immersion totale dans vos univers favoris.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">Notre engagement</h2>
                <p class="text-white">
                    Nous nous engageons à vous offrir des expériences authentiques et de haute qualité. Chaque
                    hébergement est vérifié par notre équipe pour garantir confort et fidélité à l'univers représenté.
                </p>

                <div class="border-t border-gray-300/30 pt-6 mt-8">
                    <p class="italic text-white">
                        "Voyagez au-delà de l'écran avec PopnBed, là où la fiction devient réalité."
                    </p>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
    main {
        min-height: 80vh;
    }
</style>
