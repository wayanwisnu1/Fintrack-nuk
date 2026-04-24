<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class TransactionsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Transaction([
            'title'       => $row['judul'],
            'type'        => strtolower($row['tipe']),
            'category'    => strtolower($row['kategori']),
            'amount'      => $row['jumlah'],
            'date'        => $this->transformDate($row['tanggal']),
            'description' => $row['deskripsi'] ?? null,
        ]);
    }

    /**
     * Memastikan format tanggal benar
     */
    private function transformDate($value)
    {
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return now();
        }
    }
}
