<template>
    <div class="space-y-6">
        <!-- Name -->
        <div class="space-y-2">
            <Label>
                Terminal Name
                <span class="text-destructive">*</span>
            </Label>
            <Input v-model="form.name" placeholder="Enter terminal name" />
            <p class="text-sm text-muted-foreground">A unique name to identify this POS terminal</p>
            <p v-if="form.errors.name" class="text-sm font-medium text-destructive">{{ form.errors.name }}</p>
        </div>

        <!-- Code -->
        <div class="space-y-2">
            <Label>
                Terminal Code
                <span class="text-destructive">*</span>
            </Label>
            <Input v-model="form.code" placeholder="e.g., TERM-001" />
            <p class="text-sm text-muted-foreground">Unique code for this terminal</p>
            <p v-if="form.errors.code" class="text-sm font-medium text-destructive">{{ form.errors.code }}</p>
        </div>

        <!-- Location -->
        <div class="space-y-2">
            <Label>Location</Label>
            <Input v-model="form.location" placeholder="Physical location (optional)" />
            <p class="text-sm text-muted-foreground">Physical location of this terminal (optional)</p>
            <p v-if="form.errors.location" class="text-sm font-medium text-destructive">{{ form.errors.location }}</p>
        </div>

        <!-- Status -->
        <div class="space-y-2">
            <label class="text-sm font-medium"> Status <span class="text-destructive">*</span> </label>
            <Select v-model="form.status">
                <SelectTrigger>
                    <SelectValue placeholder="Select status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="status in statuses" :key="status.value" :value="status.value">
                        <div class="flex items-center gap-2">
                            <div :class="`h-2 w-2 rounded-full ${status.color}`"></div>
                            {{ status.label }}
                        </div>
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="form.errors.status" class="text-sm text-destructive">{{ form.errors.status }}</p>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-2">
            <Button variant="outline" type="button" @click="handleCancel"> Cancel </Button>
            <Button type="submit" :disabled="form.processing"> {{ form.processing ? 'Saving...' : 'Save Terminal' }} </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { router } from '@inertiajs/vue3';

    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

    import { statuses } from '../data';

    interface Props {
        form: any;
    }

    defineProps<Props>();

    function handleCancel() {
        router.visit(route('dashboard.pos.terminals.index'));
    }
</script>
