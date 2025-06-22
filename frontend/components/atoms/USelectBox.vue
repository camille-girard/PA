<script setup lang="ts">
    import ChrevronDownIcon from '~/components/atoms/icons/ChrevronDownIcon.vue';

    interface SelectOption {
        label: string;
        value: string | number | Array<string>;
        icon?: Component;
    }

    interface SelectBoxProps {
        placeholder?: string;
        searchInput?: boolean;
        icon?: Component | null;
        modelValue: string | number | Array<string> | null;
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
        'update:modelValue': [value: string | number | Array<string> | null];
    }>();

    const isOpen = ref(false);
    const search = ref('');
    const selectBoxRef = ref<HTMLElement | null>(null);

    const selectedOptions = computed(() => {
        if (props.multiple) {
            return props.options.filter(
                (option) => Array.isArray(props.modelValue) && props.modelValue.some((val) => val === option.value)
            );
        } else {
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
            const index = currentValue.findIndex((val) => val === option.value);

            if (index === -1) {
                if (typeof option.value === 'string') {
                    currentValue.push(option.value);
                }
            } else {
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
            return Array.isArray(props.modelValue) && props.modelValue.some((val) => val === option.value);
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

    let handleClickOutside: (e: MouseEvent) => void;
    let handleKeyDown: (e: KeyboardEvent) => void;

    // Gestionnaire de clic à l'extérieur du selectbox pour le fermer
    onMounted(() => {
        handleClickOutside = (e: MouseEvent) => {
            const target = e.target as HTMLElement;
            if (selectBoxRef.value && !selectBoxRef.value.contains(target)) {
                isOpen.value = false;
            }
        };

        handleKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isOpen.value) {
                isOpen.value = false;
            }
        };

        document.addEventListener('click', handleClickOutside);
        document.addEventListener('keydown', handleKeyDown);
    });

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
        document.removeEventListener('keydown', handleKeyDown);
    });

    const baseClasses =
        'flex gap-2 border border-gray-300 rounded-lg focus:ring-2 focus:outline-none shadow-xs w-full focus:border-orange-500 focus:ring-orange-500 px-3 py-2 px-10';

    const variantClasses = {
        default: 'focus:border-orange-500 focus:ring-orange-500',
        destructive: 'border-error-subtle focus:ring-error focus:border-error-subtle',
        disabled: 'bg-disabled border-disabled-subtle text-fg-disabled cursor-not-allowed',
    };

    const sizeClasses = {
        sm: 'px-3 py-2 text-sm',
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
                <ChrevronDownIcon class="size-5 text-tertiary" />
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
                                :class="{ '!text-brand-primary': isSelected(option) }"
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
                                    class="ml-auto size-4 text-brand-primary"
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
