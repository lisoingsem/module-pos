<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\POS\Models\Shift;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasTable((new Shift())->getTable())) {
            Schema::create((new Shift())->getTable(), function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();

                // Polymorphic shiftable (User, Team, etc.)
                $table->nullableMorphs('shiftable');

                // Terminal reference
                $table->foreignId('terminal_id')->constrained('pos_terminals')->cascadeOnDelete();

                // Shift status
                $table->enum('status', ['open', 'closed', 'suspended'])->default('open');

                // Cash tracking
                $table->decimal('opening_cash', 15, 2)->default(0);
                $table->decimal('closing_cash', 15, 2)->nullable();
                $table->decimal('expected_cash', 15, 2)->nullable();
                $table->decimal('actual_cash', 15, 2)->nullable();
                $table->decimal('difference', 15, 2)->nullable();

                // Sales summaries
                $table->decimal('total_sales', 15, 2)->default(0);
                $table->decimal('total_refunds', 15, 2)->default(0);
                $table->integer('total_transactions')->default(0);

                // Timestamps
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('closed_at')->nullable();

                // User tracking
                $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();

                // Notes
                $table->text('notes')->nullable();
                $table->json('settings')->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index(['shiftable_type', 'shiftable_id'], 'pos_shifts_shiftable_index');
                $table->index('terminal_id', 'pos_shifts_terminal_id_index');
                $table->index('status', 'pos_shifts_status_index');
                $table->index('opened_at', 'pos_shifts_opened_at_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Shift())->getTable());
    }
};
