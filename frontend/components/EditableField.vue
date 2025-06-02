<script setup lang="ts">
const props = defineProps<{
  label: string
  modelValue: string
  field: string
}>()

const emit = defineEmits(['update:modelValue', 'save'])

const editing = ref(false)
const localValue = ref(props.modelValue)

watch(() => props.modelValue, (val) => {
  localValue.value = val
})

function save() {
  emit('update:modelValue', localValue.value)
  emit('save', props.field, localValue.value)
  editing.value = false
}
</script>

<template>
  <div class="flex justify-between items-start">
    <div>
      <p class="text-body-md font-bold">{{ label }}</p>
      <p v-if="!editing">{{ modelValue || 'non fourni' }}</p>
      <input
          v-else
          v-model="localValue"
          class="border px-2 py-1 rounded text-sm w-full"
      />
    </div>
    <div class="whitespace-nowrap">
      <a v-if="!editing" href="#" @click.prevent="editing = true" class="text-sm underline">modifier</a>
      <a v-else href="#" @click.prevent="save" class="text-sm underline text-green-700">sauvegarder</a>
    </div>
  </div>
</template>
