export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: 'admin' | 'moderator';
    active: boolean;
    must_set_password: boolean;
    minecraft_uuid: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
    can: { manageUsers: boolean };
};

export type Passkey = {
    id: number;
    name: string;
    authenticator: string | null;
    created_at_diff: string;
    last_used_at_diff: string | null;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
