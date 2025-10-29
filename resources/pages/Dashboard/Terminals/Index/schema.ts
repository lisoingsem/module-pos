import { z } from 'zod'

export const terminalSchema = z.object({
    id: z.number(),
    uuid: z.string(),
    name: z.string(),
    code: z.string(),
    location: z.string().nullable(),
    status: z.string(),
    status_label: z.string().optional(),
    settings: z.any().nullable(),
    shifts_count: z.number().optional(),
    printers_count: z.number().optional(),
    last_used_at: z.string().nullable(),
    created_at: z.string(),
    updated_at: z.string(),
    deleted_at: z.string().nullable(),
})

export type Terminal = z.infer<typeof terminalSchema>

