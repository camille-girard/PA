export interface CommentDto {
    id: number;
    content: string;
    rating: number;
    createdAt: string;
    client: {
        id: number;
        firstName: string;
        lastName: string;
    };
}
