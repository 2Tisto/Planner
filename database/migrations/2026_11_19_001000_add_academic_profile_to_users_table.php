<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('filiere', 120)->nullable();
            $table->string('niveau', 20)->nullable();
            $table->string('semestre', 32)->nullable();
            $table->string('timezone', 64)->default('Africa/Porto-Novo');
            $table->string('locale', 5)->default('fr');
            $table->unsignedTinyInteger('week_starts_on')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'filiere',
                'niveau',
                'semestre',
                'timezone',
                'locale',
                'week_starts_on',
            ]);
        });
    }
};
