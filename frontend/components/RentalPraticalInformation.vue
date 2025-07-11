<script setup lang="ts">
    interface PracticalInfo {
        name: string;
        icon: string;
        text: string;
    }
    const props = defineProps<{
        practicalInfos?: string | PracticalInfo[];
    }>();
    const practicalInfos = computed(() => {
        if (!props.practicalInfos) return [];
        if (typeof props.practicalInfos === 'string') {
            return props.practicalInfos.split(',').map((info) => info.trim());
        }
        return props.practicalInfos;
    });
</script>

<template>
    <section id="informations" class="space-y-6">
        <h2 class="text-h2 mb-6">Informations pratiques</h2>
        <div v-if="practicalInfos.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="(info, index) in practicalInfos"
                :key="index"
                class="flex items-center gap-4 p-4 border rounded-lg shadow-sm"
            >
                <div>
                    <h3 class="text-body-md font-semibold">{{ info }}</h3>
                </div>
            </div>
        </div>
    </section>
</template>
