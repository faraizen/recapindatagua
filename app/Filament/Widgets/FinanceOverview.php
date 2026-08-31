<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $pemasukan = Transaction::where('type', 'pemasukan')->sum('amount');
        $pengeluaran = Transaction::where('type', 'pengeluaran')->sum('amount');
        $saldo = $pemasukan - $pengeluaran;

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($pemasukan, 0, ',', '.'))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($pengeluaran, 0, ',', '.'))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),
            Stat::make('Saldo', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->icon('heroicon-o-wallet')
                ->color($saldo >= 0 ? 'primary' : 'danger'),
        ];
    }
}
