<script setup>
import UButton from '~/components/atoms/UButton.vue'
</script>
<template>
  <div class="max-w-5xl mx-auto p-6 flex flex-col md:flex-row gap-10 items-start">
    <div class="bg-orange-100 rounded-3xl p-6 flex flex-col items-center w-full md:w-1/4">
      <img
          src="/Patrick.jpg"
          alt="Avatar"
          class="w-24 h-24 rounded-full object-cover mb-4"
      />
      <p class="text-body-lg font-bold">Patrick</p>
    </div>

    <div class="w-full md:w-3/4 space-y-6">
      <div class="space-y-4">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-body-md font-bold">Nom officiel</p>
            <p>Patrick Steven</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">modifier</a>
        </div>
        <div class="flex justify-between items-start">
          <div>
            <p class="text-body-md font-bold">Prénom d'usage</p>
            <p>le grand gaillard</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">modifier</a>
        </div>
        <div class="flex justify-between items-start">
          <div>
            <p class="text-body-md font-bold">Adresse e-mail</p>
            <p>patrick.steven@gmail.com</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">modifier</a>
        </div>
        <div class="flex justify-between items-start">
          <div>
            <p class="text-body-md font-bold">Numéro de téléphone</p>
            <p class="text-body-md text-gray-500">non fourni</p>
          </div>
          <a href="#" class="text-body-sm underline text-gray-700">ajouter</a>
        </div>
        <div class="flex justify-between items-start">
          <div>
            <p class="text-body-md font-bold">Vérification d'identité</p>
            <p class="text-body-md text-gray-500">non vérifié</p>
          </div>
          <a href="#" class="text-body-sm underline text-gray-700">vérifier</a>
        </div>
        <div class="flex justify-between items-start">
          <div>
            <p class="text-body-md font-bold">Adresse</p>
            <p class="text-body-md text-gray-500">non fournie</p>
          </div>
          <a href="#" class="text-body-sm underline text-gray-700">ajouter</a>
        </div>
      </div>
    </div>
  </div>

  <div class="flex justify-center mt-10">
    <UButton
        size="lg"
        variant="primary"
        class="w-full max-w-2xl"
    >
      Ajouter un nouveau bien
    </UButton>
  </div>

</template>
