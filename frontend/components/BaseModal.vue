<script setup lang="ts">
    defineProps<{
        modelValue: boolean;
        title?: string;
    }>();

    defineEmits(['update:modelValue']);
</script>

<template>
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-lg p-6 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ title }}</h2>
                <button class="text-gray-400 hover:text-gray-600" @click="$emit('update:modelValue', false)">✖</button>
            </div>
            <div>
                <slot />
            </div>
            <div class="text-right">
                <button class="text-sm text-blue-600 hover:underline" @click="$emit('update:modelValue', false)">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</template>
