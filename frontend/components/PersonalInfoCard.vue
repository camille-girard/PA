<script setup lang="ts">
</script>
<template>
  <div class="max-w-5xl mx-auto p-6 flex flex-col md:flex-row gap-10 items-start">
    <div class="bg-orange-100 rounded-3xl p-6 flex flex-col items-center w-full md:w-1/4">
      <img
          src="https://via.placeholder.com/100"
          alt="Avatar"
          class="w-24 h-24 rounded-full mb-4 object-cover"
      />
      <p class="text-lg font-bold">Patrick</p>
    </div>

    <div class="w-full md:w-3/4 space-y-6">
      <div class="space-y-4">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold">Nom officiel</p>
            <p>Patrick Steven</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">modifier</a>
        </div>

        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold">Prénom d'usage</p>
            <p>le grand gaillard</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">modifier</a>
        </div>

        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold">Adresse e-mail</p>
            <p>patrick.steven@gmail.com</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">modifier</a>
        </div>

        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold">Numéro de téléphone</p>
            <p class="text-gray-500">non fourni</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">ajouter</a>
        </div>

        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold">Vérification d'identité</p>
            <p class="text-gray-500">non vérifié</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">vérifier</a>
        </div>

        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold">Adresse</p>
            <p class="text-gray-500">non fournie</p>
          </div>
          <a href="#" class="text-sm underline text-gray-700">ajouter</a>
        </div>
      </div>

      <div class="pt-8">
        <button
            class="bg-orange-600 text-white font-semibold px-6 py-3 rounded-xl w-full hover:bg-orange-700 transition"
        >
          Ajouter un nouveau bien
        </button>
      </div>
    </div>
  </div>
</template>
