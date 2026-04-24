<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Transaction::all();
    }

    /**
     * Header kolom di file Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'Tipe',
            'Kategori',
            'Jumlah',
            'Tanggal',
            'Deskripsi',
        ];
    }

    /**
     * Mapping data agar rapi di Excel
     */
    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->title,
            ucfirst($transaction->type),
            ucfirst($transaction->category),
            $transaction->amount,
            $transaction->date->format('d-m-Y'),
            $transaction->description,
        ];
    }
}
