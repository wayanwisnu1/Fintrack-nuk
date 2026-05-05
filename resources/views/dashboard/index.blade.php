@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Ringkasan keuanganmu — {{ now()->translatedFormat('F Y') }}</p>
        </div>
        <div
            style="display: flex; align-items: center; gap: 12px; background: var(--surface); padding: 8px 16px; border-radius: 12px; border: 1px solid var(--border);">
            <div
                style="width: 32px; height: 32px; background: var(--accent); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: #fff;">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div style="font-weight: 600; font-size: 14px; color: var(--text);">{{ auth()->user()->name }}</div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid-3" style="margin-bottom: 24px;">
        <div class="card" style="border-color: rgba(108,141,250,0.3);">
            <div
                style="font-size: 12px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); margin-bottom: 12px;">
                Total Saldo</div>
            <div
                style="font-family: var(--font-head); font-size: 26px; font-weight: 800; color: {{ $balance >= 0 ? 'var(--income)' : 'var(--expense)' }};">
                {{ $balance >= 0 ? '' : '-' }}Rp {{ number_format(abs($balance), 0, ',', '.') }}
            </div>
            <div style="font-size: 12px; color: var(--muted); margin-top: 6px;">Saldo keseluruhan</div>
        </div>

        <div class="card" style="border-color: rgba(34,212,124,0.3);">
            <div
                style="font-size: 12px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="trending-up" style="width: 14px; height: 14px; color: var(--income);"></i> Total Pemasukan</div>
            <div style="font-family: var(--font-head); font-size: 26px; font-weight: 800; color: var(--income);">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </div>
            <div style="font-size: 12px; color: var(--muted); margin-top: 6px;">Bulan ini: Rp
                {{ number_format($monthIncome, 0, ',', '.') }}</div>
        </div>

        <div class="card" style="border-color: rgba(255,87,87,0.3);">
            <div
                style="font-size: 12px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="trending-down" style="width: 14px; height: 14px; color: var(--expense);"></i> Total Pengeluaran</div>
            <div style="font-family: var(--font-head); font-size: 26px; font-weight: 800; color: var(--expense);">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </div>
            <div style="font-size: 12px; color: var(--muted); margin-top: 6px;">Bulan ini: Rp
                {{ number_format($monthExpense, 0, ',', '.') }}</div>
        </div>
    </div>

    @include('components.forecasting-card')

    <!-- Charts -->
    <div class="grid-2" style="margin-bottom: 24px;">
        <!-- Bar Chart -->
        <div class="card">
            <div style="font-family: var(--font-head); font-size: 16px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="bar-chart-3" style="width: 18px; height: 18px; color: var(--accent);"></i> Tren 6 Bulan Terakhir
            </div>
            <canvas id="barChart" height="200"></canvas>
        </div>

        <!-- Expense Donut -->
        <div class="card">
            <div style="font-family: var(--font-head); font-size: 16px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="pie-chart" style="width: 18px; height: 18px; color: var(--accent2);"></i> Pengeluaran Bulan Ini
            </div>
            @if ($expenseByCategory->isEmpty())
                <div style="text-align:center; color: var(--muted); padding: 40px 0;">
                    Belum ada pengeluaran bulan ini
                </div>
            @else
                <canvas id="donutChart" height="200"></canvas>
            @endif
        </div>
        </div>

        <!-- Pengeluaran Harian -->
        <div class="card" style="margin-bottom: 24px; padding: 20px 0;">
        <div style="padding: 0 20px 15px; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-family: var(--font-head); font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="calendar" style="width: 18px; height: 18px; color: var(--accent);"></i> Pengeluaran Harian
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <form action="{{ route('dashboard') }}" method="GET" style="margin: 0;">
                    <select name="weeks" onchange="this.form.submit()" 
                        style="font-size: 12px; background: var(--bg); color: var(--text); border: 1px solid var(--border); border-radius: 8px; padding: 4px 8px; cursor: pointer; outline: none;">
                        <option value="2" {{ $weeksToDisplay == 2 ? 'selected' : '' }}>2 Minggu Terakhir</option>
                        <option value="4" {{ $weeksToDisplay == 4 ? 'selected' : '' }}>4 Minggu Terakhir</option>
                        <option value="8" {{ $weeksToDisplay == 8 ? 'selected' : '' }}>8 Minggu Terakhir</option>
                        <option value="12" {{ $weeksToDisplay == 12 ? 'selected' : '' }}>12 Minggu Terakhir</option>
                    </select>
                </form>
                <div style="font-size: 12px; color: var(--muted); display: flex; gap: 8px; align-items: center; border-left: 1px solid var(--border); padding-left: 12px;">
                    <span style="display: inline-block; width: 12px; height: 4px; background: var(--accent); border-radius: 2px; opacity: 0.5;"></span>
                    Geser
                </div>
            </div>
        </div>

        <div
            style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; padding-bottom: 10px;">
            @php
                $allDays = collect($weeklyData)->pluck('days')->flatten(1);
                $maxExpense = $allDays->max('expense') ?: 1;
            @endphp
            @foreach ($weeklyData as $week)
                <div style="flex: 0 0 100%; scroll-snap-align: start; padding: 0 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: var(--text);">{{ $week['week_label'] }}
                            </div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">{{ $week['range'] }}</div>
                        </div>
                        <div style="font-size: 13px; color: var(--muted);">
                            Total: <span style="color: var(--expense); font-weight: 600;">
                                Rp {{ number_format($week['total_expense'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Bar visual harian -->
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; margin-bottom: 10px;">
                        @foreach ($week['days'] as $day)
                            <div style="text-align: center; cursor: pointer;"
                                onclick="showDayDetail('{{ $day['full_date'] }}')">
                                {{-- Label hari --}}
                                <div
                                    style="font-size: 10px; font-weight: 600; color: {{ $day['is_today'] ? 'var(--accent)' : 'var(--muted)' }}; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $day['label'] }}
                                </div>

                                {{-- Bar container --}}
                                <div
                                    style="height: 60px; display: flex; align-items: flex-end; justify-content: center; position: relative;">
                                    @if ($day['is_future'])
                                        <div
                                            style="width: 100%; height: 4px; background: var(--border); border-radius: 4px; position: absolute; bottom: 0;">
                                        </div>
                                    @else
                                        @php
                                            $pct = $maxExpense > 0 ? ($day['expense'] / $maxExpense) * 100 : 0;
                                            $barH = max($pct * 0.6, $day['expense'] > 0 ? 6 : 2);
                                        @endphp
                                        <div style="
                                            width: 100%;
                                            height: {{ $barH }}px;
                                            background: {{ $day['is_today'] ? 'var(--accent)' : ($day['expense'] > 0 ? 'rgba(255,87,87,0.7)' : 'var(--border)') }};
                                            border-radius: 4px 4px 0 0;
                                            transition: all 0.3s;
                                            position: absolute;
                                            bottom: 0;
                                        "
                                            title="Rp {{ number_format($day['expense'], 0, ',', '.') }}"></div>
                                    @endif
                                </div>

                                {{-- Tanggal --}}
                                <div
                                    style="
                                    font-size: 12px;
                                    font-weight: {{ $day['is_today'] ? '700' : '400' }};
                                    color: {{ $day['is_today'] ? 'var(--text)' : 'var(--muted)' }};
                                    margin-top: 6px;
                                    background: {{ $day['is_today'] ? 'var(--accent)' : 'transparent' }};
                                    border-radius: 50%;
                                    width: 24px; height: 24px;
                                    display: flex; align-items: center; justify-content: center;
                                    margin: 6px auto 0;
                                ">
                                    {{ \Carbon\Carbon::parse($day['full_date'])->format('d') }}
                                </div>

                                {{-- Jumlah --}}
                                @if (!$day['is_future'])
                                    <div
                                        style="font-size: 9px; color: {{ $day['expense'] > 0 ? 'var(--expense)' : 'var(--muted)' }}; margin-top: 4px; font-weight: 500;">
                                        @if ($day['expense'] > 0)
                                            {{ number_format($day['expense'] / 1000, 0, ',', '.') }}rb
                                        @else
                                            —
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Detail per hari (collapsible) --}}
        <div id="day-detail-container" style="padding: 0 20px;">
            @foreach ($weeklyData as $week)
                @foreach ($week['days'] as $day)
                    <div id="day-{{ $day['full_date'] }}" class="day-detail" style="display: none;">
                        <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 10px;">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="font-weight: 600; font-size: 14px;">
                                    {{ $day['label'] }}, {{ \Carbon\Carbon::parse($day['full_date'])->format('d M Y') }}
                                    @if ($day['is_today'])
                                        <span class="badge"
                                            style="background: rgba(108,141,250,0.15); color: var(--accent); margin-left: 6px;">Hari
                                            ini</span>
                                    @endif
                                </div>
                                <div style="display: flex; gap: 16px; font-size: 13px;">
                                    <span>Masuk: <strong style="color: var(--income);">Rp
                                            {{ number_format($day['income'], 0, ',', '.') }}</strong></span>
                                    <span>Keluar: <strong style="color: var(--expense);">Rp
                                            {{ number_format($day['expense'], 0, ',', '.') }}</strong></span>
                                </div>
                            </div>

                            @if ($day['transactions']->isEmpty())
                                <div style="text-align: center; color: var(--muted); padding: 16px 0; font-size: 13px;">
                                    Tidak ada transaksi hari ini
                                </div>
                            @else
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    @foreach ($day['transactions'] as $t)
                                        <div
                                            style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: var(--bg); border-radius: 8px; border: 1px solid var(--border);">
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div
                                                    style="width: 8px; height: 8px; border-radius: 50%; background: {{ $t->type === 'income' ? 'var(--income)' : 'var(--expense)' }};">
                                                </div>
                                                <div>
                                                    <div style="font-size: 13px; font-weight: 500;">{{ $t->title }}
                                                    </div>
                                                    <div style="font-size: 11px; color: var(--muted);">{{ $t->category }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="amount-{{ $t->type }}" style="font-size: 13px;">
                                                {{ $t->type === 'income' ? '+' : '-' }}{{ $t->formatted_amount }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div style="font-family: var(--font-head); font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="history" style="width: 18px; height: 18px; color: var(--accent);"></i> Transaksi Terbaru
            </div>
            <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm">
                Lihat Semua <i data-lucide="chevron-right" style="width: 14px; height: 14px; margin-left: 4px;"></i>
            </a>
        </div>

        @if ($recentTransactions->isEmpty())
            <div style="text-align:center; color: var(--muted); padding: 32px 0;">
                Belum ada transaksi. <a href="{{ route('transactions.create') }}" style="color: var(--accent);">Tambahkan
                    sekarang!</a>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentTransactions as $t)
                            <tr>
                                <td>
                                    <div style="font-weight: 500;">{{ $t->title }}</div>
                                    @if ($t->description)
                                        <div style="font-size: 12px; color: var(--muted);">
                                            {{ Str::limit($t->description, 40) }}</div>
                                    @endif
                                </td>
                                <td style="color: var(--muted);">{{ $t->category }}</td>
                                <td style="color: var(--muted);">{{ $t->date->format('d M Y') }}</td>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        const chartData = @json($chartData);
        const labels = chartData.map(d => d.label);
        const incomes = chartData.map(d => d.income);
        const expenses = chartData.map(d => d.expense);

        Chart.defaults.color = '#6b7080';
        Chart.defaults.font.family = "'DM Sans', sans-serif";

        // Bar Chart
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                        label: 'Pemasukan',
                        data: incomes,
                        backgroundColor: 'rgba(34,212,124,0.7)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenses,
                        backgroundColor: 'rgba(255,87,87,0.7)',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255,255,255,0.04)'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255,255,255,0.04)'
                        },
                        ticks: {
                            callback: v => 'Rp ' + v.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });

        // Weekly daily detail toggle
        let activeDay = null;

        function showDayDetail(date) {
            // Tutup semua dulu
            document.querySelectorAll('.day-detail').forEach(el => el.style.display = 'none');

            if (activeDay === date) {
                // Klik hari yang sama = toggle tutup
                activeDay = null;
                return;
            }

            const el = document.getElementById('day-' + date);
            if (el) {
                el.style.display = 'block';
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
                activeDay = date;
            }
        }

        // Auto buka hari ini
        document.addEventListener('DOMContentLoaded', () => {
            const today = '{{ now()->toDateString() }}';
            showDayDetail(today);
        });

        @if (!$expenseByCategory->isEmpty())
            const expCategories = @json($expenseByCategory->pluck('category'));
            const expTotals = @json($expenseByCategory->pluck('total'));
            const palette = ['#ff5757', '#ff8c42', '#f7b731', '#6c8dfa', '#9b5de5', '#22d47c', '#00bcd4'];

            new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: expCategories,
                    datasets: [{
                        data: expTotals,
                        backgroundColor: palette,
                        borderWidth: 2,
                        borderColor: '#1c1e27',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        @endif
    </script>
@endpush
