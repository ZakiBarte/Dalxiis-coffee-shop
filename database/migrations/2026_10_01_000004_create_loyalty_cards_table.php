<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_cards', function (Blueprint $table) {
            $table->id();
            // unique() makes this a true one-to-one relationship
            $table->foreignId('customer_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('card_number')->unique();
            $table->string('tier')->default('bronze');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_cards');
    }
};
