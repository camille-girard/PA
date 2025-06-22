<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const isLoading = ref(false);

const route = useRoute();

const title = route.query.title as string;
const arrival = new Date(route.query.arrival as string);
const departure = new Date(route.query.departure as string);
const guests = Number(route.query.guests);
const price = Number(route.query.price);
const nights = Number(route.query.nights);
const pricePerNight = Number(route.query.pricePerNight);

const formattedArrival = arrival.toLocaleDateString('fr-FR', {
  day: 'numeric',
  month: 'long',
  year: 'numeric',
});
const formattedDeparture = departure.toLocaleDateString('fr-FR', {
  day: 'numeric',
  month: 'long',
  year: 'numeric',
});

const totalPrice = (pricePerNight * nights + 12 + 5).toFixed(2);

const createCheckout = async () => {
  try {
    isLoading.value = true;

    const res = await $fetch('/api/create-checkout-session', {
      method: 'POST',
      body: {
        title,
        amount: Number(totalPrice),
      },
    });

    if (res?.url) {
      window.location.href = res.url;
    } else {
      alert("Erreur : URL de paiement non reçue.");
    }
  } catch (e) {
    console.error('Stripe Checkout error:', e);
    alert("Une erreur est survenue pendant le paiement.");
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <main>
    <UHeader />
    <div class="max-w-7xl w-full mx-auto pt-8 px-4">
      <section id="booking-request" class="w-full pt-8">
        <div class="py-16 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-h1">Demande de réservation</h1>
          </div>
        </div>

        <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-lg p-8 space-y-8 border border-gray-200">
          <!-- Info logement -->
          <div class="space-y-2 text-center">
            <h2 class="text-2xl font-bold text-gray-900">{{ title }}</h2>
            <div class="text-md text-gray-700">
              <p>
                Du <span class="font-semibold text-gray-900">{{ formattedArrival }}</span>
                au <span class="font-semibold text-gray-900">{{ formattedDeparture }}</span>
              </p>
              <p>{{ guests }} personne{{ guests > 1 ? 's' : '' }}</p>
            </div>
          </div>

          <!-- Détail du prix -->
          <div class="border-t pt-6 space-y-4">
            <h3 class="text-xl font-bold text-gray-900">Détail du prix</h3>
            <ul class="text-gray-800 space-y-2">
              <li class="flex justify-between">
                <span>{{ pricePerNight.toFixed(2) }} € × {{ nights }} nuit{{ nights > 1 ? 's' : '' }}</span>
                <strong>{{ (pricePerNight * nights).toFixed(2) }} €</strong>
              </li>
              <li class="flex justify-between">
                <span>Frais de service :</span>
                <strong>12,00 €</strong>
              </li>
              <li class="flex justify-between">
                <span>Taxes :</span>
                <strong>5,00 €</strong>
              </li>
            </ul>
            <div class="border-t pt-4 flex justify-between text-lg font-bold">
              <span>Total :</span>
              <span>{{ totalPrice }} €</span>
            </div>
          </div>

          <!-- Bouton -->
          <div class="pt-6 flex justify-center">
            <UButton
                size="lg"
                variant="primary"
                :disabled="isLoading"
                @click="createCheckout"
            >
              {{ isLoading ? 'Chargement...' : 'Passer au paiement' }}
            </UButton>
          </div>
        </div>
      </section>
    </div>
    <UFooter />
  </main>
</template>
