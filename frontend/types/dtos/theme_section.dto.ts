import type { AccommodationItemDto } from './accommodation_item.dto';

export interface ThemeSectionDto {
    title: string;
    slug: string;
    items: AccommodationItemDto[];
}
