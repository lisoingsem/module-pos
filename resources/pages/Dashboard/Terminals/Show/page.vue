<template>
    <dashboard-app-layout :breadcrumbs>
        <div class="space-y-6 p-6">
            <div class="max-w-4xl">
                <!-- Page Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">{{ item.name }}</h1>
                        <p class="text-muted-foreground">{{ item.code }}</p>
                    </div>
                    <div class="flex gap-2">
                        <Button variant="outline" @click="handleEdit">
                            <Icon name="PencilIcon" class="mr-2 h-4 w-4" />
                            Edit
                        </Button>
                        <Button variant="destructive" @click="handleDelete">
                            <Icon name="TrashIcon" class="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <!-- Terminal Details -->
                <div class="space-y-6">
                    <div class="rounded-lg border p-6">
                        <h2 class="mb-4 text-lg font-semibold">Terminal Information</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-muted-foreground">Name</div>
                                <div class="font-medium">{{ item.name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-muted-foreground">Code</div>
                                <div class="font-medium">{{ item.code }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-muted-foreground">Location</div>
                                <div class="font-medium">{{ item.location || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-muted-foreground">Status</div>
                                <Badge variant="outline" class="capitalize">{{ item.status_label }}</Badge>
                            </div>
                            <div>
                                <div class="text-sm text-muted-foreground">Last Used</div>
                                <div class="font-medium">{{ item.last_used_at ? formatDate(item.last_used_at) : 'Never' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-muted-foreground">Created</div>
                                <div class="font-medium">{{ formatDate(item.created_at) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </dashboard-app-layout>
</template>

<script setup lang="ts">
    import { router } from '@inertiajs/vue3';

    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { confirm } from '@/utils/confirm-dialog';

    import Icon from '@/components/Icon.vue';

    interface Props {
        title: string;
        item: {
            id: number;
            uuid: string;
            name: string;
            code: string;
            location: string | null;
            status: string;
            status_label: string;
            last_used_at: string | null;
            created_at: string;
            updated_at: string;
        };
    }

    const props = defineProps<Props>();

    const breadcrumbs = [
        { label: 'Dashboard', href: route('dashboard.index') },
        { label: 'POS', href: '#' },
        { label: 'Terminals', href: route('dashboard.pos.terminals.index') },
        { label: props.item.name, href: route('dashboard.pos.terminals.show', props.item.uuid) },
    ];

    function handleEdit() {
        router.visit(route('dashboard.pos.terminals.edit', props.item.uuid));
    }

    function handleDelete() {
        confirm({
            message: `Are you sure you want to delete terminal "${props.item.name}"?`,
            header: 'Delete Terminal',
            acceptProps: {
                label: 'Delete',
                variant: 'destructive',
            },
            rejectProps: {
                label: 'Cancel',
                variant: 'outline',
            },
            accept: () => {
                router.delete(route('dashboard.pos.terminals.destroy', props.item.uuid));
            },
        });
    }

    function formatDate(date: string) {
        return new Date(date).toLocaleString();
    }
</script>
