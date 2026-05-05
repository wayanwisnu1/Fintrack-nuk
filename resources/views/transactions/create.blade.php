@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Transaksi</h1>
            <p class="page-subtitle">Catat pemasukan atau pengeluaranmu</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-ghost">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px; margin-right: 6px;"></i> Kembali
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 640px 1fr; gap: 28px; align-items: start;">
        <div class="card">
            <!-- Type Toggle -->
            <div style="display: flex; gap: 10px; margin-bottom: 28px;">
                <button type="button" id="btn-income"
                    class="btn {{ old('type', request('type', 'income')) === 'income' ? 'btn-income' : 'btn-ghost' }}"
                    style="flex: 1; justify-content: center; display: flex; align-items: center; gap: 8px;" onclick="setType('income')">
                    <i data-lucide="arrow-up-circle" style="width: 18px; height: 18px;"></i> Pemasukan
                </button>
                <button type="button" id="btn-expense"
                    class="btn {{ old('type', request('type')) === 'expense' ? 'btn-expense' : 'btn-ghost' }}"
                    style="flex: 1; justify-content: center; display: flex; align-items: center; gap: 8px;" onclick="setType('expense')">
                    <i data-lucide="arrow-down-circle" style="width: 18px; height: 18px;"></i> Pengeluaran
                </button>
            </div>

            <form method="POST" action="{{ route('transactions.store') }}">
                @csrf
                <input type="hidden" name="type" id="type-input" value="{{ old('type', request('type', 'income')) }}">

                <div class="form-group">
                    <label class="form-label">Judul Transaksi *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                        placeholder="contoh: Gaji Maret, Beli Makan Siang...">
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Jumlah (Rp) *</label>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount') }}"
                            placeholder="500000" min="1">
                        @error('amount')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal *</label>
                        <input type="date" name="date" class="form-control"
                            value="{{ old('date', now()->format('Y-m-d')) }}">
                        @error('date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Category - Single select, options diganti JS -->
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="category" class="form-control" id="category-select" required>
                        <option value="">Pilih kategori...</option>
                    </select>
                    @error('category')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Data kategori untuk JS --}}
                @php
                    $oldCategory = old('category', '');
                @endphp
                <script>
                    const incomeCategories = @json($incomeCategories);
                    const expenseCategories = @json($expenseCategories);
                    const oldCategory = "{{ $oldCategory }}";
                </script>

                <div class="form-group">
                    <label class="form-label">Deskripsi (opsional)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Catatan tambahan...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 10px; margin-top: 8px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i> Simpan Transaksi
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>

        <!-- Panel Motivasi Keuangan -->
        <div>
            <!-- Quote utama -->
            <div class="card"
                style="margin-bottom: 16px; border-color: rgba(108,141,250,0.25); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 10px; right: 10px; opacity: 0.1;">
                    <i data-lucide="quote" style="width: 60px; height: 60px;"></i>
                </div>
                <div
                    style="font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--accent); margin-bottom: 14px;">
                    ✦ Motivasi Hari Ini</div>
                <div id="quote-text"
                    style="font-family: var(--font-head); font-size: 17px; font-weight: 700; line-height: 1.5; color: var(--text); margin-bottom: 10px;">
                    "Keuangan yang sehat dimulai dari satu catatan kecil."
                </div>
                <div id="quote-author" style="font-size: 12px; color: var(--muted); font-style: italic;">— FinTrack</div>
                <button onclick="gantiQuote()"
                    style="margin-top: 16px; background: none; border: 1px solid var(--border); border-radius: 8px; color: var(--muted); font-size: 12px; padding: 6px 12px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px;"
                    onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
                    <i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i> Ganti Quote
                </button>
                <!-- Progress bar countdown 30 detik -->
                <div style="margin-top: 14px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 10px; color: var(--muted);">Ganti otomatis dalam</span>
                        <span id="countdown-text"
                            style="font-size: 10px; color: var(--accent); font-weight: 600;">30d</span>
                    </div>
                    <div style="height: 3px; background: var(--border); border-radius: 99px; overflow: hidden;">
                        <div id="progress-bar"
                            style="height: 100%; width: 100%; background: linear-gradient(90deg, var(--accent), #9b5de5); border-radius: 99px; transition: width 1s linear;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips singkat -->
            <div class="card" style="margin-bottom: 16px; border-color: rgba(34,212,124,0.2);">
                <div
                    style="font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--income); margin-bottom: 14px;">
                    💡 Tips Keuangan</div>
                <div style="display: flex; flex-direction: column; gap: 12px;" id="tips-list">
                    <div style="display: flex; gap: 10px; align-items: flex-start;">
                        <i data-lucide="target" style="width: 18px; height: 18px; color: var(--income); flex-shrink: 0;"></i>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.5;">Terapkan aturan
                            <strong>50/30/20</strong> — 50% kebutuhan, 30% keinginan, 20% tabungan.</div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: flex-start;">
                        <i data-lucide="pie-chart" style="width: 18px; height: 18px; color: var(--income); flex-shrink: 0;"></i>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.5;">Catat <strong>setiap
                                pengeluaran</strong>, sekecil apapun. Kopi 15rb sehari = 450rb sebulan!</div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: flex-start;">
                        <i data-lucide="rocket" style="width: 18px; height: 18px; color: var(--income); flex-shrink: 0;"></i>
                        <div style="font-size: 13px; color: var(--text); line-height: 1.5;">Bayar dirimu sendiri dulu —
                            <strong>sisihkan tabungan</strong> sebelum belanja yang lain.</div>
                    </div>
                </div>
            </div>

            <!-- Statistik mini -->
            <div class="card" style="border-color: rgba(247,183,49,0.2);">
                <div
                    style="font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--accent2); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="trending-up" style="width: 14px; height: 14px;"></i> Fakta Menarik
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div
                        style="background: var(--bg); border-radius: 8px; padding: 12px; border: 1px solid var(--border);">
                        <div style="font-size: 11px; color: var(--muted); margin-bottom: 4px;">Jika hemat Rp 10.000/hari
                        </div>
                        <div
                            style="font-family: var(--font-head); font-size: 18px; font-weight: 800; color: var(--income);">
                            Rp 3.650.000</div>
                        <div style="font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 4px;">terkumpul dalam setahun <i data-lucide="party-popper" style="width: 12px; height: 12px;"></i></div>
                    </div>
                    <div
                        style="background: var(--bg); border-radius: 8px; padding: 12px; border: 1px solid var(--border);">
                        <div style="font-size: 11px; color: var(--muted); margin-bottom: 4px;">Jika hemat Rp 50.000/hari
                        </div>
                        <div
                            style="font-family: var(--font-head); font-size: 18px; font-weight: 800; color: var(--income);">
                            Rp 18.250.000</div>
                        <div style="font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 4px;">terkumpul dalam setahun <i data-lucide="rocket" style="width: 12px; height: 12px;"></i></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        function setType(type) {
            document.getElementById('type-input').value = type;

            const btnIncome = document.getElementById('btn-income');
            const btnExpense = document.getElementById('btn-expense');
            const select = document.getElementById('category-select');

            // Ganti style tombol
            if (type === 'income') {
                btnIncome.className = 'btn btn-income';
                btnExpense.className = 'btn btn-ghost';
            } else {
                btnExpense.className = 'btn btn-expense';
                btnIncome.className = 'btn btn-ghost';
            }
            [btnIncome, btnExpense].forEach(b => {
                b.style.flex = '1';
                b.style.justifyContent = 'center';
            });

            // Ganti options kategori sesuai tipe
            const categories = type === 'income' ? incomeCategories : expenseCategories;
            select.innerHTML = '<option value="">Pilih kategori...</option>';
            Object.entries(categories).forEach(([val, label]) => {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = label;
                if (val === oldCategory) opt.selected = true;
                select.appendChild(opt);
            });
        }

        // Init saat halaman load
        setType(document.getElementById('type-input').value || 'income');

        // === Quote Motivasi ===
        const quotes = [{
                text: "Keuangan yang sehat dimulai dari satu catatan kecil.",
                author: "FinTrack"
            },
            {
                text: "Jangan menabung apa yang tersisa setelah belanja, tapi belanjalah apa yang tersisa setelah menabung.",
                author: "Warren Buffett"
            },
            {
                text: "Investasi terbaik yang bisa kamu lakukan adalah pada dirimu sendiri.",
                author: "Warren Buffett"
            },
            {
                text: "Bukan berapa banyak yang kamu hasilkan, tapi berapa banyak yang kamu simpan.",
                author: "Robert Kiyosaki"
            },
            {
                text: "Orang kaya membeli aset. Orang miskin hanya punya pengeluaran. Orang menengah membeli liabilitas.",
                author: "Robert Kiyosaki"
            },
            {
                text: "Kebebasan finansial bukan tentang menjadi kaya, tapi tentang punya pilihan.",
                author: "T. Harv Eker"
            },
            {
                text: "Setiap rupiah yang kamu catat adalah langkah menuju kebebasan finansialmu.",
                author: "FinTrack"
            },
            {
                text: "Disiplin keuangan hari ini adalah kemewahan yang kamu nikmati di masa depan.",
                author: "FinTrack"
            },
            {
                text: "Kamu tidak perlu penghasilan besar untuk kaya. Kamu perlu kebiasaan yang benar.",
                author: "Dave Ramsey"
            },
            {
                text: "Anggaran bukan tentang membatasi hidupmu, tapi tentang membuat hidupmu lebih bebas.",
                author: "Dave Ramsey"
            },
            {
                text: "Satu-satunya cara untuk menjadi kaya adalah mengeluarkan lebih sedikit dari yang kamu hasilkan.",
                author: "Benjamin Franklin"
            },
            {
                text: "Tujuan finansial tanpa rencana hanyalah angan-angan.",
                author: "FinTrack"
            },
        ];

        let currentQuoteIndex = Math.floor(Math.random() * quotes.length);

        function tampilkanQuote(index) {
            const q = quotes[index];
            const textEl = document.getElementById('quote-text');
            const authorEl = document.getElementById('quote-author');
            textEl.style.opacity = '0';
            authorEl.style.opacity = '0';
            setTimeout(() => {
                textEl.textContent = `"${q.text}"`;
                authorEl.textContent = `— ${q.author}`;
                textEl.style.transition = 'opacity 0.4s';
                authorEl.style.transition = 'opacity 0.4s';
                textEl.style.opacity = '1';
                authorEl.style.opacity = '1';
            }, 200);
        }

        function gantiQuote() {
            currentQuoteIndex = (currentQuoteIndex + 1) % quotes.length;
            tampilkanQuote(currentQuoteIndex);
        }

        // Tampilkan quote random saat load
        tampilkanQuote(currentQuoteIndex);

        // === Progress Bar & Countdown ===
        const INTERVAL = 30; // detik
        let secondsLeft = INTERVAL;

        function resetCountdown() {
            secondsLeft = INTERVAL;
        }

        function updateProgressBar() {
            const pct = (secondsLeft / INTERVAL) * 100;
            const bar = document.getElementById('progress-bar');
            const txt = document.getElementById('countdown-text');
            if (bar) bar.style.width = pct + '%';
            if (txt) txt.textContent = secondsLeft + 'd';
        }

        // Tick setiap 1 detik
        setInterval(() => {
            secondsLeft--;
            if (secondsLeft <= 0) {
                currentQuoteIndex = (currentQuoteIndex + 1) % quotes.length;
                tampilkanQuote(currentQuoteIndex);
                secondsLeft = INTERVAL;
            }
            updateProgressBar();
        }, 1000);

        // Saat tombol manual diklik, reset juga countdown-nya
        const origGantiQuote = gantiQuote;
        window.gantiQuote = function() {
            currentQuoteIndex = (currentQuoteIndex + 1) % quotes.length;
            tampilkanQuote(currentQuoteIndex);
            resetCountdown();
            updateProgressBar();
        };

        updateProgressBar();
    </script>
@endpush
