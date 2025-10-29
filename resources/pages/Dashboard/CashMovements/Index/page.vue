<template>
    <dashboard-app-layout :breadcrumbs>
        <div class="space-y-6 p-6">
            <PageHeaderTitle :title>
                <template #actions>
                    <Button @click="handleCreate">
                        <span class="mr-2">+</span>
                        Record Movement
                    </Button>
                </template>
            </PageHeaderTitle>

            <DataTables :data="movements" :columns="columns" :actions="rowActions" :facets="facets" />
        </div>
    </dashboard-app-layout>
</template>

<script setup lang="ts">
    import { router } from '@inertiajs/vue3';

    import { Button } from '@/components/ui/button';

    import PageHeaderTitle from '@/components/modify/PageHeaderTitle.vue';
    import DataTables from '@/components/modify/DataTables.vue';
    interface CashMovement {
        id: number;
        uuid: string;
        shift: { terminal: { name: string } };
        type: string;
        amount: number;
        reason: string;
        recorded_by: { name: string };
        created_at: string;
    }

    interface Props {
        title: string;
        movements: CashMovement[];
    }

    const props = defineProps<Props>();

    const breadcrumbs = [
        { title: 'Dashboard', href: route('dashboard.index') },
        { title: 'POS', href: '#' },
        { title: 'Cash Movements', href: route('dashboard.pos.cash-movements.index') },
    ];

    const columns = [
        { accessorKey: 'shift.terminal.name', header: 'Terminal' },
        { accessorKey: 'type', header: 'Type' },
        { accessorKey: 'amount', header: 'Amount' },
        { accessorKey: 'reason', header: 'Reason' },
        { accessorKey: 'recorded_by.name', header: 'Recorded By' },
        { accessorKey: 'created_at', header: 'Date' },
    ];

    const rowActions = [
        {
            label: 'View',
            onClick: (row: CashMovement) => router.visit(route('dashboard.pos.cash-movements.show', row.uuid)),
        },
    ];

    const facets = [
        {
            column: 'type',
            title: 'Type',
            options: [
                { label: 'Cash In', value: 'cash_in' },
                { label: 'Cash Out', value: 'cash_out' },
                { label: 'Petty Cash', value: 'petty_cash' },
                { label: 'Bank Deposit', value: 'bank_deposit' },
            ],
        },
    ];

    function handleCreate() {
        router.visit(route('dashboard.pos.cash-movements.create'));
    }
</script>

