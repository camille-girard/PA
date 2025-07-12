export interface Client {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    roles?: string[];
    bookings?: BookingSummary[];
    createdAt?: string;
    isVerified?: boolean;
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
