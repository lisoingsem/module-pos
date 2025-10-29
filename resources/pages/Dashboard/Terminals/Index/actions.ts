import { router } from '@inertiajs/vue3'

import type { RowAction } from '@/components/datatable/DataTableRowActions.vue'
import { confirm } from '@/utils/confirm-dialog'
import { hasPermission } from '@/utils/permissions'

import type { Terminal } from './schema'

/**
 * Get row actions based on item state and permissions
 */
export function getRowActions(terminal: Terminal): RowAction<Terminal>[] {
    const isTrashed = !!terminal.deleted_at

    const normalActions: RowAction<Terminal>[] = [
        {
            label: 'View Details',
            icon: 'EyeIcon',
            onClick: (terminal: Terminal) => {
                router.visit(route('dashboard.pos.terminals.show', { terminal: terminal.uuid }))
            },
            disabled: () => !hasPermission('view-terminals'),
        },
        {
            label: 'Edit',
            icon: 'PencilIcon',
            onClick: (terminal: Terminal) => {
                router.visit(route('dashboard.pos.terminals.edit', { terminal: terminal.uuid }))
            },
            disabled: () => !hasPermission('update-terminals'),
        },
        {
            label: 'Mark Active',
            icon: 'CheckCircleIcon',
            onClick: (terminal: Terminal) => {
                router.patch(
                    route('dashboard.pos.terminals.update', { terminal: terminal.uuid }),
                    { status: 'active' },
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            // Success handled by flash message
                        },
                    },
                )
            },
            disabled: () => !hasPermission('update-terminals') || terminal.status === 'active',
        },
        {
            label: 'Mark Inactive',
            icon: 'XCircleIcon',
            onClick: (terminal: Terminal) => {
                router.patch(
                    route('dashboard.pos.terminals.update', { terminal: terminal.uuid }),
                    { status: 'inactive' },
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            // Success handled by flash message
                        },
                    },
                )
            },
            disabled: () => !hasPermission('update-terminals') || terminal.status === 'inactive',
        },
        {
            label: 'Delete',
            icon: 'TrashIcon',
            onClick: (terminal: Terminal) => {
                confirm({
                    message: `Are you sure you want to delete terminal "${terminal.name}"?`,
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
                        router.delete(route('dashboard.pos.terminals.destroy', { terminal: terminal.uuid }), {
                            preserveScroll: true,
                        })
                    },
                })
            },
            disabled: () => !hasPermission('delete-terminals'),
        },
    ]

    return normalActions
}

