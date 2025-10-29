import type { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'

import DataTableColumnHeader from '@/components/datatable/DataTableColumnHeader.vue'
import DataTableRowActions from '@/components/datatable/DataTableRowActions.vue'
import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'

import { getRowActions } from './actions'
import { statuses } from '../data'
import type { Terminal } from './schema'

export type { Terminal }

export const columns: ColumnDef<Terminal>[] = [
    {
        id: 'select',
        header: ({ table }) =>
            h(Checkbox, {
                modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
                'onUpdate:modelValue': (value: boolean | 'indeterminate') => table.toggleAllPageRowsSelected(!!value),
                ariaLabel: 'Select all',
                class: 'translate-y-0.5',
            }),
        cell: ({ row }) =>
            h(Checkbox, {
                modelValue: row.getIsSelected(),
                'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
                ariaLabel: 'Select row',
                class: 'translate-y-0.5',
            }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Name' }),
        cell: ({ row }) => {
            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-medium' }, row.original.name),
                h('span', { class: 'text-xs text-muted-foreground' }, row.original.code),
            ])
        },
    },
    {
        accessorKey: 'location',
        header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Location' }),
        cell: ({ row }) => h('span', row.original.location || '-'),
    },
    {
        accessorKey: 'status',
        header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Status' }),
        cell: ({ row }) => {
            const status = statuses.find((s) => s.value === row.original.status)

            if (!status) return null

            return h(Badge, { variant: 'outline', class: 'capitalize' }, () => status.label)
        },
        filterFn: (row, id, value) => {
            return value.includes(row.getValue(id))
        },
    },
    {
        accessorKey: 'shifts_count',
        header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Shifts' }),
        cell: ({ row }) => h('span', { class: 'text-muted-foreground' }, row.original.shifts_count || 0),
    },
    {
        accessorKey: 'printers_count',
        header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Printers' }),
        cell: ({ row }) => h('span', { class: 'text-muted-foreground' }, row.original.printers_count || 0),
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const actions = getRowActions(row.original)
            return h(DataTableRowActions, { row, actions })
        },
    },
]

