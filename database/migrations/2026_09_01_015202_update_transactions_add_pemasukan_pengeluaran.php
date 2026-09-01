<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambahkan dua kolom baru
            $table->integer('pemasukan')->nullable()->after('title');
            $table->integer('pengeluaran')->nullable()->after('pemasukan');

            // Hapus kolom lama (opsional, tapi sebaiknya dihapus agar tidak bingung)
            $table->dropColumn(['type', 'amount']);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['pemasukan', 'pengeluaran']);
            $table->enum('type', ['pemasukan', 'pengeluaran'])->after('title');
            $table->integer('amount')->after('type');
        });
    }
};
