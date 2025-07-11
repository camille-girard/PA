export interface ThemeAccommodationDto {
    id: number;
    name: string;
    images: {
        id: number;
        url: string;
    }[];
}
