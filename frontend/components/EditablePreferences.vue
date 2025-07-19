<script setup lang="ts">
    import { AccommodationAdvantage, accommodationAdvantageLabels } from '~/types/accommodationAdvantage';

    interface Props {
        label: string;
        modelValue: AccommodationAdvantage[];
    }

    const props = defineProps<Props>();
    const emit = defineEmits<{
        save: [value: AccommodationAdvantage[]];
    }>();

    const isEditing = ref(false);
    const localValue = ref<AccommodationAdvantage[]>([...props.modelValue]);

    // Liste de tous les avantages disponibles
    const availableAdvantages = Object.values(AccommodationAdvantage);

    function startEditing() {
        isEditing.value = true;
        localValue.value = [...props.modelValue];
    }

    function cancelEditing() {
        isEditing.value = false;
        localValue.value = [...props.modelValue];
    }

    function saveChanges() {
        emit('save', localValue.value);
        isEditing.value = false;
    }

    const displayValue = computed(() => {
        if (props.modelValue.length === 0) {
            return 'Aucune préférence sélectionnée';
        }
        return props.modelValue.map((advantage) => accommodationAdvantageLabels[advantage]).join(', ');
    });

    watch(
        () => props.modelValue,
        (newValue) => {
            if (!isEditing.value) {
                localValue.value = [...newValue];
            }
        }
    );
</script>

<template>
    <div class="flex justify-between items-start">
        <div class="flex-1">
            <p class="text-body-md font-bold">{{ label }}</p>
            <p v-if="!isEditing" class="mt-1">
                {{ displayValue }}
            </p>
            <div v-else class="mt-2 space-y-4">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50"
                >
                    <div v-for="advantage in availableAdvantages" :key="advantage" class="flex items-center gap-2">
                        <input
                            :id="`preference-${advantage}`"
                            v-model="localValue"
                            type="checkbox"
                            :value="advantage"
                            class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                        />
                        <label :for="`preference-${advantage}`" class="text-sm text-gray-700 cursor-pointer">
                            {{ accommodationAdvantageLabels[advantage] }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="whitespace-nowrap ml-4">
            <a v-if="!isEditing" href="#" class="text-sm underline" @click.prevent="startEditing">modifier</a>
            <div v-else class="flex gap-2">
                <a href="#" class="text-sm underline text-green-700" @click.prevent="saveChanges">sauvegarder</a>
                <a href="#" class="text-sm underline text-gray-500" @click.prevent="cancelEditing">annuler</a>
            </div>
        </div>
    </div>
</template>
