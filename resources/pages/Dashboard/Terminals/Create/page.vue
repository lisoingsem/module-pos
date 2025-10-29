<template>
    <dashboard-app-layout :breadcrumbs>
        <div class="space-y-6 p-6">
            <div class="max-w-2xl">
                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold tracking-tight">{{ title }}</h1>
                    <p class="text-muted-foreground">Create a new POS terminal</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                    <Form :form="form" />
                </form>
            </div>
        </div>
    </dashboard-app-layout>
</template>

<script setup lang="ts">
    import { useForm } from '@inertiajs/vue3';

    import Form from '../Components/Form.vue';

    interface Props {
        title: string;
        item: {
            name: string | null;
            code: string | null;
            location: string | null;
            status: string;
            terminalable_type: string | null;
            terminalable_id: number | null;
            settings: any | null;
        };
    }

    const props = defineProps<Props>();

    const breadcrumbs = [
        { title: 'Dashboard', href: route('dashboard.index') },
        { title: 'POS', href: route('dashboard.pos.cashier.index') },
        { title: 'Terminals', href: route('dashboard.pos.terminals.index') },
        { title: 'Create', href: route('dashboard.pos.terminals.create') },
    ];

    const form = useForm({
        name: props.item.name,
        code: props.item.code,
        location: props.item.location,
        status: props.item.status,
        terminalable_type: props.item.terminalable_type,
        terminalable_id: props.item.terminalable_id,
        settings: props.item.settings,
    });

    function submit() {
        form.post(route('dashboard.pos.terminals.store'), {
            preserveScroll: true,
        });
    }
</script>
