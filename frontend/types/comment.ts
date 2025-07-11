export type Comment = {
    id: string;
    content: string;
    rating: number;
    createdAt: string;
    client: {
        id: string;
    };
    owner: {
        id: string;
        firstName: string;
        lastName: string;
    };
};
