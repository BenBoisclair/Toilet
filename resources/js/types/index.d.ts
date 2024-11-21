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
    location: string;
    ratings: number;
    facilities: Facility[];
    discoverer: User;
}
