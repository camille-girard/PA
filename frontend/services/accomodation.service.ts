import type { AccomodationsResponseDto } from '~/types/dtos/accomodations_response.dto';
import type { AccommodationDto } from '~/types/dtos/accommodation.dto';

export const useAccomodationService = () => {
    const { $api } = useNuxtApp();

    const getAccomodations = async () => {
        return await useAuthFetch<AccomodationsResponseDto>($api('/api/accommodations/'));
    };

    const getAccomodationById = async (id: number) => {
        return await useAuthFetch<AccommodationDto>($api(`/api/accommodations/${id}`));
    };

    const getAccomodationsByTheme = async (themeSlug: string) => {
        return await useAuthFetch<AccomodationsResponseDto>($api(`/api/themes/${themeSlug}/accommodations`));
    };

    const searchAccomodations = async (query: string) => {
        return await useAuthFetch<AccomodationsResponseDto>(
            $api(`/api/accommodations/search?q=${encodeURIComponent(query)}`)
        );
    };

    return {
        getAccomodations,
        getAccomodationById,
        getAccomodationsByTheme,
        searchAccomodations,
    };
};
