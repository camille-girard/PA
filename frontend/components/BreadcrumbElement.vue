<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import ChevronRightIcon from '~/components/atoms/icons/ChevronRightIcon.vue';

const route = useRoute();

const breadcrumbTranslations: Record<string, string> = {
    'backoffice': 'Tableau de bord',
    'owners': 'Hôtes',
    'clients': 'Clients',
    'admins': 'Administrateurs',
    'bookings': 'Réservations',
    'themes': 'Thèmes',
    'accommodations': 'Logements',
    'tickets': 'Tickets',
    'owner-requests': 'Demandes propriétaires',
    'profile': 'Profil',
    'login': 'Connexion',
    'register': 'Inscription',
    'settings': 'Paramètres',
    'about': 'À propos',
    'edit': 'Modifier',
    'create': 'Créer',
    'delete': 'Supprimer',
};

function formatSegment(segment: string) {
    return breadcrumbTranslations[segment]
        || segment.replace(/-/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

const crumbs = computed(() => {
    const segments = route.path.split('/').filter(Boolean);
    let accumulated = '';
    return segments.map((segment) => {
        accumulated += '/' + segment;
        return {
            label: formatSegment(segment),
            path: accumulated,
        };
    });
});
</script>


<template>
    <nav aria-label="breadcrumb" class="text-sm text-gray-500">
        <ol class="flex flex-wrap items-center">
            <li class="flex items-center">
                <ULink to="/" variant="tertiary"> PopnBed </ULink>
            </li>

            <li v-for="(crumb, index) in crumbs" :key="index" class="flex items-center">
                <ChevronRightIcon class="w-4 h-4 mx-2 text-gray-400" />

                <ULink v-if="index < crumbs.length - 1" :to="crumb.path" variant="tertiary">
                    {{ crumb.label }}
                </ULink>
                <span v-else class="text-gray-400">
                    {{ crumb.label }}
                </span>
            </li>
        </ol>
    </nav>
</template>
