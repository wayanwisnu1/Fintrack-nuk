@extends('layouts.app')

@section('title', 'Semua Transaksi')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Transaksi</h1>
            <p class="page-subtitle">{{ $transactions->total() }} transaksi ditemukan</p>
        </div>
        <div style="display: flex; gap: 8px;">
            {{-- Tombol Export --}}
            <div style="display: flex; gap: 4px; border: 1px solid var(--border); padding: 4px; border-radius: 8px;">
                <a href="{{ route('transactions.export.excel') }}" class="btn btn-ghost btn-sm" title="Export ke Excel" style="display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px; color: #22d47c;"></i> Excel
                </a>
                <a href="{{ route('transactions.export.pdf') }}" class="btn btn-ghost btn-sm" title="Export ke PDF" style="display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px; color: #ff5757;"></i> PDF
                </a>
            </div>
            
            {{-- Tombol Import (Trigger Modal/Collapse) --}}
            <button onclick="document.getElementById('import-form-container').style.display = 'block'" class="btn btn-ghost btn-sm" style="display: flex; align-items: center; gap: 6px;">
                <i data-lucide="download" style="width: 14px; height: 14px;"></i> Import
            </button>

            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Tambah
            </a>
        </div>
    </div>

    {{-- Form Import (Hidden by default) --}}
    <div id="import-form-container" class="card" style="display: none; margin-bottom: 24px; border: 2px dashed var(--border);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 style="margin: 0; font-size: 14px;">Import Data Transaksi</h3>
            <button onclick="document.getElementById('import-form-container').style.display = 'none'" class="btn btn-ghost btn-sm">×</button>
        </div>
        <form action="{{ route('transactions.import') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 12px; align-items: center;">
            @csrf
            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
            <button type="submit" class="btn btn-primary btn-sm">Upload & Proses</button>
        </form>
        <p style="font-size: 11px; color: var(--muted); margin-top: 8px;">
            * Pastikan header kolom sesuai: <strong>judul, tipe, kategori, jumlah, tanggal, deskripsi</strong>.
        </p>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 24px;">

        {{-- Tab pilih mode filter waktu --}}
        @php
            $filterMode = request()->filled('date') ? 'date' : (request()->filled('year') ? 'year' : 'month');
        @endphp
        <div
            style="display: flex; gap: 6px; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
            <button type="button" onclick="setFilterMode('month')" id="tab-month"
                class="btn btn-sm {{ $filterMode === 'month' ? 'btn-primary' : 'btn-ghost' }}">
                Bulan
            </button>
            <button type="button" onclick="setFilterMode('date')" id="tab-date"
                class="btn btn-sm {{ $filterMode === 'date' ? 'btn-primary' : 'btn-ghost' }}">
                Tanggal
            </button>
            <button type="button" onclick="setFilterMode('year')" id="tab-year"
                class="btn btn-sm {{ $filterMode === 'year' ? 'btn-primary' : 'btn-ghost' }}">
                Tahun
            </button>
        </div>

        <form method="GET" action="{{ route('transactions.index') }}" id="filter-form">
            <div style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">

                {{-- Filter Tipe --}}
                <div>
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-control" style="width: 150px;">
                        <option value="">Semua Tipe</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>

                {{-- Filter Bulan --}}
                <div id="field-month" style="display: {{ $filterMode === 'month' ? 'block' : 'none' }}">
                    <label class="form-label">Bulan & Tahun</label>
                    <input type="month" name="month" class="form-control"
                        value="{{ request('month', now()->format('Y-m')) }}" style="width: 190px;">
                </div>

                {{-- Filter Tanggal Spesifik --}}
                <div id="field-date" style="display: {{ $filterMode === 'date' ? 'block' : 'none' }}">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}"
                        style="width: 190px;">
                </div>

                {{-- Filter Tahun --}}
                <div id="field-year" style="display: {{ $filterMode === 'year' ? 'block' : 'none' }}">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-control" style="width: 140px;">
                        <option value="">Semua Tahun</option>
                        @foreach ($availableYears as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol --}}
                <div style="display: flex; gap: 8px; margin-top: auto;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                </div>
            </div>
        </form>

        {{-- Ringkasan hasil filter --}}
        @if (request()->hasAny(['type', 'date', 'month', 'year', 'category']))
            <div
                style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border); display: flex; gap: 20px; flex-wrap: wrap;">
                <div style="font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 8px;">
                    Hasil filter:
                    <span style="color: var(--income); font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        <i data-lucide="arrow-up" style="width: 12px; height: 12px;"></i> Rp {{ number_format($filteredIncome, 0, ',', '.') }}
                    </span>
                    <span style="color: var(--expense); font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        <i data-lucide="arrow-down" style="width: 12px; height: 12px;"></i> Rp {{ number_format($filteredExpense, 0, ',', '.') }}
                    </span>
                    <span style="color: var(--text); font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        <i data-lucide="equals" style="width: 12px; height: 12px;"></i> Rp {{ number_format($filteredIncome - $filteredExpense, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Table -->
    <div class="card">
        @if ($transactions->isEmpty())
            <div style="text-align:center; padding: 48px; color: var(--muted);">
                <i data-lucide="box" style="width: 40px; height: 40px; margin: 0 auto 16px; display:block; opacity:0.3; stroke-width: 1.5;"></i>
                <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">Tidak ada transaksi</div>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary" style="margin-top: 12px;">
                    <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Tambah Transaksi
                </a>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Transaksi</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th style="text-align:right;">Jumlah</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $t)
                            <tr>
                                <td>
                                    <div style="font-weight: 500;">{{ $t->title }}</div>
                                    @if ($t->description)
                                        <div style="font-size: 12px; color: var(--muted);">
                                            {{ Str::limit($t->description, 50) }}</div>
                                    @endif
                                </td>
                                <td style="color: var(--muted); font-size: 13px;">{{ $t->category }}</td>
                                <td style="color: var(--muted); font-size: 13px;">{{ $t->date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $t->type }}">
                                        @if($t->type === 'income')
                                            <i data-lucide="arrow-up-circle" style="width: 12px; height: 12px; margin-right: 4px;"></i> Pemasukan
                                        @else
                                            <i data-lucide="arrow-down-circle" style="width: 12px; height: 12px; margin-right: 4px;"></i> Pengeluaran
                                        @endif
                                    </span>
                                </td>
                                <td style="text-align:right;" class="amount-{{ $t->type }}">
                                    {{ $t->type === 'income' ? '+' : '-' }}{{ $t->formatted_amount }}
                                </td>
                                <td style="text-align:center;">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <a href="{{ route('transactions.edit', $t) }}" class="btn btn-ghost btn-sm"
                                            title="Edit">
                                            <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i>
                                        </a>
                                        <form method="POST" action="{{ route('transactions.destroy', $t) }}"
                                            onsubmit="return confirm('Hapus transaksi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($transactions->hasPages())
                <div style="padding-top: 20px; border-top: 1px solid var(--border); margin-top: 8px;">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>

    @push('scripts')
        <script>
            // Initialize Lucide icons
            lucide.createIcons();

            const modes = ['month', 'date', 'year'];

            function setFilterMode(mode) {
                // Sembunyikan semua field waktu
                modes.forEach(m => {
                    document.getElementById('field-' + m).style.display = 'none';
                    document.getElementById('tab-' + m).className = 'btn btn-sm btn-ghost';
                    // Kosongkan value field yang tidak aktif agar tidak ikut terkirim
                    const el = document.getElementById('field-' + m).querySelector('input, select');
                    if (el && m !== mode) el.value = '';
                });

                // Tampilkan field yang dipilih
                document.getElementById('field-' + mode).style.display = 'block';
                document.getElementById('tab-' + mode).className = 'btn btn-sm btn-primary';
            }

            // Init dari server-side mode
            setFilterMode('{{ $filterMode ?? 'month' }}');
        </script>
    @endpush
@endsection
