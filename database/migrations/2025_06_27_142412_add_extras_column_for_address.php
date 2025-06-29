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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('destination_id')->nullable()->after('user_id');
            $table->string('destination_name')->nullable()->after('destination_id');

            $table->string('province_id')->nullable()->change();
            $table->string('city_id')->nullable()->change();

            $table->index('destination_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex(['destination_id']);
            $table->dropColumn(['destination_id', 'destination_name']);

        });
    }
};
