<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Enumerable; // <-- added

class TransactionExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Enumerable
    {
        $query = Transaction::query();

        if (!empty($this->filters['month'])) {
            $query->whereMonth('date', $this->filters['month']);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Judul',
            'Pemasukan',
            'Pengeluaran',
            'Pegangan',
            'Save',
            'Catatan',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->date->format('d M Y'),
            $transaction->title,
            number_format($transaction->pemasukan ?? 0, 0, ',', '.'),
            number_format($transaction->pengeluaran ?? 0, 0, ',', '.'),
            $transaction->pegangan ?? '',
            $transaction->save ?? '',
            $transaction->description ?? '',
        ];
    }
}
