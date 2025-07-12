export interface Admin {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    isVerified: boolean;
    roles?: string[];
    createdAt?: string;
}
