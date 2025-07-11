<script setup lang="ts">
    definePageMeta({
        middleware: 'owner',
        layout: 'default',
    });

    const route = useRoute();
    const accommodationId = route.params.id as string;
</script>

<template>
    <main class="w-full">
        <UHeader />

        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Modifier mon logement</h1>
                    </div>
                </div>
            </section>

            <section id="information-home">
                <HomeInformation :accommodation-id="accommodationId" :is-edit="true" />
            </section>
        </div>

        <UFooter />
    </main>
</template>
