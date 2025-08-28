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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Add soft deletes
            $table->softDeletes();

            // Foreign key to organizations
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

            // Email unique per organization (case-insensitive)
            $table->unique(['organization_id', 'email']);

            // Index for performance
            $table->index(['organization_id', 'first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
