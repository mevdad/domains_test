import { Head } from '@inertiajs/react';
import DomainCheckTable from '@/components/domain-check-table';
import Heading from '@/components/heading';
import { index as domainsIndex } from '@/routes/domains';
import type { Domain, DomainCheck, Paginator } from '@/types';

export default function DomainChecks({
    domain,
    checks,
    enabledChannels,
}: {
    domain: Domain;
    checks: Paginator<DomainCheck>;
    enabledChannels: { name: string; label: string; icon: string }[];
}) {
    return (
        <div className="container p-4">
            <Head title={`${domain.name} — Check History`} />

            <h1 className="sr-only">{domain.name} Check History</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title={domain.name}
                    description="Check history for this domain"
                />

                <DomainCheckTable checks={checks} enabledChannels={enabledChannels} />
            </div>
        </div>
    );
}

DomainChecks.layout = {
    breadcrumbs: [
        { title: 'Domains', href: domainsIndex() },
        { title: 'Check History' },
    ],
};
