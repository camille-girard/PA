<script setup>
    const comment = [
        {
            id: 1,
            name: 'Cyril',
            userDetail: 'PopnBeder depuis 3 ans',
            comment: 'Super séjour, Jean-François nous mets aux petits soins et la maison est incroyable !',
            userImage: 'https://via.placeholder.com/150',
            rating: 5,
        },
        {
            id: 2,
            name: 'Sophie',
            userDetail: 'PopnBeder depuis 5 ans',
            comment:
                'Un séjour parmis les orgres plus qu’agréable. Mes enfants été aux anges. La maison est fidèle au dessin...',
            userImage: 'https://via.placeholder.com/150',
            rating: 5,
        },
    ];

    const rental = [
        { title: 'Game of Thrones', image: '/Game_of_thrones.jpg' },
        { title: 'Star Wars', image: '/StarWars.png' },
    ];
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl mx-auto w-full pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center relative">
                    <div class="text-center z-10">
                        <h1 class="text-h1">Les informations du propriétaire</h1>
                    </div>
                </div>
            </section>
            <div class="flex flex-col md:flex-row gap-10">
                <aside class="md:w-1/3 space-y-8">
                    <OwnerInformationCard />
                </aside>
                <section class="md:w-2/3 space-y-16">
                    <section id="owner-information" class="space-y-6 pb-10 border-b border-gray-300">
                        <h2 class="text-h2 mb-6">Informations sur Patrick</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                            <div class="flex items-center gap-3">
                                <img src="/work.svg" alt="Profession" class="w-8 h-8" />
                                <span class="text-body-md">Profession : Gérant de Maple Hill Retreats</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="/world.svg" alt="Langues parlées" class="w-8 h-8" />
                                <span class="text-body-md">Langues parlées : Anglais</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="/location.svg" alt="Localisation" class="w-8 h-8" />
                                <span class="text-body-md">Je vis ici : Elsworth, Royaume-Uni</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="" alt="Hébergement" class="w-8 h-8" />
                                <span class="text-body-md">Hébergement professionnel</span>
                            </div>
                        </div>
                        <div class="text-gray-800 text-body-md leading-relaxed mt-6">
                            Je vis avec ma femme, Caroline, et nos deux enfants, Jasper (16 ans) et Emilia (13 ans).
                            Nous sommes tombés amoureux de cette région paisible entourée de forêts et de collines
                            douces. Le Cambridgeshire regorge de petits trésors : chemins de randonnée, marchés fermiers
                            et jolis pubs campagnards. Nous adorons les activités en plein air comme le jardinage, les
                            balades à vélo, le kayak et les soirées feu de camp.
                        </div>
                    </section>
                    <section id="owner-comment" class="space-y-6 pb-10 border-b border-gray-300">
                        <h2 class="text-h2 mb-6">Commentaires pour Patrick</h2>
                        <CommentCards :items="comment" />
                        <a href="#" class="underline font-semibold mt-6 inline-block">Afficher les commentaires</a>
                    </section>
                    <section id="owner-rentals" class="space-y-6">
                        <h2 class="text-h2 mb-10">Annonces publiées par Patrick</h2>
                        <RentalCards :items="rental" />
                    </section>
                </section>
            </div>
        </div>
        <UFooter />
    </main>
</template>

