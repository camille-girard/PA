export type Owner = {
    id: string;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    profession?: string;
    languages?: string[];
    location?: string;
    roles?: string[];
    bio?: string;
    createdAt: string;
    comments?: Comment[];
    accommodations?: Rental[];
};
