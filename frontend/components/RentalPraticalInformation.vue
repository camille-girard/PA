<script setup>
    const practicalInfos = [
        {
            icon: '/hour.svg',
            title: 'Horaires',
            description: 'Check-in à 12:00 · Check-out à 12:00',
        },
        {
            icon: '/road.svg',
            title: "Comment s'y rendre ?",
            description: 'Le Marais, Bretagne, France',
        },
        {
            icon: 'shop.svg',
            title: 'Premier commerce',
            description: 'À 5 km',
        },
    ];
</script>

<template>
    <section id="informations" class="space-y-6">
        <h2 class="text-h2 mb-6">Informations pratiques</h2>

        <ul class="space-y-6">
            <li v-for="(info, index) in practicalInfos" :key="index" class="flex items-start gap-4">
                <img :src="info.icon" alt="Icon" class="w-8 h-8 mt-1" />

                <div>
                    <h3 class="text-h6 text-gray-800">{{ info.title }}</h3>
                    <p class="text-body-sm text-gray-500">{{ info.description }}</p>
                </div>
            </li>
        </ul>
    </section>
</template>
