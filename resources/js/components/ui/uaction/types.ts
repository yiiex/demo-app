export interface Action {
    name: string;
    title: string;
    url: string;
    method?: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
    visible?: boolean;
    enabled?: boolean;
    confirm?: string;
    process?: string;
    async?: boolean;
    icon?: string;
    variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost';
}
