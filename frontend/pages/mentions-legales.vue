<script setup lang="ts">
    useSeoMeta({
        title: 'Mentions Légales - PopnBed',
        meta: [
            {
                name: 'description',
                content:
                    "Consultez les mentions légales de PopnBed, la plateforme d'hébergement inspirée de vos films et séries préférés.",
            },
        ],
    });
</script>

<template>
    <main class="max-w-7xl mx-auto px-4 py-12 pt-24">
        <div class="relative p-8 rounded-xl shadow-lg mb-12 mx-auto">
            <h1 class="text-4xl font-bold text-primary mb-6">Mentions Légales</h1>

            <div class="space-y-6 text-lg">
                <p class="text-white mt-8">
                    Conformément aux dispositions des articles 6-III et 19 de la Loi n° 2004-575 du 21 juin 2004 pour la
                    Confiance dans l'économie numérique, nous vous informons des éléments suivants :
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">1. Informations légales</h2>
                <p class="text-white">
                    PopnBed est une plateforme éditée par la société PopnBed SAS, au capital social de 10 000€,
                    immatriculée au RCS de Paris sous le numéro 123 456 789, dont le siège social est situé au 123
                    Avenue des Cinéphiles, 75002 Paris.
                </p>
                <p class="text-white mt-2">Numéro de TVA intracommunautaire : FR12 123 456 789</p>

                <h2 class="text-2xl font-semibold text-primary mt-8">2. Direction de la publication</h2>
                <p class="text-white">Directeur de la publication : Marie Dupont, Présidente</p>
                <p class="text-white mt-2">Contact : contact@popnbed.com</p>

                <h2 class="text-2xl font-semibold text-primary mt-8">3. Hébergement</h2>
                <p class="text-white">
                    Le site PopnBed est hébergé par OVH SAS, société au capital de 10 174 560€, immatriculée au RCS de
                    Lille sous le numéro 424 761 419, dont le siège social est situé 2 rue Kellermann, 59100 Roubaix,
                    France.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">4. Propriété intellectuelle</h2>
                <p class="text-white">
                    L'ensemble du contenu du site PopnBed (structure, textes, logos, images, design) est protégé par le
                    droit d'auteur et demeure la propriété exclusive de PopnBed SAS ou de ses partenaires. Toute
                    reproduction, représentation ou diffusion, en tout ou partie, du contenu de ce site sur quelque
                    support que ce soit est interdite sans l'autorisation préalable de PopnBed SAS.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">5. Protection des données personnelles</h2>
                <p class="text-white">
                    Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et
                    Libertés, vous disposez d'un droit d'accès, de rectification, d'opposition et de suppression des
                    données vous concernant. Pour exercer ces droits, veuillez nous contacter à l'adresse :
                    privacy@popnbed.com.
                </p>

                <div class="border-t border-gray-300/30 pt-6 mt-8">
                    <p class="italic text-white">Ces mentions légales ont été mises à jour le 1er avril 2025.</p>
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
