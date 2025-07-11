export interface AccommodationItemDto {
    id: number;
    title: string; // Correspond au champ 'name' dans le backend
    image: string; // URL de l'image principale
    price?: number;
    slug: string; // Pour la navigation
}
