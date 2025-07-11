<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-4">
        <div v-if="$slots.header" class="border-b pb-4 mb-4">
            <slot name="header" />
        </div>
        <div>
            <slot />
        </div>
        <div v-if="$slots.footer" class="pt-4 mt-4 border-t">
            <slot name="footer" />
        </div>
    </div>
</template>
