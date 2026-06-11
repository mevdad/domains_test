import { Head } from '@inertiajs/react';
import DomainCheckTable from '@/components/domain-check-table';
import Heading from '@/components/heading';
import { index as logsIndex } from '@/routes/logs';
import type { DomainCheck, Paginator } from '@/types';

type CheckWithDomain = DomainCheck & { domain: { id: number; name: string } };

export default function LogsIndex({
    checks,
    enabledChannels,
}: {
    checks: Paginator<CheckWithDomain>;
    enabledChannels: { name: string; label: string; icon: string }[];
}) {
    return (
        <div className="container p-4">
            <Head title="Logs" />

            <h1 className="sr-only">Check Logs</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Check Logs"
                    description="Domain check history across all your domains"
                />

                <DomainCheckTable checks={checks} enabledChannels={enabledChannels} showDomain />
            </div>
        </div>
    );
}

LogsIndex.layout = {
    breadcrumbs: [{ title: 'Logs', href: logsIndex() }],
};
