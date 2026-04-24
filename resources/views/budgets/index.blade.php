@extends('layouts.app')

@section('title', 'Anggaran Mingguan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Anggaran Mingguan</h1>
        <p class="page-subtitle">Periode: {{ $startOfWeek->format('d M') }} — {{ $endOfWeek->format('d M Y') }}</p>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="{{ route('budgets.index') }}" style="display: flex; gap: 12px; align-items: flex-end;">
        <div>
            <label class="form-label">Pilih Minggu</label>
            <input type="week" name="filter_week" class="form-control" 
                value="{{ $year }}-W{{ str_pad($week, 2, '0', STR_PAD_LEFT) }}"
                onchange="const parts = this.value.split('-W'); window.location.href='{{ route('budgets.index') }}?week=' + parseInt(parts[1]) + '&year=' + parts[0]">
        </div>
        <div style="font-size: 13px; color: var(--muted); padding-bottom: 10px;">
            Minggu ke-{{ $week }}, {{ $year }}
        </div>
    </form>
</div>

<div class="grid" style="grid-template-columns: 1fr 2fr; gap: 24px;">
    <!-- Form Set Budget -->
    <div class="card">
        <h3 style="margin-top: 0; font-size: 16px;">Set Anggaran Minggu Ini</h3>
        <form action="{{ route('budgets.store') }}" method="POST">
            @csrf
            <input type="hidden" name="week" value="{{ $week }}">
            <input type="hidden" name="year" value="{{ $year }}">
            
            <div style="margin-bottom: 16px;">
                <label class="form-label">Kategori Pengeluaran</label>
                <select name="category" class="form-control" required>
                    @foreach($expenseCategories as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label class="form-label">Jumlah Anggaran (Rp)</label>
                <input type="number" name="amount" class="form-control" placeholder="Contoh: 500000" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Anggaran</button>
        </form>
    </div>

    <!-- Progress List -->
    <div class="card">
        <h3 style="margin-top: 0; font-size: 16px;">Progres Mingguan</h3>
        @if($budgets->isEmpty())
            <p style="color: var(--muted); text-align: center; padding: 40px;">Belum ada anggaran untuk minggu ini.</p>
        @else
            @foreach($budgets as $budget)
                @php
                    $spent = $actualSpending[$budget->category] ?? 0;
                    $percent = $budget->getPercentageAttribute($spent);
                    $remaining = $budget->getRemainingAttribute($spent);
                    $color = $percent > 100 ? 'var(--expense)' : ($percent > 80 ? '#f59e0b' : 'var(--income)');
                @endphp
                <div style="margin-bottom: 24px; position: relative;" class="budget-item">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; align-items: flex-start;">
                        <div>
                            <span style="font-weight: 500;">{{ $expenseCategories[$budget->category] ?? $budget->category }}</span>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                Rp {{ number_format($spent, 0, ',', '.') }} / Rp {{ number_format($budget->amount, 0, ',', '.') }}
                            </div>
                        </div>
                        
                        {{-- Action Buttons --}}
                        <div style="display: flex; gap: 4px;">
                            <button onclick="editBudget('{{ $budget->category }}', '{{ $budget->amount }}')" 
                                class="btn btn-ghost btn-sm" style="padding: 4px 8px; height: auto;" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Hapus anggaran kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px; height: auto;" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div style="height: 10px; background: #eee; border-radius: 5px; overflow: hidden;">
                        <div style="height: 100%; width: {{ min($percent, 100) }}%; background: {{ $color }}; transition: width 0.3s;"></div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 6px; font-size: 11px;">
                        <span style="color: {{ $color }}; font-weight: 600;">{{ number_format($percent, 1) }}% Terpakai</span>
                        <span style="color: {{ $remaining < 0 ? 'var(--expense)' : 'var(--muted)' }}">
                            {{ $remaining < 0 ? 'Over: Rp ' . number_format(abs($remaining), 0, ',', '.') : 'Sisa: Rp ' . number_format($remaining, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editBudget(category, amount) {
        // Cari form di sisi kiri
        const categorySelect = document.querySelector('select[name="category"]');
        const amountInput = document.querySelector('input[name="amount"]');
        
        if (categorySelect && amountInput) {
            categorySelect.value = category;
            amountInput.value = Math.round(amount);
            
            // Scroll ke form agar user sadar
            categorySelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Beri efek highlight sebentar
            categorySelect.style.borderColor = 'var(--accent)';
            amountInput.style.borderColor = 'var(--accent)';
            setTimeout(() => {
                categorySelect.style.borderColor = 'var(--border)';
                amountInput.style.borderColor = 'var(--border)';
            }, 1000);
        }
    }
</script>
@endpush
