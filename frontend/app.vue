<script setup lang="ts">
    useSeoMeta({
        title: 'PopnBed',
        description: "PopnBed - Un site de réservation d'hébergements inspirés de films",
        ogDescription: "PopnBed - Un site de réservation d'hébergements inspirés de films",
        robots: 'index, follow',
    });
    if (import.meta.client && window._paq) {
        window._paq.push(['trackPageView']);
    }
</script>
<template>
    <NuxtLayout>
        <NuxtPage />
    </NuxtLayout>
</template>
