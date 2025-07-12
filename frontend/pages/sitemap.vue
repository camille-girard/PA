<script setup lang="ts">
    useSeoMeta({
        title: 'Plan du site - PopnBed',
        description:
            "Plan du site PopnBed - Navigation et organisation du site de réservation d'hébergements inspirés de films",
    });
</script>

<template>
    <main class="w-full">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8 mb-10">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Plan du site</h1>
                        <p class="mt-4">Navigation et organisation du site</p>
                    </div>
                </div>
            </section>

            <section class="mb-20">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <h2 class="text-h3 mb-4 text-primary">Pages principales</h2>
                        <ul class="space-y-2">
                            <li><ULink variant="tertiary" to="/">Accueil</ULink></li>
                            <li><ULink variant="tertiary" to="/explorer">Explorer</ULink></li>
                            <li><ULink variant="tertiary" to="/tendances">Tendances</ULink></li>
                            <li><ULink variant="tertiary" to="/thematiques">Thématiques</ULink></li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <h2 class="text-h3 mb-4 text-primary">Compte utilisateur</h2>
                        <ul class="space-y-2">
                            <li><ULink variant="tertiary" to="/login">Connexion</ULink></li>
                            <li><ULink variant="tertiary" to="/register">Inscription</ULink></li>
                            <li><ULink variant="tertiary" to="/forgot-password">Mot de passe oublié</ULink></li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <h2 class="text-h3 mb-4 text-primary">Propriétaire</h2>
                        <ul class="space-y-2">
                            <li><ULink variant="tertiary" to="/owner-request">Devenir propriétaire</ULink></li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <h2 class="text-h3 mb-4 text-primary">Légal</h2>
                        <ul class="space-y-2">
                            <li><ULink variant="tertiary" to="/cgv">CGV</ULink></li>
                            <li><ULink variant="tertiary" to="/mentions-legales">Mentions légales</ULink></li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
