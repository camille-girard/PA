<script setup lang="ts">
    interface PracticalInfo {
        name: string;
        icon: string;
        text: string;
    }
    const props = defineProps<{
        practicalInfos?: string | PracticalInfo[];
    }>();
</script>

<template>
    <section id="informations" class="space-y-6">
        <h2 class="text-h2 mb-6">Informations pratiques</h2>
        <div>
            <p>{{ props.practicalInfos }}</p>
        </div>
    </section>
</template>
