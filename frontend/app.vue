<script setup>
    useSeoMeta({
        title: 'PopnBed',
        description: "PopnBed - Un site de réservation d'hébergements inspirés de films",
        ogDescription: "PopnBed - Un site de réservation d'hébergements inspirés de films",
        meta: [
            {
                name: 'robots',
                content: 'index, follow',
            },
        ],
    });
</script>

<template>
    <NuxtLayout>
        <NuxtPage />
    </NuxtLayout>
</template>
