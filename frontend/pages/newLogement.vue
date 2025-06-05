<script setup lang="ts">
    import HomeInformation from '~/components/HomeInformation.vue';
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-32 px-4">
            <section id="information-home" class="mb-20">
                <h1 class="text-4xl font-semibold text-center mb-12">Ajouter un nouveau logement</h1>
            </section>
            <section>
                <HomeInformation />
            </section>
        </div>
        <UFooter />
    </main>
</template>
