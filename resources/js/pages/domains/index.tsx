import { Head, Link, router, useForm } from '@inertiajs/react';
import { Globe, Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { destroy, index, store, update } from '@/routes/domains';
import { index as checksIndex } from '@/routes/domains/checks';
import type { Domain } from '@/types';

const EmptyState = () => (
    <div className="p-8 text-center">
        <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-muted">
            <Globe className="h-7 w-7 text-muted-foreground" />
        </div>
        <p className="font-medium">No domains yet</p>
        <p className="mt-1 text-sm text-muted-foreground">Add a domain to get started</p>
    </div>
);

export default function DomainsIndex({ domains }: { domains: Domain[] }) {
    const [addOpen, setAddOpen] = useState(false);
    const [editingDomain, setEditingDomain] = useState<Domain | null>(null);
    const [deletingDomain, setDeletingDomain] = useState<Domain | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const addForm = useForm<{ name: string; check_interval: number; check_timeout: number; check_method: 'GET' | 'HEAD' }>({
        name: '',
        check_interval: 5,
        check_timeout: 10,
        check_method: 'GET',
    });
    const editForm = useForm<{ name: string; check_interval: number; check_timeout: number; check_method: 'GET' | 'HEAD' }>({
        name: '',
        check_interval: 5,
        check_timeout: 10,
        check_method: 'GET',
    });

    const handleAdd = (e: React.SyntheticEvent) => {
        e.preventDefault();
        addForm.post(store.url(), {
            preserveScroll: true,
            onSuccess: () => {
                setAddOpen(false);
                addForm.reset();
            },
        });
    };

    const openEdit = (domain: Domain) => {
        editForm.setData({
            name: domain.name,
            check_interval: domain.check_interval,
            check_timeout: domain.check_timeout,
            check_method: domain.check_method,
        });
        setEditingDomain(domain);
    };

    const handleEdit = (e: React.SyntheticEvent) => {
        e.preventDefault();
        if (!editingDomain) return;
        editForm.patch(update.url(editingDomain.id), {
            preserveScroll: true,
            onSuccess: () => {
                setEditingDomain(null);
                editForm.reset();
            },
        });
    };

    const handleDelete = () => {
        if (!deletingDomain) return;
        setIsDeleting(true);
        router.delete(destroy.url(deletingDomain.id), {
            preserveScroll: true,
            onSuccess: () => {
                setDeletingDomain(null);
                setIsDeleting(false);
            },
            onError: () => setIsDeleting(false),
        });
    };

    return (
        <div className="container p-4">
            <Head title="Domains" />

            <h1 className="sr-only">Domains</h1>

            <div className="space-y-6">
                <div className="flex items-start justify-between">
                    <Heading
                        variant="small"
                        title="Domains"
                        description="Manage the domains associated with your account"
                    />
                    <Button size="sm" onClick={() => setAddOpen(true)}>
                        <Plus className="mr-1.5 h-4 w-4" />
                        Add domain
                    </Button>
                </div>

                <div className="overflow-hidden rounded-lg border border-border">
                    {domains.length > 0 ? (
                        domains.map((domain) => (
                            <div
                                key={domain.id}
                                className="flex items-center justify-between border-b p-4 last:border-b-0"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-muted">
                                        <Globe className="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div className="space-y-1">
                                        <div className="flex items-center gap-2">
                                            <p className="font-medium tracking-tight">{domain.name}</p>
                                            {domain.latest_check != null && (
                                                <Badge
                                                    variant={domain.latest_check.is_up ? 'default' : 'destructive'}
                                                    className={domain.latest_check.is_up ? 'bg-green-500/15 text-green-700 border-green-500/30 dark:text-green-400' : ''}
                                                >
                                                    {domain.latest_check.is_up ? 'UP' : 'DOWN'}
                                                </Badge>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-1.5">
                                            <span className="inline-flex items-center rounded-md bg-muted px-2 py-0.5 text-[11px] font-medium tracking-wide text-muted-foreground uppercase ring-1 ring-border ring-inset">
                                                {domain.check_method}
                                            </span>
                                            <span className="inline-flex items-center rounded-md bg-muted px-2 py-0.5 text-[11px] font-medium tracking-wide text-muted-foreground ring-1 ring-border ring-inset">
                                                every {domain.check_interval === 60 ? '1h' : `${domain.check_interval}m`}
                                            </span>
                                            <span className="inline-flex items-center rounded-md bg-muted px-2 py-0.5 text-[11px] font-medium tracking-wide text-muted-foreground ring-1 ring-border ring-inset">
                                                {domain.check_timeout}s timeout
                                            </span>
                                            <Link
                                                href={checksIndex(domain.id).url}
                                                className="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium text-muted-foreground hover:text-foreground transition-colors"
                                            >
                                                View history →
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex items-center gap-1">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => openEdit(domain)}
                                    >
                                        <Pencil className="h-4 w-4" />
                                        <span className="sr-only">Edit {domain.name}</span>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        className="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        onClick={() => setDeletingDomain(domain)}
                                    >
                                        <Trash2 className="h-4 w-4" />
                                        <span className="sr-only">Delete {domain.name}</span>
                                    </Button>
                                </div>
                            </div>
                        ))
                    ) : (
                        <EmptyState />
                    )}
                </div>
            </div>

            {/* Add domain dialog */}
            <Dialog
                open={addOpen}
                onOpenChange={(open) => {
                    if (!open) {
                        setAddOpen(false);
                        addForm.reset();
                    } else {
                        setAddOpen(true);
                    }
                }}
            >
                <DialogContent>
                    <DialogTitle>Add domain</DialogTitle>
                    <DialogDescription>Enter the domain name you want to add.</DialogDescription>
                    <form onSubmit={handleAdd} className="space-y-4">
                        <div className="grid gap-2">
                            <Label htmlFor="add-name">Domain name</Label>
                            <Input
                                id="add-name"
                                name="name"
                                value={addForm.data.name}
                                onChange={(e) => addForm.setData('name', e.target.value)}
                                placeholder="example.com"
                                autoComplete="off"
                                autoFocus
                            />
                            <InputError message={addForm.errors.name} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="add-check-interval">Check interval</Label>
                            <Select
                                value={String(addForm.data.check_interval)}
                                onValueChange={(value) => addForm.setData('check_interval', parseInt(value))}
                            >
                                <SelectTrigger id="add-check-interval" className="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="1">1 minute</SelectItem>
                                    <SelectItem value="5">5 minutes</SelectItem>
                                    <SelectItem value="10">10 minutes</SelectItem>
                                    <SelectItem value="15">15 minutes</SelectItem>
                                    <SelectItem value="30">30 minutes</SelectItem>
                                    <SelectItem value="60">1 hour</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError message={addForm.errors.check_interval} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="add-check-timeout">
                                Request timeout{' '}
                                <span className="text-muted-foreground">(seconds)</span>
                            </Label>
                            <Input
                                id="add-check-timeout"
                                type="number"
                                min={1}
                                max={60}
                                value={addForm.data.check_timeout}
                                onChange={(e) => addForm.setData('check_timeout', parseInt(e.target.value) || 1)}
                            />
                            <InputError message={addForm.errors.check_timeout} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="add-check-method">Check method</Label>
                            <Select
                                value={addForm.data.check_method}
                                onValueChange={(value) => addForm.setData('check_method', value as 'GET' | 'HEAD')}
                            >
                                <SelectTrigger id="add-check-method" className="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="GET">GET</SelectItem>
                                    <SelectItem value="HEAD">HEAD</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError message={addForm.errors.check_method} />
                        </div>
                        <DialogFooter className="gap-2">
                            <DialogClose asChild>
                                <Button type="button" variant="secondary">
                                    Cancel
                                </Button>
                            </DialogClose>
                            <Button type="submit" disabled={addForm.processing}>
                                {addForm.processing ? 'Adding...' : 'Add domain'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Edit domain dialog */}
            <Dialog
                open={editingDomain !== null}
                onOpenChange={(open) => {
                    if (!open) {
                        setEditingDomain(null);
                        editForm.reset();
                    }
                }}
            >
                <DialogContent>
                    <DialogTitle>Edit domain</DialogTitle>
                    <DialogDescription>Update the domain name.</DialogDescription>
                    <form onSubmit={handleEdit} className="space-y-4">
                        <div className="grid gap-2">
                            <Label htmlFor="edit-name">Domain name</Label>
                            <Input
                                id="edit-name"
                                name="name"
                                value={editForm.data.name}
                                onChange={(e) => editForm.setData('name', e.target.value)}
                                placeholder="example.com"
                                autoComplete="off"
                            />
                            <InputError message={editForm.errors.name} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="edit-check-interval">Check interval</Label>
                            <Select
                                value={String(editForm.data.check_interval)}
                                onValueChange={(value) => editForm.setData('check_interval', parseInt(value))}
                            >
                                <SelectTrigger id="edit-check-interval" className="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="1">1 minute</SelectItem>
                                    <SelectItem value="5">5 minutes</SelectItem>
                                    <SelectItem value="10">10 minutes</SelectItem>
                                    <SelectItem value="15">15 minutes</SelectItem>
                                    <SelectItem value="30">30 minutes</SelectItem>
                                    <SelectItem value="60">1 hour</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError message={editForm.errors.check_interval} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="edit-check-timeout">
                                Request timeout{' '}
                                <span className="text-muted-foreground">(seconds)</span>
                            </Label>
                            <Input
                                id="edit-check-timeout"
                                type="number"
                                min={1}
                                max={60}
                                value={editForm.data.check_timeout}
                                onChange={(e) => editForm.setData('check_timeout', parseInt(e.target.value) || 1)}
                            />
                            <InputError message={editForm.errors.check_timeout} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="edit-check-method">Check method</Label>
                            <Select
                                value={editForm.data.check_method}
                                onValueChange={(value) => editForm.setData('check_method', value as 'GET' | 'HEAD')}
                            >
                                <SelectTrigger id="edit-check-method" className="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="GET">GET</SelectItem>
                                    <SelectItem value="HEAD">HEAD</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError message={editForm.errors.check_method} />
                        </div>
                        <DialogFooter className="gap-2">
                            <DialogClose asChild>
                                <Button type="button" variant="secondary">
                                    Cancel
                                </Button>
                            </DialogClose>
                            <Button type="submit" disabled={editForm.processing}>
                                {editForm.processing ? 'Saving...' : 'Save changes'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Delete domain dialog */}
            <Dialog
                open={deletingDomain !== null}
                onOpenChange={(open) => {
                    if (!open && !isDeleting) {
                        setDeletingDomain(null);
                    }
                }}
            >
                <DialogContent>
                    <DialogTitle>Delete domain</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete "{deletingDomain?.name}"? This action cannot be undone.
                    </DialogDescription>
                    <DialogFooter className="gap-2">
                        <DialogClose asChild>
                            <Button variant="secondary" disabled={isDeleting}>
                                Cancel
                            </Button>
                        </DialogClose>
                        <Button variant="destructive" onClick={handleDelete} disabled={isDeleting}>
                            {isDeleting ? 'Deleting...' : 'Delete domain'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}

DomainsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Domains',
            href: index(),
        },
    ],
};
