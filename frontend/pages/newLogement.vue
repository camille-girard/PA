<script setup>
import Footer from "~/components/Footer.vue";
import Header from "~/components/Header.vue";
import HomeInformation from "~/components/HomeInformation.vue";
</script>

<template>
  <main>
    <Header />
    <div class="max-w-7xl w-full mx-auto pt-8 px-4">
      <section class="w-full pt-8">
        <div class="py-20 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-h1">Ajouter un nouveau logement</h1>
          </div>
        </div>
      </section>
      <section id="information-home">
        <HomeInformation />
      </section>
    </div>
    <Footer />
  </main>
</template>
