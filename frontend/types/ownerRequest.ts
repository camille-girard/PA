export interface OwnerRequest {
    id: number;
    message: string;
    createdAt: string;
    reviewed: boolean;
    user: {
        id: number;
        firstName: string;
        lastName: string;
        email: string;
    };
}