<script setup lang="ts">
    import ChrevronDownIcon from '~/components/atoms/icons/ChrevronDownIcon.vue';
    import XIcon from '~/components/atoms/icons/XIcon.vue';

    interface SelectOption {
        label: string;
        value: unknown;
        icon?: Component;
    }

    interface SelectBoxProps {
        placeholder?: string;
        searchInput?: boolean;
        icon?: Component | null;
        modelValue: unknown;
        multiple?: boolean;
        name?: string;
        options: SelectOption[];
        label?: string;
        hintText?: string;
        destructive?: boolean;
        disabled?: boolean;
        size?: 'sm' | 'md';
        required?: boolean;
        position?: 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
    }

    const props = withDefaults(defineProps<SelectBoxProps>(), {
        placeholder: 'Sélectionner...',
        icon: null,
        name: '',
        multiple: false,
        label: '',
        destructive: false,
        hintText: '',
        disabled: false,
        size: 'sm',
        required: false,
        position: 'bottom-left',
        searchInput: false,
    });

    const emit = defineEmits<{
        'update:modelValue': [value: unknown];
    }>();

    const isOpen = ref(false);
    const search = ref('');
    const selectBoxRef = ref<HTMLElement | null>(null);

    const selectedOptions = computed(() => {
        if (props.multiple) {
            // Pour sélections multiples, on retourne un tableau d'options
            return props.options.filter(
                (option) => Array.isArray(props.modelValue) && props.modelValue.includes(option.value)
            );
        } else {
            // Pour sélection unique, on retourne une seule option
            return props.options.find((option) => option.value === props.modelValue);
        }
    });

    const displayValue = computed(() => {
        if (props.multiple) {
            if (Array.isArray(selectedOptions.value) && selectedOptions.value.length > 0) {
                return selectedOptions.value.map((option) => option.label).join(', ');
            }
            return props.placeholder;
        } else {
            // Check if selectedOptions.value exists and is a single object (not an array)
            return selectedOptions.value && !Array.isArray(selectedOptions.value)
                ? selectedOptions.value.label
                : props.placeholder;
        }
    });

    const filteredOptions = computed(() => {
        if (!search.value) {
            return props.options;
        }

        return props.options.filter((option) => option.label.toLowerCase().includes(search.value.toLowerCase()));
    });

    function toggleDropdown() {
        if (!props.disabled) {
            isOpen.value = !isOpen.value;
            if (isOpen.value) {
                search.value = '';
            }
        }
    }

    function selectOption(option: SelectOption) {
        if (props.multiple) {
            const currentValue = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
            const index = currentValue.indexOf(option.value);

            if (index === -1) {
                // Ajouter la valeur si elle n'est pas déjà sélectionnée
                currentValue.push(option.value);
            } else {
                // Retirer la valeur si elle est déjà sélectionnée
                currentValue.splice(index, 1);
            }

            emit('update:modelValue', currentValue);
        } else {
            emit('update:modelValue', option.value);
            isOpen.value = false;
        }
    }

    function isSelected(option: SelectOption): boolean {
        if (props.multiple) {
            return Array.isArray(props.modelValue) && props.modelValue.includes(option.value);
        }
        return props.modelValue === option.value;
    }

    function clearSelection(event: Event) {
        event.stopPropagation();
        if (props.multiple) {
            emit('update:modelValue', []);
        } else {
            emit('update:modelValue', null);
        }
    }

    // Gestionnaire de clic à l'extérieur du selectbox pour le fermer
    onMounted(() => {
        document.addEventListener('click', (e: MouseEvent) => {
            const target = e.target as HTMLElement;
            if (selectBoxRef.value && !selectBoxRef.value.contains(target)) {
                isOpen.value = false;
            }
        });
    });

    onUnmounted(() => {
        document.removeEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isOpen.value) {
                isOpen.value = false;
            }
        });
    });

    const baseClasses =
        'border bg-primary rounded-lg focus:ring-2 focus:border-transparent focus:outline-none placeholder:text-placeholder text-primary shadow-xs w-full';

    const variantClasses = {
        default: 'border-primary focus:ring-primary',
        destructive: 'border-error-subtle focus:ring-error',
        disabled: 'bg-disabled border-disabled-subtle text-fg-disabled cursor-not-allowed',
    };

    const sizeClasses = {
        sm: 'px-3 py-2',
        md: 'px-4 py-2.5',
    };
</script>

