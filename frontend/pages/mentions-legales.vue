<script setup lang="ts">
    useSeoMeta({
        title: 'Mentions légales - PopnBed',
        description:
            "Mentions légales de PopnBed - Site de réservation d'hébergements inspirés de films (projet d'école)",
    });
</script>

<template>
    <main class="w-full">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8 mb-10">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Mentions légales</h1>
                        <p class="mt-4">Informations légales relatives à PopnBed</p>
                    </div>
                </div>
            </section>

            <section class="mb-20">
                <div class="bg-white rounded-lg p-8 shadow-sm prose max-w-none">
                    <div class="space-y-8">
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-8">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        <strong>Important :</strong> PopnBed est un projet d'école développé à des fins
                                        pédagogiques uniquement. Il ne s'agit pas d'une véritable entreprise ou d'un
                                        service commercial.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">1. Informations générales</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="font-semibold mb-2">Nom du projet :</h3>
                                    <p class="text-gray-700">PopnBed</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2">Nature :</h3>
                                    <p class="text-gray-700">Projet étudiant / Plateforme fictive</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2">Type :</h3>
                                    <p class="text-gray-700">Application web éducative</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2">Objectif :</h3>
                                    <p class="text-gray-700">Démonstration pédagogique</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">2. Responsable du projet</h2>
                            <p class="text-gray-700 leading-relaxed">
                                Ce projet est développé dans le cadre d'une formation en développement web. Il s'agit
                                d'un exercice pédagogique visant à démontrer les compétences acquises en développement
                                d'applications web modernes.
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">3. Hébergement</h2>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-700">
                                    <strong>Hébergement :</strong> Environnement de développement local<br />
                                    <strong>Technologies utilisées :</strong> Vue.js, Nuxt.js, Symfony, Docker<br />
                                    <strong>Base de données :</strong> PostgreSQL
                                </p>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">4. Propriété intellectuelle</h2>
                            <p class="text-gray-700 leading-relaxed">
                                Le code source de ce projet est développé à des fins éducatives. Les éléments suivants
                                sont utilisés :
                            </p>
                            <ul class="list-disc pl-6 mt-2 text-gray-700">
                                <li>Frameworks et bibliothèques open source (Vue.js, Nuxt.js, Symfony)</li>
                                <li>Images et références cinématographiques à des fins pédagogiques uniquement</li>
                                <li>Design et interface développés dans le cadre du projet</li>
                                <li>Contenus fictifs créés pour la démonstration</li>
                            </ul>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">5. Limitation de responsabilité</h2>
                            <p class="text-gray-700 leading-relaxed">En tant que projet d'école, PopnBed :</p>
                            <ul class="list-disc pl-6 mt-2 text-gray-700">
                                <li>Ne constitue pas un service commercial réel</li>
                                <li>N'effectue aucune transaction financière réelle</li>
                                <li>Ne collecte pas de données personnelles réelles</li>
                                <li>N'offre aucun service d'hébergement réel</li>
                                <li>Est uniquement destiné à la démonstration et à l'évaluation académique</li>
                            </ul>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">6. Données et confidentialité</h2>
                            <p class="text-gray-700 leading-relaxed">Dans le cadre de ce projet éducatif :</p>
                            <ul class="list-disc pl-6 mt-2 text-gray-700">
                                <li>Seules des données fictives doivent être utilisées</li>
                                <li>Aucune donnée personnelle réelle ne doit être saisie</li>
                                <li>
                                    Le projet respecte les principes de protection des données à des fins pédagogiques
                                </li>
                                <li>Les données sont stockées localement dans l'environnement de développement</li>
                            </ul>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">7. Contact</h2>
                            <p class="text-gray-700 leading-relaxed">
                                Pour toute question relative à ce projet éducatif, vous pouvez utiliser le système de
                                tickets intégré à la plateforme ou contacter l'équipe pédagogique responsable.
                            </p>
                        </div>

                        <div>
                            <h2 class="text-h3 mb-4">8. Mentions techniques</h2>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-700 text-sm">
                                    <strong>Frontend :</strong> Vue.js 3, Nuxt.js, TypeScript, Tailwind CSS<br />
                                    <strong>Backend :</strong> Symfony 6, PHP 8+, Doctrine ORM<br />
                                    <strong>Base de données :</strong> PostgreSQL<br />
                                    <strong>Containerisation :</strong> Docker, Docker Compose<br />
                                    <strong>Serveur web :</strong> Nginx
                                </p>
                            </div>
                        </div>

                        <div class="border-t pt-6 mt-8">
                            <p class="text-sm text-gray-500 text-center">
                                Ces mentions légales sont spécifiquement adaptées pour un projet d'école et ne
                                constituent pas un document légal pour une entreprise réelle.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
