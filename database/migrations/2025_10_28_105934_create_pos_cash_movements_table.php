<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\POS\Models\CashMovement;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasTable((new CashMovement())->getTable())) {
            Schema::create((new CashMovement())->getTable(), function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();

                // Shift reference
                $table->foreignId('shift_id')->constrained('pos_shifts')->cascadeOnDelete();

                // Movement type
                $table->enum('type', [
                    'cash_in',
                    'cash_out',
                    'opening_balance',
                    'closing_balance',
                    'petty_cash',
                    'bank_deposit',
                    'adjustment',
                    'refund',
                ])->default('cash_in');

                // Amount
                $table->decimal('amount', 15, 2);

                // Payment method (optional - for specific tracking)
                $table->enum('payment_method', [
                    'cash',
                    'card',
                    'mobile_money',
                    'bank_transfer',
                    'other',
                ])->nullable();

                // Reason and notes
                $table->string('reason')->nullable();
                $table->text('notes')->nullable();

                // Reference (invoice number, receipt number, etc.)
                $table->string('reference')->nullable();

                // User tracking
                $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('shift_id', 'pos_cash_movements_shift_id_index');
                $table->index('type', 'pos_cash_movements_type_index');
                $table->index('created_at', 'pos_cash_movements_created_at_index');
                $table->index('performed_by', 'pos_cash_movements_performed_by_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new CashMovement())->getTable());
    }
};
