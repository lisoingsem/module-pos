<?php

declare(strict_types=1);

namespace Modules\POS\Http\Controllers\Inertia\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;
use Modules\POS\Http\Requests\Cart\CheckoutRequest;
use Modules\POS\Http\Requests\Cart\StoreRequest;
use Modules\POS\Models\Shift;
use Modules\POS\Models\Terminal;
use Modules\POS\Services\POSOrderService;
use Modules\POS\Services\ReceiptService;
use Modules\Product\Models\Product;

final class CashierController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private readonly POSOrderService $posOrderService,
        private readonly ReceiptService $receiptService
    ) {}

    /**
     * Display the cashier interface.
     */
    public function index(Request $request): Response
    {
        $terminal = Terminal::where('status', 'active')->first();
        $openShift = $terminal ? Shift::where('terminal_id', $terminal->id)
            ->where('status', 'open')
            ->first() : null;

        return Inertia::render('POS::Dashboard/Cashier/Index', [
            'title' => __('pos::menu.cashier'),
            'terminal' => $terminal,
            'shift' => $openShift,
        ]);
    }

    /**
     * Create new cart with items.
     */
    public function createCart(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Delegate to service (all business logic in service layer)
        $order = $this->posOrderService->createCartWithItems(
            items: $validated['items'],
            customerId: $validated['customer_id'] ?? null,
            shiftId: $validated['shift_id'] ?? null,
            orderDiscount: $validated['order_discount'] ?? null
        );

        return response()->json([
            'success' => true,
            'order' => new OrderResource($order),
        ]);
    }

    /**
     * Add product to cart.
     */
    public function addToCart(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = $this->posOrderService->addToCart(
            $order,
            $validated['product_id'],
            $validated['quantity']
        );

        return response()->json([
            'success' => true,
            'order' => new OrderResource($order),
        ]);
    }

    /**
     * Update cart item.
     */
    public function updateCartItem(Request $request, int $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $order = $this->posOrderService->updateCartItem($itemId, $validated['quantity']);

        return response()->json([
            'success' => true,
            'order' => new OrderResource($order),
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(int $itemId): JsonResponse
    {
        $order = $this->posOrderService->removeFromCart($itemId);

        return response()->json([
            'success' => true,
            'order' => new OrderResource($order),
        ]);
    }

    /**
     * Process checkout.
     */
    public function checkout(CheckoutRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();

        if ('cash' === $validated['payment_method'] && isset($validated['cash_tendered'])) {
            $result = $this->posOrderService->processCashPayment($order, $validated['cash_tendered']);

            return response()->json([
                'success' => true,
                'order' => new OrderResource($result['order']),
                'change' => $result['change'],
                'cash_tendered' => $result['cash_tendered'],
                'receipt_html' => $this->receiptService->generateReceiptHTML($result['order']),
            ]);
        }

        // For card and mobile payments
        $paymentData = [
            'amount' => $validated['amount'],
            'method' => $validated['payment_method'], // Map payment_method to method
            'reference' => $validated['reference'] ?? mb_strtoupper($validated['payment_method']) . '-' . now()->format('YmdHis'),
        ];

        $order = $this->posOrderService->checkout($order, $paymentData);

        return response()->json([
            'success' => true,
            'order' => new OrderResource($order),
            'receipt_html' => $this->receiptService->generateReceiptHTML($order),
        ]);
    }

    /**
     * Get receipt.
     */
    public function getReceipt(Order $order): JsonResponse
    {
        $receiptHTML = $this->receiptService->generateReceiptHTML($order);

        return response()->json([
            'success' => true,
            'html' => $receiptHTML,
        ]);
    }

    /**
     * Print receipt.
     */
    public function printReceipt(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'printer_id' => ['required', 'integer', 'exists:pos_printers,id'],
        ]);

        $printer = \Modules\POS\Models\Printer::findOrFail($validated['printer_id']);
        $result = $this->receiptService->print($order, $printer);

        return response()->json([
            'success' => $result,
            'message' => $result ? __('pos::receipt.printed-successfully') : __('pos::receipt.print-failed'),
        ]);
    }

    /**
     * Search products.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $search = $request->input('search', '');

        $products = Product::where(function ($q) use ($search): void {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        })
            ->where('status', 'active')
            ->with(['media'])
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }
}
