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
        Schema::create('contact_metas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('contact_id')->constrained()->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value');
            $table->timestamps();
            
            // Ensure unique key per contact
            $table->unique(['contact_id', 'key']);
            $table->index(['contact_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_metas');
    }
};
