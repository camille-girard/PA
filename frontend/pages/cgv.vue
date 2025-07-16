<script setup lang="ts">
    useSeoMeta({
        title: 'Conditions Générales de Vente - PopnBed',
        description:
            "Conditions générales de vente et d'utilisation de PopnBed - Site de réservation d'hébergements inspirés de films",
    });
</script>

<template>
    <main class="w-full">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8 mb-10">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Conditions Générales de Vente</h1>
                        <p class="mt-4">Conditions d'utilisation de PopnBed</p>
                    </div>
                </div>
            </section>

            <section class="mb-20">
                <div class="bg-white rounded-lg p-8 shadow-sm prose max-w-none">
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-h3 mb-4">Article 1 - Objet</h2>
                            <p class="text-gray-700 leading-relaxed">
                                Les présentes conditions générales de vente (CGV) régissent les relations contractuelles
                                entre PopnBed, plateforme de réservation d'hébergements à thème cinématographique
                                (projet d'école), et ses utilisateurs. En utilisant notre service, vous acceptez sans
                                réserve ces conditions.
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">Article 2 - Nature du service</h2>
                            <p class="text-gray-700 leading-relaxed">
                                PopnBed est une plateforme fictive développée dans le cadre d'un projet éducatif. Elle
                                permet la mise en relation entre propriétaires d'hébergements et clients souhaitant
                                réserver des logements inspirés d'univers cinématographiques.
                                <strong
                                    >Ce service est uniquement à des fins pédagogiques et ne constitue pas une véritable
                                    plateforme de réservation.</strong
                                >
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">Article 3 - Inscription</h2>
                            <p class="text-gray-700 leading-relaxed">
                                L'inscription sur PopnBed est gratuite et ouverte à toute personne majeure.
                                L'utilisateur s'engage à fournir des informations exactes et à mettre à jour son profil.
                                Un seul compte par personne est autorisé.
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">Article 4 - Réservations</h2>
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Important :</strong> Les réservations effectuées sur cette plateforme sont
                                fictives et n'engagent aucune transaction réelle. Aucun paiement réel ne sera effectué
                                et aucun hébergement réel ne sera fourni. Cette fonctionnalité est uniquement à des fins
                                de démonstration pédagogique.
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">Article 5 - Responsabilités</h2>
                            <p class="text-gray-700 leading-relaxed">
                                PopnBed étant un projet d'école, nous déclinons toute responsabilité concernant :
                            </p>
                            <ul class="list-disc pl-6 mt-2 text-gray-700">
                                <li>La véracité des informations publiées par les utilisateurs</li>
                                <li>Les interactions entre utilisateurs</li>
                                <li>La disponibilité continue du service</li>
                                <li>La sécurité des données (utiliser des données fictives uniquement)</li>
                            </ul>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">Article 6 - Propriété intellectuelle</h2>
                            <p class="text-gray-700 leading-relaxed">
                                Les éléments de design, le code source et la structure de PopnBed sont protégés par les
                                droits d'auteur. Les images et références cinématographiques utilisées le sont à des
                                fins pédagogiques uniquement.
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">Article 7 - Données personnelles</h2>
                            <p class="text-gray-700 leading-relaxed">
                                Dans le cadre de ce projet éducatif, nous recommandons fortement l'utilisation de
                                données fictives uniquement. Les données collectées ne sont traitées qu'à des fins
                                pédagogiques et de démonstration.
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">Article 8 - Modification des CGV</h2>
                            <p class="text-gray-700 leading-relaxed">
                                Ces conditions peuvent être modifiées à tout moment dans le cadre de l'évolution du
                                projet éducatif. Les utilisateurs seront informés des modifications par notification sur
                                la plateforme.
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">Article 9 - Contact</h2>
                            <p class="text-gray-700 leading-relaxed">
                                Pour toute question concernant ces conditions générales de vente, vous pouvez nous
                                contacter via le système de tickets intégré à la plateforme.
                            </p>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-8">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Avertissement :</strong> PopnBed est un projet d'école à des fins
                                        pédagogiques uniquement. Aucune transaction réelle n'est effectuée sur cette
                                        plateforme.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
