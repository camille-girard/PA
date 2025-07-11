import type { ThemeDto } from '~/types/dtos/theme.dto';
import type { ThemesWithAccommodationsResponseDto } from '~/types/dtos/themes_with_accommodations_response.dto';

export const useThemeService = () => {
    const { $api } = useNuxtApp();

    /**
     * Récupère tous les thèmes avec leurs hébergements
     */
    const getThemesWithAccommodations = async () => {
        return await useAuthFetch<ThemesWithAccommodationsResponseDto>($api('/api/themes/accommodation'));
    };

    /**
     * Récupère un thème par son slug
     */
    const getThemeBySlug = async (slug: string) => {
        return await useAuthFetch<ThemeDto>($api(`/api/themes/${slug}`));
    };

    return {
        getThemesWithAccommodations,
        getThemeBySlug,
    };
};
