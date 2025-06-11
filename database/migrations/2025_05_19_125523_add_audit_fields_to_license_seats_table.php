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
        Schema::table('license_seats', function (Blueprint $table) {
            $table->dateTime('last_audit_date')->nullable()->default(null);
            $table->date('next_audit_date')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_seats', function (Blueprint $table) {
            $table->dropColumn('last_audit_date');
            $table->dropColumn('next_audit_date');
        });
    }
};
