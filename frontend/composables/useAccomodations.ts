import type { AccommodationDto } from '~/types/dtos/accommodation.dto'
import { useAccomodationService } from '~/services/accomodation.service'
import type { TrendingItemDto } from '~/types/dtos/trending_item.dto'

export const useAccomodations = () => {
    const accomodationService = useAccomodationService()
    const accomodations = ref<AccommodationDto[]>([])
    const trendingItems = ref<TrendingItemDto[]>([])
    const isLoading = ref<boolean>(false)
    const error = ref<Error | null>(null)

    /**
     * Charge tous les hébergements
     */
    const fetchAllAccomodations = async () => {
        isLoading.value = true
        error.value = null
        
        try {
            const response = await accomodationService.getAccomodations()
            
            if (response.data.value && Array.isArray(response.data.value)) {
                accomodations.value = response.data.value
            }
        } catch (err) {
            console.error('Erreur lors du chargement des hébergements:', err)
            error.value = err as Error
        } finally {
            isLoading.value = false
        }
    }
    
    /**
     * Transforme les hébergements en items pour l'affichage de tendances
     * @param limit Limite optionnelle du nombre d'items à retourner
     * @returns Une liste d'éléments de tendance formatés
     */
    const getTrendingItems = (limit?: number): TrendingItemDto[] => {
        const items = accomodations.value.map((acc) => ({
            id: acc.id,
            title: acc.name,
            price: acc.price,
            slug: acc.theme?.slug || acc.theme?.name?.toLowerCase().replace(/\\s+/g, '-') || 'accommodation',
            image: acc.images?.[0]?.url || 'https://via.placeholder.com/400x250',
        }))
        
        return limit ? items.slice(0, limit) : items
    }
    
    /**
     * Charge et prépare les éléments de tendance directement
     * @param limit Nombre d'éléments à afficher
     */
    const loadTrendingItems = async (limit = 3) => {
        isLoading.value = true
        error.value = null
        
        try {
            const response = await accomodationService.getAccomodations()
            
            if (response.data.value && Array.isArray(response.data.value)) {
                trendingItems.value = response.data.value.slice(0, limit).map((acc: AccommodationDto) => ({
                    id: acc.id,
                    title: acc.name,
                    price: acc.price,
                    slug: acc.theme?.slug || acc.theme?.name?.toLowerCase().replace(/\\s+/g, '-') || 'accommodation',
                    image: acc.images?.[0]?.url || 'https://via.placeholder.com/400x250',
                }))
            }
        } catch (err) {
            console.error('Erreur lors du chargement des tendances:', err)
            error.value = err as Error
        } finally {
            isLoading.value = false
        }
    }
    
    /**
     * Récupère un hébergement par son identifiant
     * @param id Identifiant de l'hébergement
     * @returns L'hébergement ou undefined si non trouvé
     */
    const getAccomodationById = async (id: number): Promise<AccommodationDto | undefined> => {
        try {
            const response = await accomodationService.getAccomodationById(id)
            return response.data.value as AccommodationDto
        } catch (err) {
            console.error(`Erreur lors de la récupération de l'hébergement ${id}:`, err)
            error.value = err as Error
            return undefined
        }
    }
    
    /**
     * Recherche des hébergements par thème
     * @param themeSlug Slug du thème
     */
    const fetchByTheme = async (themeSlug: string) => {
        isLoading.value = true
        error.value = null
        
        try {
            const response = await accomodationService.getAccomodationsByTheme(themeSlug)
            
            if (response.data.value && Array.isArray(response.data.value)) {
                accomodations.value = response.data.value
            }
        } catch (err) {
            console.error(`Erreur lors de la recherche d'hébergements par thème ${themeSlug}:`, err)
            error.value = err as Error
        } finally {
            isLoading.value = false
        }
    }
    
    /**
     * Recherche des hébergements par mot-clé
     * @param query Terme de recherche
     */
    const searchAccomodations = async (query: string) => {
        isLoading.value = true
        error.value = null
        
        try {
            const response = await accomodationService.searchAccomodations(query)
            
            if (response.data.value && Array.isArray(response.data.value)) {
                accomodations.value = response.data.value
            }
        } catch (err) {
            console.error(`Erreur lors de la recherche d'hébergements avec le terme "${query}":`, err)
            error.value = err as Error
        } finally {
            isLoading.value = false
        }
    }

    return {
        accomodations,
        trendingItems,
        isLoading,
        error,
        fetchAllAccomodations,
        getTrendingItems,
        loadTrendingItems,
        getAccomodationById,
        fetchByTheme,
        searchAccomodations
    }
}