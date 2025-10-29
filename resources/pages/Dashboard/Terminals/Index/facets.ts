import { statuses } from '../data'

export const facets = [
    {
        column: 'status',
        title: 'Status',
        options: statuses.map((status) => ({
            label: status.label,
            value: status.value,
        })),
    },
]

