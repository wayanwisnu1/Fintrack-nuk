<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Fintrack</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .income { color: green; }
        .expense { color: red; }
        .footer { margin-top: 30px; text-align: right; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Transaksi Fintrack</h2>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $totalIncome = 0; $totalExpense = 0; @endphp
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                <td>{{ $transaction->title }}</td>
                <td>{{ ucfirst($transaction->category) }}</td>
                <td>
                    <span class="{{ $transaction->type == 'income' ? 'income' : 'expense' }}">
                        {{ ucfirst($transaction->type) }}
                    </span>
                </td>
                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            @php
                if($transaction->type == 'income') $totalIncome += $transaction->amount;
                else $totalExpense += $transaction->amount;
            @endphp
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Pemasukan: Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        <p>Total Pengeluaran: Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        <p><strong>Saldo Bersih: Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</strong></p>
    </div>
</body>
</html>
