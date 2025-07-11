import type { ThemeAccommodationDto } from './theme_accommodation.dto';

export interface ThemeWithAccommodationsDto {
    id: number;
    name: string;
    slug: string;
    accommodations: ThemeAccommodationDto[];
}
