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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departemen_id')->constrained('departemens')->cascadeOnDelete();
            $table->foreignId('dosen_wali_id')->nullable()->constrained('dosens')->nullOnDelete();
            $table->string('nrp', 20)->unique()->index();
            $table->string('nama', 150)->index();
            $table->string('email', 150)->unique();
            $table->unsignedSmallInteger('angkatan')->default(2024);
            $table->decimal('ipk', 3, 2)->default(3.50);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