<template>
    <div ref="selectBoxRef" class="relative flex flex-col gap-1.5 w-full">
        <label v-if="label" class="text-secondary text-sm font-medium" :for="name">
            {{ label }} <span v-if="required" class="text-brand-tertiary">*</span>
        </label>

        <div
            :class="[
                baseClasses,
                disabled ? variantClasses.disabled : variantClasses[destructive ? 'destructive' : 'default'],
                sizeClasses[size],
                'flex items-center justify-between cursor-pointer',
            ]"
            @click="toggleDropdown"
        >
            <div class="flex items-center gap-2 flex-1 overflow-hidden">
                <component :is="icon" v-if="icon" class="flex-shrink-0 size-5 text-tertiary" />
                <div
                    class="text-ellipsis overflow-hidden whitespace-nowrap"
                    :class="{
                        'text-placeholder':
                            !selectedOptions || (Array.isArray(selectedOptions) && selectedOptions.length === 0),
                    }"
                >
                    {{ displayValue }}
                </div>
            </div>

            <div class="flex items-center">
                <button
                    v-if="modelValue && (modelValue.length > 0 || modelValue !== null)"
                    type="button"
                    class="mr-1 p-0.5 text-tertiary hover:text-tertiary-hover"
                    @click.stop="clearSelection($event)"
                >
                    <XIcon class="size-4" />
                </button>
                <ChrevronDownIcon class="size-5 text-fg-quaternary" />
            </div>
        </div>

        <p v-if="hintText" :class="['text-sm', destructive ? 'text-error-primary' : 'text-tertiary']">
            {{ hintText }}
        </p>

        <Transition :name="`dropdown-${position}`">
            <div
                v-if="isOpen"
                class="absolute z-10 w-full min-w-[180px] rounded-lg shadow-lg bg-primary border border-secondary-alt"
                :class="{
                    'top-auto bottom-full mb-2': position.startsWith('top'),
                    'top-full mt-2': position.startsWith('bottom'),
                    'left-0': position.endsWith('left'),
                    'right-0': position.endsWith('right'),
                }"
            >
                <div v-if="searchInput" class="px-2 pt-2">
                    <input
                        v-model="search"
                        type="text"
                        class="w-full px-3 py-1.5 text-sm border border-primary rounded-md focus:ring-primary focus:border-primary focus:outline-none"
                        placeholder="Rechercher..."
                        @click.stop
                        @keydown.stop
                    />
                </div>

                <div class="py-1 max-h-60 overflow-y-auto">
                    <div v-if="filteredOptions.length === 0" class="px-3 py-2 text-tertiary text-sm">
                        Aucun résultat trouvé
                    </div>
                    <div v-for="(option, index) in filteredOptions" :key="index" class="px-1.5 py-px">
                        <div
                            class="px-3 py-2 flex items-center gap-3 group hover:bg-primary-hover transition-colors rounded-md cursor-pointer"
                            @click.stop="selectOption(option)"
                        >
                            <div
                                class="flex items-center gap-2 w-full text-secondary group-hover:text-secondary-hover text-sm font-semibold"
                                :class="{ '!text-brand-secondary': isSelected(option) }"
                            >
                                <component
                                    :is="option.icon"
                                    v-if="option.icon"
                                    class="size-4 text-fg-quaternary group-hover:text-fg-quaternary-hover"
                                />
                                {{ option.label }}

                                <!-- Indicateur de sélection -->
                                <svg
                                    v-if="isSelected(option)"
                                    class="ml-auto size-4 text-brand-secondary"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
    /* Animations pour l'apparition et disparition du dropdown */
    .dropdown-bottom-right-enter-active,
    .dropdown-bottom-right-leave-active,
    .dropdown-bottom-left-enter-active,
    .dropdown-bottom-left-leave-active {
        transition:
            opacity 0.2s ease,
            transform 0.2s ease;
    }

    .dropdown-bottom-right-enter-from,
    .dropdown-bottom-right-leave-to,
    .dropdown-bottom-left-enter-from,
    .dropdown-bottom-left-leave-to {
        opacity: 0;
        transform: translateY(-10px);
    }

    .dropdown-top-right-enter-active,
    .dropdown-top-right-leave-active,
    .dropdown-top-left-enter-active,
    .dropdown-top-left-leave-active {
        transition:
            opacity 0.2s ease,
            transform 0.2s ease;
    }

    .dropdown-top-right-enter-from,
    .dropdown-top-right-leave-to,
    .dropdown-top-left-enter-from,
    .dropdown-top-left-leave-to {
        opacity: 0;
        transform: translateY(10px);
    }
</style>
