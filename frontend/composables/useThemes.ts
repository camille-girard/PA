import { useThemeService } from '~/services/theme.service';
import type { ThemeWithAccommodationsDto } from '~/types/dtos/theme_with_accommodations.dto';
import type { ThemeSectionDto } from '~/types/dtos/theme_section.dto';

export const useThemes = () => {
    const themeService = useThemeService();
    const themeSections = ref<ThemeSectionDto[]>([]);
    const isLoading = ref<boolean>(false);
    const error = ref<Error | null>(null);

    /**
     * Charge tous les thèmes avec leurs hébergements
     */
    const fetchThemesWithAccommodations = async () => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await themeService.getThemesWithAccommodations();

            if (response.data.value && response.data.value.themes) {
                themeSections.value = response.data.value.themes.map((theme) => ({
                    title: theme.name,
                    items: theme.accommodations.map((accommodation) => ({
                        id: accommodation.id,
                        title: accommodation.name,
                        image: accommodation.images[0]?.url || 'https://via.placeholder.com/400x250',
                        slug: theme.slug,
                    })),
                    slug: theme.slug,
                }));
            }
        } catch (err) {
            console.error('Erreur lors du chargement des thèmes:', err);
            error.value = err as Error;
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Récupère un thème par son slug
     */
    const getThemeBySlug = async (slug: string): Promise<ThemeWithAccommodationsDto | null> => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await themeService.getThemeBySlug(slug);
            isLoading.value = false;

            if (response.data.value) {
                return response.data.value as unknown as ThemeWithAccommodationsDto;
            }
            return null;
        } catch (err) {
            console.error(`Erreur lors du chargement du thème ${slug}:`, err);
            error.value = err as Error;
            isLoading.value = false;
            return null;
        }
    };

    return {
        themeSections,
        isLoading,
        error,
        fetchThemesWithAccommodations,
        getThemeBySlug,
    };
};
