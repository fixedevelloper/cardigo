<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->float('charge')->default(0.0);
            $table->float('amount')->default(0.0);
            $table->float('total')->default(0.0);
            $table->string("method")->nullable();
            $table->string("status")->nullable();
            $table->string("phone")->nullable();
            $table->string("order_key")->nullable();
            $table->string('type')->nullable()->comment("recharge:transfert:deposit");
            $table->foreignId('card_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
