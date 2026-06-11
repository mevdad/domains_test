export type DomainCheckNotification = {
    id: number;
    channel: 'mail' | 'telegram';
    status: 'pending' | 'sent' | 'failed';
    attempts: number;
    last_error: string | null;
    sent_at: string | null;
};

export type DomainCheck = {
    id: number;
    domain_id: number;
    is_up: boolean;
    status_code: number | null;
    response_time_ms: number | null;
    error: string | null;
    checked_at: string;
    notifications?: DomainCheckNotification[];
};
