<script setup lang="ts">
    import SearchIcon from '~/components/atoms/icons/SearchIcon.vue';
    import CloseButton from '~/components/atoms/icons/XIcon.vue';

    import { useSearchResultsStore } from '~/stores/searchResults';

    const destination = ref('');
    const amountTravelers = ref<number | undefined>(undefined);
    const arrivalDate = ref();
    const departureDate = ref();
    const isLoading = ref(false);

    const clearForm = () => {
        destination.value = '';
        amountTravelers.value = undefined;
        arrivalDate.value = undefined;
        departureDate.value = undefined;
    };

    const handleSearch = async () => {
        if (!destination.value.trim()) {
            return;
        }

        const searchData = {
            destination: destination.value.trim(),
            arrivalDate: arrivalDate.value,
            departureDate: departureDate.value,
            amountTravelers: amountTravelers.value,
        };

        try {
            isLoading.value = true;

            const { $api } = useNuxtApp();
            const response = await useAuthFetch($api('/api/search'), {
                method: 'POST',
                body: searchData,
            });

            const searchResultsStore = useSearchResultsStore();

            searchResultsStore.setResults(response.data);

            const router = useRouter();
            await router.push({
                path: '/recherche',
                query: {
                    destination: searchData.destination,
                    arrivalDate: searchData.arrivalDate,
                    departureDate: searchData.departureDate,
                    amountTravelers: searchData.amountTravelers,
                },
            });
        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const route = useRoute();

    onMounted(() => {
        if (route.query.destination) destination.value = String(route.query.destination);
        if (route.query.arrivalDate) arrivalDate.value = route.query.arrivalDate;
        if (route.query.departureDate) departureDate.value = route.query.departureDate;
        if (route.query.amountTravelers) amountTravelers.value = Number(route.query.amountTravelers);
    });
    const isFormEmpty = computed(
        () =>
            !destination.value.trim() &&
            !arrivalDate.value &&
            !departureDate.value &&
            (amountTravelers.value === undefined || amountTravelers.value === null)
    );
</script>

<template>
    <div class="flex flex-col items-center">
        <div class="mt-14 max-w-screen-sm space-y-3 w-full">
            <UInput v-model="destination" placeholder="Destination, thématique" type="text" :icon="SearchIcon" />
            <div class="flex w-full justify-between gap-2">
                <UDatePicker v-model="arrivalDate" placeholder="Arrivée" type="date" class="w-full" />
                <UDatePicker v-model="departureDate" placeholder="Départ" type="date" class="w-full" />
                <UInputNumber v-model="amountTravelers" placeholder="Voyageurs" type="number" :min="1" class="w-full" />
            </div>
        </div>
        <div class="flex justify-center items-center w-full gap-2 max-w-screen-sm mt-4">
            <UButton
                variant="primary"
                :disabled="isLoading"
                :icon="SearchIcon"
                icon-position="leading"
                @click="handleSearch"
            >
                Rechercher
            </UButton>
            <UButton
                v-if="!isFormEmpty"
                variant="secondary"
                icon-position="leading"
                :icon="CloseButton"
                @click="clearForm"
            >
                Effacer
            </UButton>
        </div>
    </div>
</template>
