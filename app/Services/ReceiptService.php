<?php

declare(strict_types=1);

namespace Modules\POS\Services;

use Exception;
use Modules\Order\Models\Order;
use Modules\POS\Models\Printer;

final class ReceiptService
{
    /**
     * Generate receipt HTML.
     */
    public function generateReceiptHTML(Order $order): string
    {
        $order->load(['items', 'customer', 'payments']);

        return view('pos::receipts.default', [
            'order' => $order,
            'business' => $this->getBusinessInfo(),
            'date' => now()->format('M d, Y H:i'),
        ])->render();
    }

    /**
     * Generate receipt for printing (ESC/POS format).
     */
    public function generateReceiptESCPOS(Order $order): string
    {
        // ESC/POS commands for thermal printers
        $esc = "\x1B";
        $init = $esc . '@'; // Initialize printer
        $center = $esc . 'a' . chr(1); // Center align
        $left = $esc . 'a' . chr(0); // Left align
        $bold = $esc . 'E' . chr(1); // Bold on
        $boldOff = $esc . 'E' . chr(0); // Bold off
        $cut = $esc . 'i'; // Cut paper

        $receipt = $init;
        $receipt .= $center . $bold;
        $receipt .= config('app.name') . "\n";
        $receipt .= $boldOff;
        $receipt .= "Thank you for your purchase\n";
        $receipt .= str_repeat('-', 32) . "\n";
        $receipt .= $left;
        $receipt .= "Order: {$order->order_number}\n";
        $receipt .= 'Date: ' . now()->format('M d, Y H:i') . "\n";
        $receipt .= str_repeat('-', 32) . "\n\n";

        // Items
        foreach ($order->items as $item) {
            $receipt .= $this->formatLine($item->name, $item->quantity, $item->total);
        }

        $receipt .= str_repeat('-', 32) . "\n";
        $receipt .= $bold;
        $receipt .= $this->formatLine('TOTAL', '', $order->total);
        $receipt .= $boldOff;
        $receipt .= "\n\n";

        // Payment
        foreach ($order->payments as $payment) {
            $receipt .= 'Paid: ' . mb_strtoupper($payment->payment_method) . ' - $' . number_format($payment->amount, 2) . "\n";
        }

        $receipt .= "\n" . $center;
        $receipt .= "Thank you! Come again!\n\n\n";
        $receipt .= $cut;

        return $receipt;
    }

    /**
     * Send receipt to printer.
     */
    public function print(Order $order, Printer $printer): bool
    {
        $receiptData = $this->generateReceiptESCPOS($order);

        // TODO: Implement actual printer communication
        // For network printers, send to IP:PORT
        // For USB printers, write to device path
        // This is a placeholder

        if ('network' === $printer->connection_type && $printer->ip_address) {
            return $this->sendToNetworkPrinter($printer->ip_address, $printer->port, $receiptData);
        }

        return true;
    }

    /**
     * Format line for receipt.
     */
    private function formatLine(string $name, $qty, float $price): string
    {
        $maxNameLength = 20;
        $name = mb_substr($name, 0, $maxNameLength);

        $line = mb_str_pad($name, $maxNameLength);

        if ($qty) {
            $line .= ' x' . $qty;
        }

        $line .= mb_str_pad('$' . number_format($price, 2), 10, ' ', STR_PAD_LEFT);
        $line .= "\n";

        return $line;
    }

    /**
     * Send data to network printer.
     */
    private function sendToNetworkPrinter(string $ip, int $port, string $data): bool
    {
        try {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if (false === $socket || ! socket_connect($socket, $ip, $port)) {
                return false;
            }

            socket_write($socket, $data, mb_strlen($data));
            socket_close($socket);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get business information.
     */
    private function getBusinessInfo(): array
    {
        return [
            'name' => config('app.name'),
            'address' => config('app.address', ''),
            'phone' => config('app.phone', ''),
            'email' => config('app.email', ''),
        ];
    }
}
