<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visibility_settings', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('visibility_level', ['all', 'role_based', 'custom', 'private'])->default('role_based');
            $table->json('visible_to_roles')->nullable(); // ['admin', 'moderator']
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->unique(['model_type', 'model_id']);
            $table->index('visibility_level');
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visibility_settings');
    }
};
