export interface Client {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    address?: string;
    avatar?: string;
    isVerified?: boolean;
    roles?: string[];
    preferences?: string[];
    bookings?: BookingSummary[];
    createdAt?: string;
    bookingCount?: number;
}


export interface BookingSummary {
    id: number;
    startDate: string;
    endDate: string;
    status: string;
    totalPrice: number;
    accommodation: {
        id: number;
        name: string;
    };
}
