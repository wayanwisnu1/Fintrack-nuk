@extends('layouts.app')

@section('title', 'Target Keuangan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Target Keuangan</h1>
        <p class="page-subtitle">Wujudkan impianmu dengan menabung secara teratur</p>
    </div>
    <button onclick="document.getElementById('add-goal-form').style.display = 'block'" class="btn btn-primary">
        ＋ Buat Target Baru
    </button>
</div>

{{-- Form Tambah Target (Hidden by default) --}}
<div id="add-goal-form" class="card" style="display: none; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;">Buat Target Keuangan</h3>
        <button onclick="document.getElementById('add-goal-form').style.display = 'none'" class="btn btn-ghost btn-sm">×</button>
    </div>
    <form action="{{ route('goals.store') }}" method="POST">
        @csrf
        <div class="grid-2" style="margin-bottom: 16px;">
            <div>
                <label class="form-label">Nama Target</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Beli Laptop Baru" required>
            </div>
            <div>
                <label class="form-label">Nominal Target (Rp)</label>
                <input type="number" name="target_amount" class="form-control" placeholder="Contoh: 10000000" required>
            </div>
        </div>
        <div style="margin-bottom: 20px;">
            <label class="form-label">Catatan (Opsional)</label>
            <textarea name="note" class="form-control" rows="2"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Target</button>
    </form>
</div>

<div class="grid-2">
    @forelse($goals as $goal)
        <div class="card" style="position: relative; {{ $goal->status == 'completed' ? 'border: 1px solid var(--income);' : '' }}">
            @if($goal->status == 'completed')
                <div style="position: absolute; top: 12px; right: 12px;" class="badge badge-income">✨ Tercapai</div>
            @endif

            <h3 style="margin-top: 0; margin-bottom: 4px; font-size: 18px;">{{ $goal->name }}</h3>
            <p style="font-size: 12px; color: var(--muted); margin-bottom: 20px;">{{ $goal->note ?? 'Tidak ada catatan' }}</p>

            <div style="margin-bottom: 8px; display: flex; justify-content: space-between; font-size: 13px;">
                <span style="font-weight: 600; color: var(--accent);">
                    Rp {{ number_format($goal->balance, 0, ',', '.') }}
                </span>
                <span style="color: var(--muted);">
                    dari Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                </span>
            </div>

            {{-- Progress Bar --}}
            <div style="height: 12px; background: #eee; border-radius: 6px; overflow: hidden; margin-bottom: 12px;">
                <div style="height: 100%; width: {{ min($goal->percentage, 100) }}%; background: linear-gradient(90deg, var(--accent), #9b5de5); transition: width 0.5s;"></div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                <div style="font-size: 12px; font-weight: 600;">{{ number_format($goal->percentage, 1) }}% Terkumpul</div>
                
                <div style="display: flex; gap: 8px;">
                    @if($goal->status == 'active')
                        <button onclick="showDepositForm({{ $goal->id }})" class="btn btn-income btn-sm">
                            💰 Tabung
                        </button>
                    @endif
                    <form action="{{ route('goals.destroy', $goal) }}" method="POST" onsubmit="return confirm('Hapus target ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--expense);">Hapus</button>
                    </form>
                </div>
            </div>

            {{-- Deposit Form (Hidden) --}}
            <div id="deposit-form-{{ $goal->id }}" style="display: none; margin-top: 20px; padding-top: 16px; border-top: 1px dashed var(--border);">
                <form action="{{ route('goals.deposit', $goal) }}" method="POST">
                    @csrf
                    <div style="display: flex; gap: 10px;">
                        <input type="number" name="amount" class="form-control" placeholder="Masukkan jumlah..." required>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        <button type="button" onclick="document.getElementById('deposit-form-{{ $goal->id }}').style.display='none'" class="btn btn-ghost btn-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column: span 2; text-align: center; padding: 60px;">
            <p style="color: var(--muted);">Belum ada target keuangan. Yuk buat target pertamamu!</p>
        </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
    function showDepositForm(id) {
        // Sembunyikan form deposit lain jika ada
        document.querySelectorAll('[id^="deposit-form-"]').forEach(el => el.style.display = 'none');
        // Tampilkan yang dipilih
        document.getElementById('deposit-form-' + id).style.display = 'block';
    }
</script>
@endpush
