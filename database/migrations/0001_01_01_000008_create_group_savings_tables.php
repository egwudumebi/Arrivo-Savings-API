<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_savings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('balance', 18, 2)->default(0);
            $table->decimal('target_amount', 18, 2)->nullable();
            $table->string('currency', 3)->default('NGN');
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['creator_id', 'status']);
        });

        Schema::create('group_savings_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('group_savings_id')->constrained('group_savings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['group_savings_id', 'user_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_savings_members');
        Schema::dropIfExists('group_savings');
    }
};
