import type { DomainCheck } from './domain-check';

export type Domain = {
    id: number;
    name: string;
    check_interval: number;
    check_timeout: number;
    check_method: 'GET' | 'HEAD';
    created_at: string;
    updated_at: string;
    latest_check?: DomainCheck | null;
};
