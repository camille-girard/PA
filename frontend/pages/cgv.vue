<script setup lang="ts">
    useSeoMeta({
        title: 'Conditions Générales de Vente - PopnBed',
        meta: [
            {
                name: 'description',
                content:
                    "Consultez les conditions générales de vente de PopnBed, la plateforme d'hébergement inspirée de vos films et séries préférés.",
            },
        ],
    });
</script>

<template>
    <main class="max-w-7xl mx-auto px-4 py-12 pt-24">
        <div class="relative p-8 rounded-xl shadow-lg mb-12 mx-auto">
            <h1 class="text-4xl font-bold text-primary mb-6">Conditions Générales de Vente</h1>

            <div class="space-y-6 text-lg">
                <p class="text-white mt-8">
                    Les présentes conditions générales de vente régissent l'utilisation de la plateforme PopnBed et la
                    réservation des hébergements proposés.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">1. Objet et champ d'application</h2>
                <p class="text-white">
                    Les présentes conditions générales de vente s'appliquent à toutes les réservations effectuées sur
                    PopnBed, plateforme de mise en relation entre propriétaires d'hébergements thématiques et voyageurs.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">2. Modalités de réservation</h2>
                <p class="text-white">
                    La réservation d'un hébergement sur PopnBed nécessite la création préalable d'un compte utilisateur.
                    Toute réservation est considérée comme ferme et définitive dès validation du paiement et réception
                    de la confirmation par email.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">3. Tarification et modalités de paiement</h2>
                <p class="text-white">
                    Les prix affichés sont indiqués en euros, toutes taxes comprises. Le paiement s'effectue en ligne
                    par carte bancaire ou tout autre moyen proposé sur la plateforme. Une commission de service de 10%
                    est appliquée sur chaque réservation.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">4. Conditions d'annulation</h2>
                <p class="text-white">
                    Les conditions d'annulation varient selon les hébergements et sont clairement indiquées lors de la
                    réservation. En règle générale, toute annulation effectuée moins de 48 heures avant l'arrivée prévue
                    entraîne des frais correspondant à la première nuit.
                </p>

                <h2 class="text-2xl font-semibold text-primary mt-8">5. Responsabilités</h2>
                <p class="text-white">
                    PopnBed agit en qualité d'intermédiaire entre les propriétaires et les voyageurs. Notre
                    responsabilité se limite à la mise en relation et ne saurait être engagée en cas de litige entre les
                    parties ou de non-conformité de l'hébergement.
                </p>

                <div class="border-t border-gray-300/30 pt-6 mt-8">
                    <p class="italic text-white">
                        Ces conditions générales de vente ont été mises à jour le 1er avril 2025. PopnBed se réserve le
                        droit de les modifier à tout moment.
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
