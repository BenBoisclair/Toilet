export interface User {
    id: number;
    username: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};

export interface Facility {
    id: number;
    name: string;
}

export interface Toilet {
    id: number;
    latitude: number;
    longitude: number;
    name: string;
    location: string | null;
    description: string;
    ratings: number;
    created_at: string;
    reviews_count: number | null;
    reviews_avg_rating: number | null;
    facilities: Facility[] | null;
    discoverer: User;
    reviews: Review[] | null;
}

export interface Gender {
    id: number;
    name: string;
}

export interface Review {
    id: number;
    created_at: string;
    content: string | null;
    rating: number;
    gender: Gender;
    reviewer: User;
    facilities: Facility[] | null;
}
