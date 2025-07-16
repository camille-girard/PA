<script setup lang="ts">
    definePageMeta({
        middleware: 'owner',
        layout: 'default',
    });
</script>

<template>
    <main class="w-full">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Ajouter un nouvel hébergement</h1>
                    </div>
                </div>
            </section>

            <section id="information-home">
                <HomeInformation />
            </section>
        </div>
        <UFooter />
    </main>
</template>
