import type { AccommodationImage } from './accommodationImage';
import type { Comment } from './comment';
import type { Owner } from './owner';
import type { Theme } from './theme';
import type { AccommodationAdvantage } from './accommodationAdvantage';

export interface Accommodation {
    id: number;
    name: string;
    description: string;
    address: string;
    city?: string;
    postalCode?: string;
    country?: string;
    type?: string;
    bedrooms?: number;
    bathrooms?: number;
    capacity: number;
    price: number;
    rating?: number;
    advantage: AccommodationAdvantage[];
    practicalInformations: string;
    images: AccommodationImage[];
    owner: Owner;
    theme?: Theme;
    comments?: Comment[];
    latitude?: number;
    longitude?: number;
    minStay?: number;
    maxStay?: number;
    createdAt?: string;
}
