export interface ThemeDto {
    id: number;
    name: string;
    description?: string;
    slug: string;
    image?: {
        url: string;
        alt?: string;
    };
}
