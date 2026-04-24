@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Transaksi</h1>
            <p class="page-subtitle">Perbarui detail transaksi</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <div style="max-width: 640px;">
        <div class="card">
            <!-- Type Toggle -->
            <div style="display: flex; gap: 10px; margin-bottom: 28px;">
                <button type="button" id="btn-income"
                    class="btn {{ old('type', $transaction->type) === 'income' ? 'btn-income' : 'btn-ghost' }}"
                    style="flex: 1; justify-content: center;" onclick="setType('income')">
                    ↑ Pemasukan
                </button>
                <button type="button" id="btn-expense"
                    class="btn {{ old('type', $transaction->type) === 'expense' ? 'btn-expense' : 'btn-ghost' }}"
                    style="flex: 1; justify-content: center;" onclick="setType('expense')">
                    ↓ Pengeluaran
                </button>
            </div>

            <form method="POST" action="{{ route('transactions.update', $transaction) }}">
                @csrf @method('PUT')
                <input type="hidden" name="type" id="type-input" value="{{ old('type', $transaction->type) }}">

                <div class="form-group">
                    <label class="form-label">Judul Transaksi *</label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $transaction->title) }}" required>
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Jumlah (Rp) *</label>
                        <input type="number" name="amount" class="form-control"
                            value="{{ old('amount', $transaction->amount) }}" min="1" required>
                        @error('amount')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal *</label>
                        <input type="date" name="date" class="form-control"
                            value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required>
                        @error('date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group" id="income-categories"
                    style="{{ old('type', $transaction->type) === 'expense' ? 'display:none' : '' }}">
                    <label class="form-label">Kategori *</label>
                    <select name="category" class="form-control" id="income-cat">
                        <option value="">Pilih kategori...</option>
                        @foreach ($incomeCategories as $val => $label)
                            <option value="{{ $val }}"
                                {{ old('category', $transaction->category) === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="expense-categories"
                    style="{{ old('type', $transaction->type) !== 'expense' ? 'display:none' : '' }}">
                    <label class="form-label">Kategori *</label>
                    <select name="category" class="form-control" id="expense-cat">
                        <option value="">Pilih kategori...</option>
                        @foreach ($expenseCategories as $val => $label)
                            <option value="{{ $val }}"
                                {{ old('category', $transaction->category) === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @error('category')
                    <div class="form-error" style="margin-top: -12px; margin-bottom: 16px;">{{ $message }}</div>
                @enderror

                <div class="form-group">
                    <label class="form-label">Deskripsi (opsional)</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $transaction->description) }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 10px; margin-top: 8px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                        💾 Simpan Perubahan
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setType(type) {
            document.getElementById('type-input').value = type;
            const btnIncome = document.getElementById('btn-income');
            const btnExpense = document.getElementById('btn-expense');
            const incCat = document.getElementById('income-categories');
            const expCat = document.getElementById('expense-categories');
            const incSelect = document.getElementById('income-cat');
            const expSelect = document.getElementById('expense-cat');

            if (type === 'income') {
                btnIncome.className = 'btn btn-income';
                btnExpense.className = 'btn btn-ghost';
                incCat.style.display = '';
                expCat.style.display = 'none';
                
                // Aktifkan name untuk income, matikan untuk expense
                incSelect.setAttribute('name', 'category');
                expSelect.removeAttribute('name');
            } else {
                btnExpense.className = 'btn btn-expense';
                btnIncome.className = 'btn btn-ghost';
                expCat.style.display = '';
                incCat.style.display = 'none';
                
                // Aktifkan name untuk expense, matikan untuk income
                expSelect.setAttribute('name', 'category');
                incSelect.removeAttribute('name');
            }
            [btnIncome, btnExpense].forEach(b => {
                b.style.flex = '1';
                b.style.justifyContent = 'center';
            });
        }
        setType(document.getElementById('type-input').value);
    </script>
@endpush
