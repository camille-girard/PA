export interface AdminDto {
    id: number;
    email: string;
    firstName: string;
    lastName: string;
    phone?: string;
    isVerified: boolean;
    roles?: string[];
    createdAt?: string;
}
