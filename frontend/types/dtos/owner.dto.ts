import type { Accommodation } from '../accommodation';

export interface OwnerDto {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    address?: string;
    avatar?: string;
    isVerified: boolean;
    createdAt: string;
    bio?: string;
    notation?: number;
    accommodationCount?: number;
    accommodations?: Accommodation[];
}
