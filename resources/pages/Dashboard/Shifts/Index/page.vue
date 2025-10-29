<template>
    <dashboard-app-layout :breadcrumbs>
        <div class="space-y-6 p-6">
            <PageHeaderTitle :title>
                <template #actions>
                    <Button @click="handleCreate">
                        <span class="mr-2">+</span>
                        Open New Shift
                    </Button>
                </template>
            </PageHeaderTitle>

            <DataTables
                :data="shifts"
                :columns="columns"
                :actions="rowActions"
                :operations="bulkOperations"
                :facets="facets"
                enable-selection
            />
        </div>
    </dashboard-app-layout>
</template>

<script setup lang="ts">
    import { router } from '@inertiajs/vue3';

    import { Button } from '@/components/ui/button';

    import PageHeaderTitle from '@/components/modify/PageHeaderTitle.vue';
    import DataTables from '@/components/modify/DataTables.vue';
    interface Shift {
        id: number;
        uuid: string;
        terminal: { name: string };
        opened_by: { name: string };
        status: string;
        opening_cash: number;
        closing_cash: number | null;
        opened_at: string;
        closed_at: string | null;
    }

    interface Props {
        title: string;
        shifts: Shift[];
    }

    const props = defineProps<Props>();

    const breadcrumbs = [
        { title: 'Dashboard', href: route('dashboard.index') },
        { title: 'POS', href: '#' },
        { title: 'Shifts', href: route('dashboard.pos.shifts.index') },
    ];

    const columns = [
        { accessorKey: 'terminal.name', header: 'Terminal' },
        { accessorKey: 'opened_by.name', header: 'Cashier' },
        { accessorKey: 'status', header: 'Status' },
        { accessorKey: 'opening_cash', header: 'Opening Cash' },
        { accessorKey: 'closing_cash', header: 'Closing Cash' },
        { accessorKey: 'opened_at', header: 'Opened At' },
    ];

    const rowActions = [
        {
            label: 'View',
            onClick: (row: Shift) => router.visit(route('dashboard.pos.shifts.show', row.uuid)),
        },
        {
            label: 'Close Shift',
            onClick: (row: Shift) => router.post(route('dashboard.pos.shifts.close', row.uuid)),
            show: (row: Shift) => row.status === 'open',
        },
    ];

    const bulkOperations = [];
    const facets = [
        {
            column: 'status',
            title: 'Status',
            options: [
                { label: 'Open', value: 'open' },
                { label: 'Closed', value: 'closed' },
                { label: 'Suspended', value: 'suspended' },
            ],
        },
    ];

    function handleCreate() {
        router.visit(route('dashboard.pos.shifts.create'));
    }
</script>

