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
        Schema::table('order_cancel', function (Blueprint $table) {
            $table->string('refundslip')->nullable()->after('status'); // Replace 'column_name' with an existing column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cancel', function (Blueprint $table) {
            $table->dropColumn('refundslip');
        });
    }
};
