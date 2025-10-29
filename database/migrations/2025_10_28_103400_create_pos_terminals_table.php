<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\POS\Models\Terminal;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasTable((new Terminal())->getTable())) {
            Schema::create((new Terminal())->getTable(), function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();

                // Polymorphic owner (Warehouse, User, Store, Branch, etc.)
                $table->nullableMorphs('terminalable');

                // Terminal details
                $table->string('name');
                $table->string('code')->unique();
                $table->string('location')->nullable();
                $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');

                // Settings
                $table->json('settings')->nullable();

                // Tracking
                $table->timestamp('last_used_at')->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('code', 'pos_terminals_code_index');
                $table->index('status', 'pos_terminals_status_index');
                $table->index(['terminalable_type', 'terminalable_id'], 'pos_terminals_terminalable_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Terminal())->getTable());
    }
};
