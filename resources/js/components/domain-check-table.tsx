import { Link } from '@inertiajs/react';
import { CheckCircle2, ChevronDown, ChevronRight, Clock, XCircle } from 'lucide-react';
import { Fragment, useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { index as checksIndex } from '@/routes/domains/checks';
import type { DomainCheck, DomainCheckNotification, Paginator } from '@/types';

type Channel = { name: string; label: string; icon: string };

type CheckRow = DomainCheck & { domain?: { id: number; name: string } };

function NotificationStatusIcon({ notification }: { notification: DomainCheckNotification | undefined }) {
    if (!notification) {
        return <span className="text-muted-foreground">—</span>;
    }
    if (notification.status === 'sent') {
        return <CheckCircle2 className="h-4 w-4 text-green-500" />;
    }
    if (notification.status === 'failed') {
        return (
            <span title={notification.last_error ?? undefined}>
                <XCircle className="h-4 w-4 text-destructive" />
            </span>
        );
    }
    return <Clock className="h-4 w-4 text-muted-foreground" />;
}

type Props = {
    checks: Paginator<CheckRow>;
    enabledChannels: Channel[];
    showDomain?: boolean;
};

export default function DomainCheckTable({ checks, enabledChannels, showDomain = false }: Props) {
    const [expanded, setExpanded] = useState<number | null>(null);
    const columnCount = 7 + (showDomain ? 1 : 0) + enabledChannels.length;

    return (
        <div className="space-y-4">
            <div className="overflow-hidden rounded-lg border border-border">
                {checks.data.length > 0 ? (
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b bg-muted/50 text-left text-xs font-medium text-muted-foreground uppercase">
                                <th className="px-4 py-3">Date / Time</th>
                                {showDomain && <th className="px-4 py-3">Domain</th>}
                                <th className="px-4 py-3">Status</th>
                                <th className="px-4 py-3">Method</th>
                                <th className="px-4 py-3">Code</th>
                                <th className="px-4 py-3">Response</th>
                                <th className="px-4 py-3">Error</th>
                                {enabledChannels.map((ch) => (
                                    <th key={ch.name} className="px-4 py-3" title={ch.label}>
                                        <span
                                            className="inline-flex"
                                            dangerouslySetInnerHTML={{ __html: ch.icon }}
                                        />
                                    </th>
                                ))}
                                <th className="px-4 py-3">Body</th>
                            </tr>
                        </thead>
                        <tbody>
                            {checks.data.map((check) => (
                                <Fragment key={check.id}>
                                    <tr className="border-b last:border-b-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-mono text-xs text-muted-foreground whitespace-nowrap">
                                            {new Date(check.checked_at).toLocaleString()}
                                        </td>
                                        {showDomain && check.domain && (
                                            <td className="px-4 py-3">
                                                <Link
                                                    href={checksIndex(check.domain.id).url}
                                                    className="font-medium hover:underline"
                                                >
                                                    {check.domain.name}
                                                </Link>
                                            </td>
                                        )}
                                        <td className="px-4 py-3">
                                            <Badge
                                                variant={check.is_up ? 'default' : 'destructive'}
                                                className={check.is_up ? 'bg-green-500/15 text-green-700 border-green-500/30 dark:text-green-400' : ''}
                                            >
                                                {check.is_up ? 'UP' : 'DOWN'}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {check.method ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {check.status_code ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 text-xs whitespace-nowrap">
                                            {check.response_time_ms != null ? `${check.response_time_ms} ms` : '—'}
                                        </td>
                                        <td className="px-4 py-3 max-w-xs truncate text-xs text-muted-foreground" title={check.error ?? undefined}>
                                            {check.error ?? '—'}
                                        </td>
                                        {enabledChannels.map((ch) => (
                                            <td key={ch.name} className="px-4 py-3">
                                                <NotificationStatusIcon
                                                    notification={check.notifications?.find((n) => n.channel === ch.name)}
                                                />
                                            </td>
                                        ))}
                                        <td className="px-4 py-3">
                                            {check.response_body ? (
                                                <button
                                                    type="button"
                                                    onClick={() => setExpanded(expanded === check.id ? null : check.id)}
                                                    className="inline-flex items-center text-muted-foreground hover:text-foreground"
                                                    aria-label="Toggle response body"
                                                >
                                                    {expanded === check.id ? (
                                                        <ChevronDown className="h-4 w-4" />
                                                    ) : (
                                                        <ChevronRight className="h-4 w-4" />
                                                    )}
                                                </button>
                                            ) : (
                                                <span className="text-muted-foreground">—</span>
                                            )}
                                        </td>
                                    </tr>
                                    {expanded === check.id && check.response_body && (
                                        <tr className="border-b last:border-b-0 bg-muted/20">
                                            <td colSpan={columnCount} className="px-4 py-3">
                                                <pre className="max-h-96 overflow-auto rounded-md bg-background p-3 font-mono text-xs whitespace-pre-wrap break-all">
                                                    {check.response_body}
                                                </pre>
                                            </td>
                                        </tr>
                                    )}
                                </Fragment>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <div className="p-8 text-center text-sm text-muted-foreground">No checks recorded yet.</div>
                )}
            </div>

            {checks.last_page > 1 && (
                <div className="flex items-center justify-center gap-1">
                    {checks.links.map((link, i) => (
                        link.url ? (
                            <Link
                                key={i}
                                href={link.url}
                                className={`px-3 py-1.5 rounded-md text-sm border transition-colors ${link.active ? 'bg-primary text-primary-foreground border-primary' : 'border-border hover:bg-muted'}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ) : (
                            <span
                                key={i}
                                className="px-3 py-1.5 rounded-md text-sm border border-border text-muted-foreground opacity-50"
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        )
                    ))}
                </div>
            )}
        </div>
    );
}
