<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }

        .business-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .business-info {
            font-size: 10px;
            color: #666;
        }

        .order-info {
            margin: 15px 0;
        }

        .order-info div {
            margin: 3px 0;
        }

        .items {
            margin: 15px 0;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .item-details {
            flex: 1;
        }

        .item-price {
            text-align: right;
            min-width: 80px;
        }

        .totals {
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 10px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .total-line.grand-total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        .payment {
            margin: 15px 0;
            padding: 10px 0;
            border-top: 1px dashed #000;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #000;
            font-size: 11px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="business-name">{{ $business['name'] }}</div>
        <div class="business-info">
            @if ($business['address'])
                {{ $business['address'] }}<br>
            @endif
            @if ($business['phone'])
                Tel: {{ $business['phone'] }}<br>
            @endif
        </div>
    </div>

    <div class="order-info">
        <div><strong>Order #:</strong> {{ $order->order_number }}</div>
        <div><strong>Date:</strong> {{ $date }}</div>
        @if ($order->customer)
            <div><strong>Customer:</strong> {{ $order->customer->name }}</div>
        @endif
    </div>

    <div class="items">
        <div style="border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
            <strong>Items:</strong>
        </div>
        @foreach ($order->items as $item)
            <div class="item">
                <div class="item-details">
                    <div>{{ $item->name }}</div>
                    <div style="font-size: 10px; color: #666;">
                        {{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}
                    </div>
                </div>
                <div class="item-price">
                    ${{ number_format($item->total, 2) }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="totals">
        <div class="total-line">
            <span>Subtotal:</span>
            <span>${{ number_format($order->subtotal, 2) }}</span>
        </div>
        @if ($order->tax_total > 0)
            <div class="total-line">
                <span>Tax:</span>
                <span>${{ number_format($order->tax_total, 2) }}</span>
            </div>
        @endif
        @if ($order->discount_total > 0)
            <div class="total-line">
                <span>Discount:</span>
                <span>-${{ number_format($order->discount_total, 2) }}</span>
            </div>
        @endif
        <div class="total-line grand-total">
            <span>TOTAL:</span>
            <span>${{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    <div class="payment">
        @foreach ($order->payments as $payment)
            <div class="total-line">
                <span>{{ mb_strtoupper($payment->payment_method->value ?? $payment->payment_method) }}:</span>
                <span>${{ number_format($payment->amount, 2) }}</span>
            </div>
        @endforeach
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Please come again</p>
    </div>
</body>

</html>
