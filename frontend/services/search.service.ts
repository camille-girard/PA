import type { AccomodationsResponseDto } from '~/types/dtos/accomodations_response.dto';

export const useSearchService = () => {
    const { $api } = useNuxtApp();

    const searchAccommodations = async (query: string) => {
        return await useAuthFetch<AccomodationsResponseDto>(
            $api(`/api/accommodations/search?q=${encodeURIComponent(query)}`)
        );
    };

    const submitSearch = async (searchData: {
        destination: string;
        arrivalDate?: string;
        departureDate?: string;
        amountTravelers?: number;
    }) => {
        return await useAuthFetch<{ accommodations: AccomodationsResponseDto }>($api('/api/search'), {
            method: 'POST',
            body: searchData,
        });
    };

    return {
        searchAccommodations,
        submitSearch,
    };
};
