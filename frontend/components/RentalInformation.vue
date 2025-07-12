<script setup lang="ts">
    import '~/types/accommodation';
    import '~/types/owner';

    const props = defineProps<{
        informations?: Accommodation;
        host?: Owner;
    }>();
</script>

<template>
    <section id="rental-informations" class="space-y-10">
        <div v-if="props.informations">
            <h2 class="text-h2 mb-4">{{ props.informations.name }}</h2>
            <p class="text-gray-700">
                {{ props.informations.description }}
            </p>
        </div>

        <div v-if="props.informations?.advantages" class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div v-for="(info, index) in props.informations.advantages" :key="index" class="flex items-center gap-3">
                <span class="text-body-md text-gray-800 font-medium">{{ info }}</span>
            </div>
        </div>

        <div v-if="props.host" class="flex items-center gap-4 mt-8">
            <NuxtLink :to="`/owner/${props.host.id}`" class="flex items-center gap-4">
                <img 
                    :src="props.host.avatar"
                    alt="Photo Hôte" 
                    class="w-12 h-12 rounded-full object-cover"
                />
            </NuxtLink>
            <div class="w-[90%]">
                <p class="text-body-md font-semibold text-gray-800">
                    {{ props.host.firstName }} {{ props.host.lastName }}
                </p>
                <span class="font-normal text-sm text-gray-500">{{ props.host.bio }}</span>
                <div class="flex items-center">
                    <template v-for="n in Math.floor(props.host.rating)" :key="n">
                      <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.293c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.54-1.118l1.07-3.293a1 1 0 00-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>
