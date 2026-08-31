<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class FinanceChart extends ChartWidget
{
    protected ?string $heading = 'Pemasukan vs Pengeluaran (6 Bulan Terakhir)'; // ubah dari static ke non-static

    protected function getData(): array
    {
        $labels = $pemasukan = $pengeluaran = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');

            $pemasukan[] = Transaction::where('type', 'pemasukan')
                ->whereYear('date', $month->year)->whereMonth('date', $month->month)
                ->sum('amount');

            $pengeluaran[] = Transaction::where('type', 'pengeluaran')
                ->whereYear('date', $month->year)->whereMonth('date', $month->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                ['label' => 'Pemasukan', 'data' => $pemasukan, 'backgroundColor' => '#22c55e'],
                ['label' => 'Pengeluaran', 'data' => $pengeluaran, 'backgroundColor' => '#ef4444'],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
