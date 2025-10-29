<template>
    <Dialog v-model:open="uiStore.showPaymentDialog">
        <DialogContent class="h-[90vh] w-[95vw] !max-w-6xl p-0" @pointerDownOutside.prevent @escapeKeyDown.prevent>
            <!-- Header (Fixed) -->
            <DialogHeader class="border-b px-6 py-4">
                <DialogTitle class="text-2xl">{{ __('pos.cashier.payment-dialog.title') }}</DialogTitle>
            </DialogHeader>

            <!-- Body (Scrollable, 2-column layout) -->
            <div class="grid flex-1 grid-cols-2 gap-8 overflow-y-auto p-8">
                <!-- LEFT SIDE: Payment Input -->
                <div class="space-y-6">
                    <!-- Payment Method Tabs -->
                    <div class="grid grid-cols-3 gap-2">
                        <Button
                            :variant="paymentMethod === 'cash' ? 'default' : 'outline'"
                            size="lg"
                            class="cursor-pointer"
                            @click="selectPaymentMethod('cash')"
                        >
                            💵
                        </Button>
                        <Button
                            :variant="paymentMethod === 'card' ? 'default' : 'outline'"
                            size="lg"
                            class="cursor-pointer"
                            @click="selectPaymentMethod('card')"
                        >
                            💳
                        </Button>
                        <Button
                            :variant="paymentMethod === 'mobile_money' ? 'default' : 'outline'"
                            size="lg"
                            class="cursor-pointer"
                            @click="selectPaymentMethod('mobile_money')"
                        >
                            📱
                        </Button>
                    </div>

                    <!-- CASH PAYMENT -->
                    <div v-if="paymentMethod === 'cash'" class="space-y-6">
                        <!-- Amount Received -->
                        <div class="space-y-3">
                            <label class="text-lg font-semibold">{{ __('pos.cashier.payment-dialog.amount-received') }}</label>
                            <Input
                                ref="amountInput"
                                v-model="amountInputDisplay"
                                type="number"
                                step="0.01"
                                class="h-24 cursor-text border-2 text-center text-5xl font-bold"
                                :placeholder="total.toFixed(2)"
                                @focus="selectAllText"
                                @input="handleAmountInput"
                            />
                            <div class="flex gap-2">
                                <Button type="button" size="default" variant="outline" class="flex-1 cursor-pointer" @click="setExactTotal">
                                    {{ __('pos.cashier.payment-dialog.exact-amount') }}
                                </Button>
                                <Button type="button" size="default" variant="outline" class="flex-1 cursor-pointer" @click="clearAmount">
                                    {{ __('pos.cashier.payment-dialog.clear') }}
                                </Button>
                            </div>
                        </div>

                        <!-- Quick Add (USD Default) -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-muted-foreground">{{ __('pos.cashier.payment-dialog.quick-add') }}</label>
                            <div class="grid grid-cols-6 gap-2">
                                <Button
                                    v-for="denom in [1, 5, 10, 20, 50, 100]"
                                    :key="denom"
                                    type="button"
                                    size="default"
                                    variant="secondary"
                                    class="cursor-pointer"
                                    @click="addDenomination(denom)"
                                >
                                    +${{ denom }}
                                </Button>
                            </div>
                        </div>

                        <!-- Change -->
                        <div class="rounded-xl bg-muted/50 p-6">
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-semibold">{{ __('pos.cashier.payment-dialog.change') }}:</span>
                                <span class="text-4xl font-bold" :class="changeInUSD < 0 ? 'text-destructive' : 'text-green-600'">
                                    ${{ Math.abs(changeInUSD).toFixed(2) }}
                                </span>
                            </div>
                            <p v-if="changeInUSD < 0" class="mt-2 text-sm text-destructive">
                                {{ __('pos.cashier.payment-dialog.insufficient-amount') }}
                            </p>
                        </div>
                    </div>

                    <!-- CARD / MOBILE PAYMENT -->
                    <div v-else class="space-y-6">
                        <div class="rounded-xl border-2 border-muted bg-muted/30 p-8">
                            <div class="mb-6 text-center text-8xl">{{ paymentMethod === 'card' ? '💳' : '📱' }}</div>
                            <p class="mb-6 text-center text-lg text-muted-foreground">
                                {{
                                    paymentMethod === 'card'
                                        ? __('pos.cashier.payment-dialog.card-instruction')
                                        : __('pos.cashier.payment-dialog.mobile-instruction')
                                }}
                            </p>
                            <div class="rounded-lg bg-background p-6 text-center shadow-sm">
                                <p class="mb-2 text-xs font-medium tracking-wider text-muted-foreground uppercase">
                                    {{ __('pos.cashier.payment-dialog.charging') }}
                                </p>
                                <div class="text-5xl font-bold text-primary">${{ total.toFixed(2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: Order Summary -->
                <div class="space-y-6">
                    <!-- Total Amount Card -->
                    <div class="rounded-xl border-2 border-primary/20 bg-primary/5 p-6 text-center">
                        <p class="mb-2 text-sm font-medium tracking-wider text-muted-foreground uppercase">
                            {{ __('pos.cashier.payment-dialog.total-due') }}
                        </p>
                        <div class="text-6xl font-bold text-primary">${{ total.toFixed(2) }}</div>
                    </div>

                    <!-- Payment Summary (Cash only) -->
                    <div v-if="paymentMethod === 'cash'" class="space-y-4 rounded-xl border bg-muted/30 p-6">
                        <h3 class="text-lg font-semibold">{{ __('pos.cashier.payment-dialog.summary') }}</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between text-base">
                                <span class="text-muted-foreground">{{ __('pos.cashier.payment-dialog.total-due') }}:</span>
                                <span class="font-semibold">${{ total.toFixed(2) }}</span>
                            </div>

                            <div class="flex justify-between text-base">
                                <span class="text-muted-foreground">{{ __('pos.cashier.payment-dialog.amount-received') }}:</span>
                                <span class="font-semibold">${{ amountTendered.toFixed(2) }}</span>
                            </div>

                            <div class="border-t pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>{{ __('pos.cashier.payment-dialog.change') }}:</span>
                                    <span :class="changeInUSD < 0 ? 'text-destructive' : 'text-green-600'">
                                        ${{ Math.abs(changeInUSD).toFixed(2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Info (Card/Mobile) -->
                    <div v-else class="space-y-4 rounded-xl border bg-muted/30 p-6">
                        <h3 class="text-lg font-semibold">
                            {{
                                paymentMethod === 'card'
                                    ? __('pos.cashier.payment-dialog.card-payment')
                                    : __('pos.cashier.payment-dialog.mobile-payment')
                            }}
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            {{ __('pos.cashier.payment-dialog.external-payment-note') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer (Fixed) -->
            <DialogFooter class="border-t px-6 py-4">
                <Button variant="outline" size="lg" class="cursor-pointer" @click="handleCancel">
                    {{ __('common.cancel') }}
                </Button>
                <Button :disabled="!canProcessPayment || processing" size="lg" class="cursor-pointer" @click="handleProcessPayment">
                    {{ processing ? __('pos.cashier.payment-dialog.processing') : __('pos.cashier.payment-dialog.process-payment') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
    import axios from 'axios';
    import { computed, onMounted, ref, watch } from 'vue';

    import { Button } from '@/components/ui/button';
    import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import { useSound } from '../composables/useSound';
    import { useCartStore } from '../stores/cart-store';
    import { useUIStore } from '../stores/ui-store';

    const uiStore = useUIStore();
    const cartStore = useCartStore();
    const { play } = useSound();

    // State
    const paymentMethod = ref<'cash' | 'card' | 'mobile_money'>('cash');
    const amountTendered = ref(0);
    const amountInputDisplay = ref('0.00');
    const currencies = ref<any[]>([]);
    const selectedCurrency = ref<any | null>(null);
    const processing = ref(false);
    const amountInput = ref<any>(null);

    // Computed
    const total = computed(() => cartStore.total);
    const currentDenominations = computed(() => selectedCurrency.value?.denominations || []);

    const changeInUSD = computed(() => {
        if (!selectedCurrency.value || selectedCurrency.value.code === 'USD') {
            return amountTendered.value - total.value;
        }
        return convertToUSD(amountTendered.value) - total.value;
    });

    const paymentMethodName = computed(() => {
        if (paymentMethod.value === 'card') return __('pos.cashier.payment-method.card');
        if (paymentMethod.value === 'mobile_money') return __('pos.cashier.payment-method.mobile-money');
        return __('pos.cashier.payment-method.cash');
    });

    const canProcessPayment = computed(() => {
        if (cartStore.isEmpty) return false;
        if (paymentMethod.value === 'cash') {
            return changeInUSD.value >= 0;
        }
        return true;
    });

    // Watch for dialog open
    watch(
        () => uiStore.showPaymentDialog,
        (newVal) => {
            if (newVal) {
                const usdCurrency = currencies.value.find((c) => c.code === 'USD');
                selectedCurrency.value = usdCurrency || currencies.value[0] || null;
                paymentMethod.value = 'cash';
                setExactTotal();
            }
        },
    );

    // Payment Method Selection
    function selectPaymentMethod(method: 'cash' | 'card' | 'mobile_money') {
        paymentMethod.value = method;
        if (method === 'cash') {
            setExactTotal();
        }
    }

    // Currency Operations
    function convertToUSD(amount: number): number {
        if (!selectedCurrency.value || selectedCurrency.value.code === 'USD') {
            return amount;
        }
        return amount / selectedCurrency.value.exchange_rate_to_usd;
    }

    function convertFromUSD(amountUSD: number, targetCurrency?: any): number {
        const currency = targetCurrency || selectedCurrency.value;
        if (!currency || currency.code === 'USD') {
            return amountUSD;
        }
        return amountUSD * currency.exchange_rate_to_usd;
    }

    function formatDenomination(amount: number): string {
        return amount.toLocaleString();
    }

    function addDenomination(denom: number) {
        amountTendered.value += denom;
        updateInputDisplay();
        play('add');
    }

    // Input Handling Functions
    function updateInputDisplay() {
        amountInputDisplay.value = amountTendered.value.toFixed(2);
    }

    function handleAmountInput() {
        // Input is type="number", so value is already a number
        const parsed = parseFloat(amountInputDisplay.value) || 0;
        amountTendered.value = parsed;
    }

    function selectAllText() {
        if (amountInput.value) {
            const inputElement = amountInput.value.$el || amountInput.value;
            if (inputElement && typeof inputElement.select === 'function') {
                inputElement.select();
            }
        }
    }

    function clearAmount() {
        amountTendered.value = 0;
        updateInputDisplay();
    }

    function setExactTotal() {
        amountTendered.value = total.value;
        updateInputDisplay();
    }

    // Payment Processing
    async function handleProcessPayment() {
        if (cartStore.isEmpty) {
            play('error');
            return;
        }

        const paymentData: any = {
            payment_method: paymentMethod.value,
            amount: total.value,
        };

        if (paymentMethod.value === 'cash') {
            const amountInUSD = convertToUSD(amountTendered.value);

            if (amountInUSD < total.value) {
                play('error');
                return;
            }

            paymentData.cash_tendered = amountInUSD;
            paymentData.change = amountInUSD - total.value;
            paymentData.currency_code = selectedCurrency.value?.code || 'USD';
            paymentData.currency_amount_tendered = amountTendered.value;
        }

        try {
            processing.value = true;
            const result = await cartStore.checkout(paymentData);
            play('success');
            uiStore.closePaymentDialog();

            if (result) {
                uiStore.openReceiptDialog(result);
            }

            processing.value = false;
        } catch (error) {
            console.error('Payment failed:', error);
            play('error');
            processing.value = false;
        }
    }

    function handleCancel() {
        uiStore.closePaymentDialog();
    }

    // Fetch currencies on mount
    onMounted(async () => {
        try {
            const response = await axios.get(route('api.payway.currencies.active'));
            if (response.data.success) {
                currencies.value = response.data.currencies;
                selectedCurrency.value = currencies.value.find((c) => c.code === 'USD') || currencies.value[0] || null;
            }
        } catch (error) {
            console.error('Failed to fetch currencies:', error);
            currencies.value = [
                {
                    code: 'USD',
                    symbol: '$',
                    name: 'US Dollar',
                    exchange_rate_to_usd: 1,
                    denominations: [1, 5, 10, 20, 50, 100],
                },
            ];
            selectedCurrency.value = currencies.value[0];
        }

        updateInputDisplay();
    });
</script>
