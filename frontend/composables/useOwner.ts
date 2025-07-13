import type { AccommodationItemDto } from '~/types/dtos/accommodation_item.dto';

export interface OwnerDetail {
    id: string;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    profession?: string;
    languages?: string;
    adress?: string;
    roles?: string[];
    bio?: string;
    createdAt: string;
    avatar?: string;
    rating: number;
    comments?: Array<{
        id: string;
        content: string;
        rating: number;
        createdAt: string;
        client: {
            id: string;
        };
        owner: {
            id: string;
            firstName: string;
            lastName: string;
        };
    }>;
    accommodations?: Array<{
        id: number;
        name: string;
        price: number;
        images?: Array<{ url: string }>;
        theme?: { slug: string; name?: string };
    }>;
}

export const useOwner = () => {
    const { $api } = useNuxtApp();

    const owner = ref<OwnerDetail | null>(null);
    const comments = ref<OwnerDetail['comments']>([]);
    const rentals = ref<AccommodationItemDto[]>([]);
    const isLoading = ref<boolean>(false);
    const error = ref<Error | null>(null);

    /**
     * Récupère les données d'un propriétaire par son ID
     * @param id Identifiant du propriétaire
     */
    const fetchOwnerById = async (id: string | number) => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await useAuthFetch<OwnerDetail>($api(`/api/owners/${id}`));

            if (response.data.value) {
                owner.value = response.data.value;
                comments.value = response.data.value.comments || [];

                if (response.data.value.accommodations) {
                    rentals.value = response.data.value.accommodations.map((accommodation) => ({
                        id: accommodation.id,
                        title: accommodation.name,
                        image: accommodation.images?.[0]?.url || 'https://via.placeholder.com/400x250',
                        slug: accommodation.theme?.slug || 'default-slug',
                        price: accommodation.price,
                    }));
                }
            }
        } catch (err) {
            console.error('Erreur lors de la récupération des données du propriétaire:', err);
            error.value = err as Error;
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Calcule la note moyenne du propriétaire à partir des commentaires
     * @returns La notation moyenne ou 0 s'il n'y a pas de commentaires
     */
    const getAverageRating = computed((): number => {
        if (!comments.value || comments.value.length === 0) {
            return 0;
        }

        const sum = comments.value.reduce((total, comment) => total + comment.rating, 0);
        return parseFloat((sum / comments.value.length).toFixed(1));
    });

    /**
     * Récupère la notation directe du propriétaire
     * @returns La notation du propriétaire ou 0 par défaut
     */
    const getOwnerRating = computed((): number => {
        return owner.value?.rating || 0;
    });

    /**
     * Formate le nom complet du propriétaire
     * @returns Le nom complet formaté
     */
    const getFullName = computed((): string => {
        if (!owner.value) return '';
        return `${owner.value.firstName} ${owner.value.lastName}`;
    });

    /**
     * Vérifie si les données du propriétaire sont en cours de chargement
     * @returns True si les données sont en cours de chargement
     */
    const isOwnerLoading = computed((): boolean => {
        return isLoading.value;
    });

    /**
     * Récupère la durée depuis laquelle le propriétaire est inscrit
     * @returns La durée en années, mois ou jours selon la période
     */
    const getMembershipDuration = computed((): string => {
        if (!owner.value || !owner.value.createdAt) return '';

        const createdDate = new Date(owner.value.createdAt);
        const now = new Date();
        
        const diffTime = Math.abs(now.getTime() - createdDate.getTime());
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 30) {
            if (diffDays === 0) {
                return 'aujourd\'hui';
            } else if (diffDays === 1) {
                return '1 jour';
            } else {
                return `${diffDays} jours`;
            }
        }
        
        const diffMonths =
            (now.getFullYear() - createdDate.getFullYear()) * 12 + now.getMonth() - createdDate.getMonth();

        if (diffMonths < 12) {
            return diffMonths === 1 ? '1 mois' : `${diffMonths} mois`;
        }
        
        const years = Math.floor(diffMonths / 12);
        const remainingMonths = diffMonths % 12;
        
        if (remainingMonths === 0) {
            return years === 1 ? '1 an' : `${years} ans`;
        } else {
            const yearText = years === 1 ? '1 an' : `${years} ans`;
            const monthText = remainingMonths === 1 ? '1 mois' : `${remainingMonths} mois`;
            return `${yearText} et ${monthText}`;
        }
    });

    return {
        owner,
        comments,
        rentals,
        isLoading,
        error,
        fetchOwnerById,
        getAverageRating,
        getOwnerRating,
        getFullName,
        isOwnerLoading,
        getMembershipDuration,
    };
};
