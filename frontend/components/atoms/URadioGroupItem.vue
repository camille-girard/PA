<script setup lang="ts">
    interface RadioGroupItemProps {
        label: string;
        value: string;
        description: string;
        icon?: Component;
        groupValue?: string;
        disabled?: boolean;
    }

    const props = defineProps<RadioGroupItemProps>();

    const emit = defineEmits<{
        'update:groupValue': [value: string];
    }>();

    const isSelected = computed(() => props.groupValue === props.value);

    function handleChange() {
        if (!props.disabled) {
            emit('update:groupValue', props.value);
        }
    }
</script>

<template>
    <div
        class="border border-secondary bg-primary rounded-xl p-4 relative cursor-pointer w-full focus:outline-none focus:ring-2 focus:ring-primary flex gap-3 pr-8 self-stretch"
        @click="handleChange"
    >
        <div v-if="icon" class="border border-primary bg-primary p-2 rounded-md shadow-xs h-fit w-fit">
            <component :is="icon" class="h-4 w-4 text-fg-secondary" />
        </div>
        <div class="flex-grow">
            <p class="font-medium text-secondary text-sm">
                {{ label }}
            </p>
            <p v-if="description" class="text-sm text-tertiary">
                {{ description }}
            </p>
        </div>
        <div class="absolute top-4 right-4">
            <UCheckbox v-model="isSelected" name="" />
        </div>
    </div>
</template>
