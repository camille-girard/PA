<script setup lang="ts">
    interface Props {
        length?: number;
        modelValue?: string;
        disabled?: boolean;
        placeholder?: string;
        label?: string;
        hintText?: string;
        destructive?: boolean;
        required?: boolean;
    }

    const props = withDefaults(defineProps<Props>(), {
        length: 6,
        modelValue: '',
        disabled: false,
        placeholder: '',
        error: false,
        errorMessage: '',
        destructive: false,
        hintText: '',
        required: false,
        label: '',
    });

    const emit = defineEmits<{
        'update:modelValue': [value: string];
        complete: [value: string];
    }>();

    const inputRefs = ref<HTMLInputElement[]>([]);
    const digits = ref<string[]>(new Array(props.length).fill(''));

    watch(
        () => props.modelValue,
        (newValue) => {
            if (newValue) {
                const valueArray = newValue.split('').slice(0, props.length);
                digits.value = [...valueArray, ...new Array(props.length - valueArray.length).fill('')];
            } else {
                digits.value = new Array(props.length).fill('');
            }
        },
        { immediate: true }
    );

    // Émettre les changements
    watch(
        digits,
        (newDigits) => {
            const value = newDigits.join('');
            emit('update:modelValue', value);

            // Vérifier si tous les champs sont remplis
            if (value.length === props.length && !value.includes('')) {
                emit('complete', value);
            }
        },
        { deep: true }
    );

    const handleInput = (index: number, event: Event) => {
        const target = event.target as HTMLInputElement;
        const value = target.value.replace(/[^0-9]/g, ''); // Seuls les chiffres sont autorisés

        if (value.length > 1) {
            // Si plusieurs caractères sont collés
            handlePaste(index, value);
            return;
        }

        digits.value[index] = value;

        // Passer au champ suivant si un chiffre est saisi
        if (value && index < props.length - 1) {
            focusNext(index + 1);
        }
    };

    const handleKeydown = (index: number, event: KeyboardEvent) => {
        const _target = event.target as HTMLInputElement;

        switch (event.key) {
            case 'Backspace':
                if (!digits.value[index] && index > 0) {
                    // Si le champ actuel est vide, supprimer le chiffre précédent
                    digits.value[index - 1] = '';
                    focusNext(index - 1);
                } else {
                    digits.value[index] = '';
                }
                break;

            case 'ArrowLeft':
                event.preventDefault();
                if (index > 0) {
                    focusNext(index - 1);
                }
                break;

            case 'ArrowRight':
                event.preventDefault();
                if (index < props.length - 1) {
                    focusNext(index + 1);
                }
                break;

            case 'Delete':
                digits.value[index] = '';
                break;

            default:
                // Empêcher la saisie de caractères non numériques
                if (!/[0-9]/.test(event.key) && !['Tab', 'Enter', 'Escape'].includes(event.key)) {
                    event.preventDefault();
                }
        }
    };

    const handlePaste = (startIndex: number, pastedValue: string) => {
        const numbers = pastedValue.replace(/[^0-9]/g, '').split('');

        for (let i = 0; i < numbers.length && startIndex + i < props.length; i++) {
            digits.value[startIndex + i] = numbers[i];
        }

        // Mettre le focus sur le dernier champ rempli ou le suivant
        const lastFilledIndex = Math.min(startIndex + numbers.length - 1, props.length - 1);
        const nextIndex = Math.min(lastFilledIndex + 1, props.length - 1);
        focusNext(nextIndex);
    };

    const focusNext = (index: number) => {
        nextTick(() => {
            if (inputRefs.value[index]) {
                inputRefs.value[index].focus();
                inputRefs.value[index].select();
            }
        });
    };

    const clearAll = () => {
        digits.value = new Array(props.length).fill('');
        focusNext(0);
    };

    defineExpose({
        clearAll,
        focus: () => focusNext(0),
    });
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label v-if="label" class="text-secondary text-sm font-medium">
            {{ label }} <span v-if="required" class="text-brand-tertiary">*</span>
        </label>

        <div class="flex gap-2 justify-center">
            <input
                v-for="(digit, index) in digits"
                :key="index"
                ref="inputRefs"
                v-model="digits[index]"
                type="text"
                inputmode="numeric"
                maxlength="1"
                :placeholder="placeholder"
                :disabled="disabled"
                :class="[
                    'rounded-[10px] size-16 px-2 py-0.5 shadow-xs bg-primary border border-primary text-center focus:ring focus:ring-primary focus:ring-offset-2 focus:outline-none text-3xl text-primary transition-all duration-200 ease-in-out',
                    {
                        '!border-error-subtle !border-2 !focus:ring-error': destructive,
                        '!border-2 !border-brand': digit && !destructive,
                    },
                ]"
                @input="handleInput(index, $event)"
                @keydown="handleKeydown(index, $event)"
                @paste.prevent="handlePaste(index, $event.clipboardData?.getData('text') || '')"
                @focus="($event.target as HTMLInputElement).select()"
            />
        </div>

        <p v-if="hintText" :class="['text-sm', destructive ? 'text-error-primary' : 'text-tertiary']">
            {{ hintText }}
        </p>
    </div>
</template>
