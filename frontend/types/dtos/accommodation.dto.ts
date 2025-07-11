import type { AccommodationImageDto } from './accommodation_image.dto';
import type { CommentDto } from './comment.dto';
import type { OwnerDto } from './owner.dto';
import type { ThemeDto } from './theme.dto';

export interface AccommodationDto {
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
    advantage: string[];
    practicalInformations: string;
    latitude?: number;
    longitude?: number;
    minStay?: number;
    maxStay?: number;
    images: AccommodationImageDto[];
    owner: OwnerDto;
    theme?: ThemeDto;
    comments: CommentDto[];
}
