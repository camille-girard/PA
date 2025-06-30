<script setup lang="ts">
    import { useRoute } from 'vue-router';
    import { ref, reactive, watchEffect } from 'vue';
    import UInputNumber from '~/components/atoms/UInputNumber.vue';
    import USelectBox from '~/components/atoms/USelectBox.vue';
    import UDatePicker from '~/components/molecules/UDatePicker.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const {
        public: { apiUrl },
    } = useRuntimeConfig();
    const route = useRoute();
    const id = route.params.id;

    const {
        data: booking,
        refresh,
        pending,
    } = await useAuthFetch(`/api/bookings/${id}`, {
        baseURL: apiUrl,
        transform: (res) => res.booking,
    });

    const { data: clients } = await useAuthFetch('/api/clients', {
        baseURL: apiUrl,
        transform: (res) =>
            res.map((c: any) => ({
                label: `${c.firstName} ${c.lastName}`,
                value: c.id,
            })),
    });

    const { data: accommodations } = await useAuthFetch('/api/accommodations', {
        baseURL: apiUrl,
        transform: (res) =>
            res.map((a: any) => ({
                label: a.name,
                value: a.id,
            })),
    });

    const statusOptions = [
        { label: 'Acceptée', value: 'accepted' },
        { label: 'En attente', value: 'pending' },
        { label: 'Refusée', value: 'refused' },
    ];

    const form = reactive({
        startDate: null as Date | null,
        endDate: null as Date | null,
        clientId: null as number | null,
        accommodationId: null as number | null,
        totalPrice: 0 as number | null,
        status: 'pending',
    });

    watchEffect(() => {
        if (booking.value) {
            form.startDate = new Date(booking.value.startDate);
            form.endDate = new Date(booking.value.endDate);
            form.clientId = booking.value.client?.id;
            form.accommodationId = booking.value.accommodation?.id;
            form.totalPrice = booking.value.totalPrice;
            form.status = booking.value.status;
        }
    });

    const saving = ref(false);
    const success = ref(false);
    const errorMsg = ref('');

    async function save() {
        saving.value = true;
        success.value = false;
        errorMsg.value = '';

        try {
            console.log('Payload envoyé :', {
                startDate: form.startDate?.toISOString(),
                endDate: form.endDate?.toISOString(),
                clientId: form.clientId,
                accommodationId: form.accommodationId,
                totalPrice: Number(form.totalPrice ?? 0),
                status: form.status,
            });
            await $fetch(`/api/bookings/${id}`, {
                method: 'PUT',
                baseURL: apiUrl,
                body: {
                    startDate: form.startDate?.toISOString(),
                    endDate: form.endDate?.toISOString(),
                    clientId: form.clientId,
                    accommodationId: form.accommodationId,
                    totalPrice: Number(form.totalPrice ?? 0),
                    status: form.status,
                },
            });
            success.value = true;
            await refresh();
        } catch (error: any) {
            console.error('Erreur lors de la mise à jour :', error);
            errorMsg.value = error?.data?.message || 'Erreur lors de l’enregistrement.';
        } finally {
            saving.value = false;
        }
    }
</script>

<template>
    <div class="max-w-3xl p-6 md:p-10 dark:bg-gray-900 space-y-8">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-white">Modifier la réservation</h1>

        <form class="grid gap-6 md:grid-cols-2" :aria-busy="saving || pending" @submit.prevent="save">
            <UDatePicker v-model="form.startDate" label="Date de début" name="startDate" required />
            <UDatePicker v-model="form.endDate" label="Date de fin" name="endDate" required />

            <USelectBox
                v-model="form.clientId"
                :options="clients || []"
                label="Client"
                name="clientId"
                required
                class="md:col-span-2"
            />

            <USelectBox
                v-model="form.accommodationId"
                :options="accommodations || []"
                label="Hébergement"
                name="accommodationId"
                required
                class="md:col-span-2"
            />

            <UInputNumber
                v-model="form.totalPrice"
                label="Prix total (€)"
                name="totalPrice"
                :min="0"
                :step="0.01"
                required
                class="md:col-span-2"
            />

            <USelectBox
                v-model="form.status"
                :options="statusOptions"
                label="Statut"
                name="status"
                required
                class="md:col-span-2"
            />

            <div class="md:col-span-2 flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
                <UButton :disabled="saving" :is-loading="saving" size="lg" variant="primary" type="submit">
                    {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
                </UButton>

                <span v-if="success" class="text-green-600 text-sm">Modifications enregistrées</span>
                <span v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</span>
            </div>
        </form>
    </div>
</template>
