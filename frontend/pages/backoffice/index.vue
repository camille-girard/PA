<script setup>
    import StatsCard from '~/components/StatsCard.vue';
    import UserIcon from '~/components/atoms/icons/UserIcon.vue';
    import BuildingIcon from '~/components/atoms/icons/BuildingIcon.vue';
    import CoinsHandIcon from '~/components/atoms/icons/CoinsHandIcon.vue';
    import AllIcons from '~/components/AllIcons.vue';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const stats = [
        { label: 'Clients', value: '5,423', icon: UserIcon },
        { label: 'Hôtes', value: '1,893', icon: BuildingIcon },
        { label: 'Réservations', value: '2,345', icon: CoinsHandIcon },
    ];
</script>

<template>
    <div>
        <p class="text-2xl font-semibold">Tableau de Board</p>
        <StatsCard :stats="stats" />
        <AllIcons />
    </div>
</template>
