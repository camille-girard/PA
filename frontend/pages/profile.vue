<script setup>
    const rental = [
        { title: 'Friends', image: '/friends.png' },
        { title: 'Star Wars', image: '/StarWars.png' },
        { title: 'Le Seigneur des anneaux', image: '/Seingeur_des_anneaux.png' },
    ];
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section id="information-profile" class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center relative">
                    <div class="text-center z-10">
                        <h1 class="text-h1">Mes informations personnelles</h1>
                    </div>
                </div>
                <PersonalInfoCard />
            </section>
            <section id="client-rentals" class="mb-20 mt-20">
                <h2 class="text-center text-h2 mb-10">Consultés récemment</h2>
                <RentalCards :items="rental" />
            </section>
            <section id="owner-rentals" class="mb-20">
                <h2 class="text-center text-h2 mb-10">Mes biens publiés</h2>
                <RentalCards :items="rental" />
            </section>
        </div>
        <UFooter />
    </main>
</template>

