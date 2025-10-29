<template>
    <dashboard-app-layout :breadcrumbs>
        <div class="space-y-6 p-6">
            <div class="space-y-4">
                <!-- Page Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">{{ title }}</h1>
                        <p class="text-muted-foreground">Manage your POS terminals and stations</p>
                    </div>
                    <Button @click="handleCreate">
                        <Icon name="PlusIcon" class="mr-2 h-4 w-4" />
                        Add Terminal
                    </Button>
                </div>

                <!-- Bulk Actions Bar -->
                <div v-if="selectedRowCount > 0" class="flex items-center justify-between rounded-lg border bg-muted/50 p-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">{{ selectedRowCount }} item(s) selected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" size="sm">
                                    <Icon name="MoreVerticalIcon" class="h-4 w-4" />
                                    <span class="ml-2">Actions</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem @click="handleBulkMarkActive(selectedRows)">
                                    <Icon name="CheckCircleIcon" class="mr-2 h-4 w-4" />
                                    Mark as Active
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="handleBulkMarkInactive(selectedRows)">
                                    <Icon name="XCircleIcon" class="mr-2 h-4 w-4" />
                                    Mark as Inactive
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem class="text-destructive" @click="handleBulkDelete(selectedRows)">
                                    <Icon name="TrashIcon" class="mr-2 h-4 w-4" />
                                    Delete Selected
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>

                <!-- Data Table -->
                <DataTables />
            </div>
        </div>
    </dashboard-app-layout>
</template>

<script setup lang="ts">
    import { router } from '@inertiajs/vue3';
    import type { ColumnDef } from '@tanstack/vue-table';
    import { storeToRefs } from 'pinia';
    import { onMounted } from 'vue';

    import { Button } from '@/components/ui/button';
    import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
    import { useDataTableStore } from '@/stores/datatable-store';

    import { columns } from './columns';
    import { setupEcho } from './echo';
    import { facets } from './facets';
    import { handleBulkDelete, handleBulkMarkActive, handleBulkMarkInactive } from './operations';
    import type { Terminal } from './schema';

    import Icon from '@/components/Icon.vue';

    import DataTables from '@/components/modify/DataTables.vue';
    interface Props {
        title: string;
        items: {
            data: Terminal[];
            links: any;
            meta: any;
        };
    }

    const props = defineProps<Props>();

    const breadcrumbs = [
        { title: 'Dashboard', href: route('dashboard.index') },
        { title: 'POS', href: '#' },
        { title: 'Terminals', href: route('dashboard.pos.terminals.index') },
    ];

    const datatableStore = useDataTableStore();
    const { selectedRows, selectedRowCount } = storeToRefs(datatableStore);

    onMounted(() => {
        datatableStore.initializeTable({
            columns: columns as ColumnDef<unknown>[],
            data: props.items.data,
            facets: facets,
            searchKey: 'name',
        });

        setupEcho();
    });

    function handleCreate() {
        router.visit(route('dashboard.pos.terminals.create'));
    }
</script>
