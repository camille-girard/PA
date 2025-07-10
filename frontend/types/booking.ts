export interface Booking {
    id: number;
    startDate: string;
    endDate: string;
    status: string;
    totalPrice: number;
    accommodation: {
        id: number;
        name: string;
        description?: string;
        address?: string;
        price: number;
        images?: Array<{ url: string; isMain?: boolean }>;
    };
    client?: {
        id: number;
        firstName: string;
        lastName: string;
        email: string;
    };
}
