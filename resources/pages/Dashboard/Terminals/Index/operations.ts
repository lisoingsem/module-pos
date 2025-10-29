import { router } from '@inertiajs/vue3'

import { confirm } from '@/utils/confirm-dialog'

import type { Terminal } from './schema'

/**
 * Mark terminals as active
 */
export function handleBulkMarkActive(terminals: Terminal[]): void {
    const ids = terminals.map((t) => t.id)

    confirm({
        message: `Mark ${ids.length} terminal(s) as active?`,
        header: 'Update Status',
        acceptProps: {
            label: 'Mark Active',
            variant: 'default',
        },
        rejectProps: {
            label: 'Cancel',
            variant: 'outline',
        },
        accept: () => {
            router.post(
                route('dashboard.pos.terminals.bulk-update'),
                {
                    ids,
                    status: 'active',
                },
                {
                    preserveScroll: true,
                },
            )
        },
    })
}

/**
 * Mark terminals as inactive
 */
export function handleBulkMarkInactive(terminals: Terminal[]): void {
    const ids = terminals.map((t) => t.id)

    confirm({
        message: `Mark ${ids.length} terminal(s) as inactive?`,
        header: 'Update Status',
        acceptProps: {
            label: 'Mark Inactive',
            variant: 'default',
        },
        rejectProps: {
            label: 'Cancel',
            variant: 'outline',
        },
        accept: () => {
            router.post(
                route('dashboard.pos.terminals.bulk-update'),
                {
                    ids,
                    status: 'inactive',
                },
                {
                    preserveScroll: true,
                },
            )
        },
    })
}

/**
 * Delete selected terminals
 */
export function handleBulkDelete(terminals: Terminal[]): void {
    const ids = terminals.map((t) => t.id)

    confirm({
        message: `Delete ${ids.length} terminal(s)? This action cannot be undone.`,
        header: 'Delete Terminals',
        acceptProps: {
            label: `Delete ${ids.length} Terminal(s)`,
            variant: 'destructive',
        },
        rejectProps: {
            label: 'Cancel',
            variant: 'outline',
        },
        accept: () => {
            router.post(
                route('dashboard.pos.terminals.bulk-delete'),
                { ids },
                {
                    preserveScroll: true,
                },
            )
        },
    })
}

