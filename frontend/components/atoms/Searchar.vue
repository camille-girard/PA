<template>
  <div class="relative w-full max-w-sm">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <IconSearch class="w-5 h-5 text-gray-400" />
    </div>
    <input
        type="text"
        :placeholder="placeholder"
        v-model="modelValueProxy"
        @input="onInput"
        class="w-full pl-10 pr-4 py-2 rounded-xl bg-white shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import IconSearch from '~/components/atoms/icons/SearchIcon.vue' // adapte au chemin réel

const props = defineProps<{
  modelValue: string
  placeholder?: string
}>()

const emit = defineEmits(['update:modelValue', 'search'])

const modelValueProxy = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value),
})

function onInput() {
  emit('search', modelValueProxy.value)
}
</script>
