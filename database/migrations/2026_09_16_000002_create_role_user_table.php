<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('role_id')->constrained('role')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->primary(['user_id', 'role_id']);
        });

        $now = now();
        DB::table('users')->select(['id', 'role_id'])->orderBy('id')->each(function ($user) use ($now) {
            DB::table('role_user')->insertOrIgnore([
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
