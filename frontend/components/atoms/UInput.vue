<script setup lang="ts">
import type { InputTypeHTMLAttribute } from 'vue';

interface InputProps {
  placeholder?: string;
  label?: string;
  type: InputTypeHTMLAttribute;
  size?: 'sm' | 'md';
  name?: string;
  required?: boolean;
  hintText?: string;
  destructive?: boolean;
  modelValue?: string | number;
  icon?: Component | string | null;
  iconPosition?: 'leading' | 'trailing';
}

const _props = withDefaults(defineProps<InputProps>(), {
  size: 'sm',
  label: '',
  placeholder: '',
  name: '',
  required: false,
  destructive: false,
  hintText: '',
  modelValue: '',
  icon: null,
  iconPosition: 'leading',
});

const _emit = defineEmits<{
  'update:modelValue': [value: string | number];
}>();

const baseClasses =
    'flex gap-2 border border-gray-300 rounded-lg focus:ring-2 focus:outline-none placeholder:text-gray-400 text-gray-700 shadow-xs w-full';

const variantClasses = {
  default: 'focus:border-orange-500 focus:ring-orange-500',
  destructive: 'border-error-subtle focus:ring-error focus:border-error-subtle',
};

const sizeClasses = {
  sm: 'px-3 py-2',
  md: '',
};

const wrapperClasses = 'flex items-center relative w-full';
</script>

<template>
  <div class="flex flex-col gap-1.5">
    <label v-if="label" class="text-body-sm" :for="name">
      {{ label }} <span v-if="required" class="text-brand-tertiary">*</span>
    </label>
    <div :class="wrapperClasses">
      <component
          :is="icon"
          v-if="icon && iconPosition === 'leading'"
          class="absolute left-3 size-5 text-body-md pointer-events-none"
      />
      <input
          :id="name"
          :class="[
          baseClasses,
          variantClasses[destructive ? 'destructive' : 'default'],
          sizeClasses[size],
          { 'pl-10': icon && iconPosition === 'leading' },
          { 'pr-10': icon && iconPosition === 'trailing' },
        ]"
          :type="type"
          :placeholder="placeholder"
          :name="name"
          :value="modelValue"
          @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      />
      <component
          :is="icon"
          v-if="icon && iconPosition === 'trailing'"
          class="absolute right-3 size-5 text-body-md pointer-events-none"
      />
    </div>
    <p v-if="hintText" :class="['text-body-sm', destructive ? 'text-error-primary' : 'text-body-md']">
      {{ hintText }}
    </p>
  </div>
</template>
