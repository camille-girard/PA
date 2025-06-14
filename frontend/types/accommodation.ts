export type Accommodation = {
    id: number;
    name: string;
    description: string;
    address: string;
    capacity: number;
    price: number;
    advantage: string[];
    practicalInformations: string;
    images: any[];
    owner: Owner;
    host: string[];
    theme: Theme;
    comments: any[];
};
