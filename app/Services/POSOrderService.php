<?php

declare(strict_types=1);

namespace Modules\POS\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Order\Enums\OrderChannelEnum;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;
use Modules\Order\Services\OrderService;
use Modules\POS\Models\Shift;
use Modules\Product\Models\Product;

final class POSOrderService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        private readonly OrderService $orderService,
        private readonly ShiftService $shiftService
    ) {}

    /**
     * Create POS order (cart).
     */
    public function createCart(?int $customerId = null, ?int $shiftId = null): Order
    {
        $data = [
            'channel' => OrderChannelEnum::POS->value,
            'status' => OrderStatusEnum::DRAFT->value,
            'payment_status' => 'unpaid',
            'fulfillment_status' => 'unfulfilled',
            'customer_id' => $customerId,
            'created_by' => Auth::id(),
            'settings' => [
                'shift_id' => $shiftId,
                'terminal_id' => request()->input('terminal_id'),
            ],
        ];

        return $this->orderService->createOrder($data);
    }

    /**
     * Create cart with items (one-step operation).
     *
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     */
    public function createCartWithItems(
        array $items,
        ?int $customerId = null,
        ?int $shiftId = null,
        ?float $orderDiscount = null
    ): Order {
        return DB::transaction(function () use ($items, $customerId, $shiftId, $orderDiscount) {
            // Step 1: Create empty cart
            $order = $this->createCart($customerId, $shiftId);

            // Step 2: Add items (prices fetched from DB for security)
            foreach ($items as $item) {
                $this->addToCart(
                    $order,
                    $item['product_id'],
                    $item['quantity']
                );
            }

            // Step 3: Apply order-level discount if provided
            if ($orderDiscount && $orderDiscount > 0) {
                $order->update(['discount_amount' => $orderDiscount]);
            }

            // Return fresh order with all items loaded
            return $order->fresh(['items', 'customer']);
        });
    }

    /**
     * Add product to cart.
     */
    public function addToCart(Order $order, int $productId, int $quantity = 1): Order
    {
        $product = Product::findOrFail($productId);

        $itemData = [
            'orderable_type' => Product::class,
            'orderable_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'unit_price' => $product->sale_price ?? $product->price,
            'quantity' => $quantity,
            'discount_amount' => 0,
            'tax_amount' => 0,
        ];

        $this->orderService->addItem($order, $itemData);

        return $order->fresh(['items']);
    }

    /**
     * Update cart item quantity.
     */
    public function updateCartItem(int $itemId, int $quantity): Order
    {
        $item = \Modules\Order\Models\OrderItem::findOrFail($itemId);

        if ($quantity <= 0) {
            $this->orderService->removeItem($item);
        } else {
            $this->orderService->updateItem($item, ['quantity' => $quantity]);
        }

        return $item->order->fresh(['items']);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(int $itemId): Order
    {
        $item = \Modules\Order\Models\OrderItem::findOrFail($itemId);
        $order = $item->order;

        $this->orderService->removeItem($item);

        return $order->fresh(['items']);
    }

    /**
     * Complete checkout and process payment.
     */
    public function checkout(Order $order, array $paymentData): Order
    {
        return DB::transaction(function () use ($order, $paymentData) {
            // Confirm order
            $order = $this->orderService->confirmOrder($order);

            // Update status to paid
            $order->update([
                'status' => OrderStatusEnum::PAID->value,
                'payment_status' => 'paid',
                'paid_at' => now(),
                'placed_at' => now(),
            ]);

            // Record payment
            $order->payments()->create([
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentData['method'],
                'status' => 'completed',
                'payment_date' => now(),
                'reference' => $paymentData['reference'] ?? null,
            ]);

            // Update shift sales
            if (isset($order->settings['shift_id'])) {
                $shift = Shift::find($order->settings['shift_id']);
                if ($shift && $shift->isOpen()) {
                    $this->shiftService->updateShiftSales($shift, $order->total, false);
                }
            }

            return $order->fresh(['items', 'payments']);
        });
    }

    /**
     * Process cash payment.
     */
    public function processCashPayment(Order $order, float $cashTendered): array
    {
        $change = $cashTendered - $order->total;

        $order = $this->checkout($order, [
            'amount' => $order->total,
            'method' => 'cash',
            'reference' => 'CASH-' . now()->format('YmdHis'),
        ]);

        return [
            'order' => $order,
            'cash_tendered' => $cashTendered,
            'change' => $change,
        ];
    }

    /**
     * Process card payment.
     */
    public function processCardPayment(Order $order, array $cardData): Order
    {
        // TODO: Integrate with Payway module for card processing
        return $this->checkout($order, [
            'amount' => $order->total,
            'method' => 'card',
            'reference' => $cardData['transaction_id'] ?? 'CARD-' . now()->format('YmdHis'),
        ]);
    }

    /**
     * Get active POS orders for cashier.
     */
    public function getActiveOrders(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('channel', OrderChannelEnum::POS->value)
            ->where('status', OrderStatusEnum::DRAFT->value)
            ->where('created_by', $userId)
            ->with(['items', 'customer'])
            ->latest()
            ->get();
    }
}
