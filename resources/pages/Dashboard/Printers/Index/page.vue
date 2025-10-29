<template>
    <dashboard-app-layout :breadcrumbs>
        <div class="space-y-6 p-6">
            <PageHeaderTitle :title>
                <template #actions>
                    <Button @click="handleCreate">
                        <span class="mr-2">+</span>
                        Add Printer
                    </Button>
                </template>
            </PageHeaderTitle>

            <DataTables :data="printers" :columns="columns" :actions="rowActions" :operations="bulkOperations" :facets="facets" enable-selection />
        </div>
    </dashboard-app-layout>
</template>

<script setup lang="ts">
    import { router } from '@inertiajs/vue3';

    import { Button } from '@/components/ui/button';

    import PageHeaderTitle from '@/components/modify/PageHeaderTitle.vue';
    import DataTables from '@/components/modify/DataTables.vue';
    interface Printer {
        id: number;
        uuid: string;
        name: string;
        type: string;
        connection_type: string;
        ip_address: string | null;
        port: number | null;
        is_default: boolean;
        is_active: boolean;
    }

    interface Props {
        title: string;
        printers: Printer[];
    }

    const props = defineProps<Props>();

    const breadcrumbs = [
        { title: 'Dashboard', href: route('dashboard.index') },
        { title: 'POS', href: '#' },
        { title: 'Printers', href: route('dashboard.pos.printers.index') },
    ];

    const columns = [
        { accessorKey: 'name', header: 'Name' },
        { accessorKey: 'type', header: 'Type' },
        { accessorKey: 'connection_type', header: 'Connection' },
        { accessorKey: 'ip_address', header: 'IP Address' },
        { accessorKey: 'is_default', header: 'Default' },
        { accessorKey: 'is_active', header: 'Active' },
    ];

    const rowActions = [
        {
            label: 'View',
            onClick: (row: Printer) => router.visit(route('dashboard.pos.printers.show', row.uuid)),
        },
        {
            label: 'Edit',
            onClick: (row: Printer) => router.visit(route('dashboard.pos.printers.edit', row.uuid)),
        },
        {
            label: 'Set as Default',
            onClick: (row: Printer) => router.post(route('dashboard.pos.printers.set-default', row.uuid)),
            show: (row: Printer) => !row.is_default,
        },
        {
            label: 'Delete',
            onClick: (row: Printer) => router.delete(route('dashboard.pos.printers.destroy', row.uuid)),
        },
    ];

    const bulkOperations = [
        {
            label: 'Bulk Delete',
            onClick: (selectedIds: number[]) => {
                router.delete(route('dashboard.pos.printers.bulk-delete'), {
                    data: { ids: selectedIds },
                });
            },
        },
    ];

    const facets = [
        {
            column: 'type',
            title: 'Type',
            options: [
                { title: 'Receipt', value: 'receipt' },
                { title: 'Kitchen', value: 'kitchen' },
                { title: 'Label', value: 'label' },
                { title: 'Report', value: 'report' },
            ],
        },
        {
            column: 'is_active',
            title: 'Status',
            options: [
                { label: 'Active', value: true },
                { label: 'Inactive', value: false },
            ],
        },
    ];

    function handleCreate() {
        router.visit(route('dashboard.pos.printers.create'));
    }
</script>
