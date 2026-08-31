<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['pemasukan', 'pengeluaran'])->after('id');
            $table->string('title')->after('type');
            $table->bigInteger('amount')->after('title');
            $table->date('date')->after('amount');
            $table->text('description')->nullable()->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['type', 'title', 'amount', 'date', 'description']);
        });
    }
};
