<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\POS\Models\Printer;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasTable((new Printer())->getTable())) {
            Schema::create((new Printer())->getTable(), function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();

                // Terminal reference
                $table->foreignId('terminal_id')->constrained('pos_terminals')->cascadeOnDelete();

                // Printer details
                $table->string('name');
                $table->enum('type', ['receipt', 'kitchen', 'label', 'report'])->default('receipt');
                $table->string('connection_type')->default('network'); // network, usb, bluetooth
                $table->string('ip_address')->nullable();
                $table->integer('port')->nullable()->default(9100);
                $table->string('device_path')->nullable(); // For USB/serial printers

                // Settings
                $table->integer('paper_width')->default(80); // mm
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->json('settings')->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('terminal_id', 'pos_printers_terminal_id_index');
                $table->index('type', 'pos_printers_type_index');
                $table->index('is_default', 'pos_printers_is_default_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Printer())->getTable());
    }
};
