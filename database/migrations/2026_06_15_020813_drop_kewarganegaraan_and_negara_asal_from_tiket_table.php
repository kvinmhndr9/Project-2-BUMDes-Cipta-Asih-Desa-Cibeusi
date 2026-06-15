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
        Schema::table('Tiket', function (Blueprint $table) {
            $table->dropColumn(['kewarganegaraan', 'negara_asal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Tiket', function (Blueprint $table) {
            $table->string('kewarganegaraan', 10)->nullable();
            $table->string('negara_asal', 100)->nullable();
        });
    }
};
