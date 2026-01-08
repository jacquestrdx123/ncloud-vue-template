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
        Schema::create('climatology_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month'); // 1-12
            $table->decimal('average_rain', 8, 2)->default(0);
            $table->decimal('ari_wet_100yr', 8, 2)->default(0);
            $table->decimal('ari_dry_100yr', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['customer_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('climatology_references');
    }
};
