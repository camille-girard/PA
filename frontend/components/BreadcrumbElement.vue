<script setup lang="ts">
    import { computed } from 'vue';
    import { useRoute } from 'vue-router';
    import ChevronRightIcon from '~/components/atoms/icons/ChevronRightIcon.vue';

    const route = useRoute();

    function formatSegment(segment: string) {
        return segment.replace(/-/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
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
