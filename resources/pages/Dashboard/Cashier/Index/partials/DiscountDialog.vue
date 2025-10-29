<template>
    <Dialog
        v-model:open="uiStore.showDiscountDialog"
        :title="isItemDiscount ? __('pos.cashier.discount-dialog.item-title') : __('pos.cashier.discount-dialog.order-title')"
        :description="
            isItemDiscount
                ? __('pos.cashier.discount-dialog.item-description', { args: { item: uiStore.discountTarget?.product_name } })
                : __('pos.cashier.discount-dialog.order-description')
        "
    >
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <Input v-model="discountAmount" type="number" placeholder="0" class="flex-1" min="0" max="100" />
                <span class="text-2xl font-bold">%</span>
            </div>

            <div class="flex justify-end gap-2">
                <Button variant="outline" @click="handleCancel">
                    {{ __('common.cancel') }}
                </Button>
                <Button @click="handleApplyDiscount" :disabled="discountAmount < 0 || discountAmount > 100">
                    {{ __('pos.cashier.discount-dialog.apply-discount') }}
                </Button>
            </div>
        </div>
    </Dialog>
</template>

<script setup lang="ts">
    import { computed, ref, watch } from 'vue';

    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { useSound } from '../composables/useSound';
    import { useCartStore } from '../stores/cart-store';
    import { useUIStore } from '../stores/ui-store';

    import Dialog from '@/components/modify/Dialog.vue';
    const uiStore = useUIStore();
    const cartStore = useCartStore();
    const { play } = useSound();

    const discountAmount = ref(0);

    const isItemDiscount = computed(() => uiStore.discountTarget !== null);

    watch(
        () => uiStore.showDiscountDialog,
        (newVal) => {
            if (newVal) {
                discountAmount.value = uiStore.discountTarget?.discount || cartStore.orderDiscount;
            }
        },
    );

    function handleApplyDiscount() {
        if (isItemDiscount.value) {
            cartStore.updateItemDiscount(uiStore.discountTarget.id, discountAmount.value);
        } else {
            cartStore.setOrderDiscount(discountAmount.value);
        }
        play('success');
        uiStore.closeDiscountDialog();
    }

    function handleCancel() {
        uiStore.closeDiscountDialog();
    }
</script>
