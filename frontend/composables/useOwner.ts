import type { AccommodationItemDto } from '~/types/dtos/accommodation_item.dto';

// Type étendu pour l'Owner avec tous les champs nécessaires
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

                // Transformation des hébergements pour l'affichage
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
     * @returns La durée en années ou mois
     */
    const getMembershipDuration = computed((): string => {
        if (!owner.value || !owner.value.createdAt) return '';

        const createdDate = new Date(owner.value.createdAt);
        const now = new Date();
        const diffMonths =
            (now.getFullYear() - createdDate.getFullYear()) * 12 + now.getMonth() - createdDate.getMonth();

        if (diffMonths >= 12) {
            const years = Math.floor(diffMonths / 12);
            return `${years} ${years === 1 ? 'an' : 'ans'}`;
        }

        return `${diffMonths} mois`;
    });

    return {
        owner,
        comments,
        rentals,
        isLoading,
        error,
        fetchOwnerById,
        getAverageRating,
        getFullName,
        isOwnerLoading,
        getMembershipDuration,
    };
};
