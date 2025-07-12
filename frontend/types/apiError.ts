export interface ApiError {
    status?: number;
    data?: {
        message?: string;
        error?: string;
    };
    message?: string;
}
