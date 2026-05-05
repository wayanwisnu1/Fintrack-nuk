@inject('healthService', 'App\Services\FinancialHealthService')

@php
    $health = $healthService->getScore();
    $score = $health['total'];
    
    // Tentukan warna berdasarkan skor
    $color = '#ff5757'; // Merah (Kritis)
    if ($score >= 80) $color = '#22d47c'; // Hijau
    elseif ($score >= 60) $color = '#6c8dfa'; // Biru
    elseif ($score >= 40) $color = '#f7b731'; // Kuning
@endphp

<div class="card" style="margin-bottom: 24px; position: relative; overflow: hidden;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
        <div style="font-family: var(--font-head); font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="activity" style="width: 18px; height: 18px; color: {{ $color }};"></i> Financial Health Score
        </div>
        <div class="badge" style="background: {{ $color }}15; color: {{ $color }}; border: 1px solid {{ $color }}30;">
            {{ $health['rating'] }}
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 100px 1fr; gap: 24px; align-items: center;">
        {{-- Circular Progress --}}
        <div style="position: relative; width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
            <svg viewBox="0 0 36 36" style="width: 100px; height: 100px; transform: rotate(-90deg);">
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--border)" stroke-width="3" />
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="{{ $color }}" stroke-width="3" stroke-dasharray="{{ $score }}, 100" stroke-linecap="round" style="transition: stroke-dasharray 1s ease-out;" />
            </svg>
            <div style="position: absolute; font-family: var(--font-head); font-size: 24px; font-weight: 800; color: var(--text);">
                {{ $score }}
            </div>
        </div>

        {{-- Details & Advice --}}
        <div>
            <div style="font-size: 14px; color: var(--text); line-height: 1.5; margin-bottom: 12px; font-weight: 500;">
                {{ $health['advice'] }}
            </div>
            
            <div style="display: flex; gap: 12px;">
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--muted); text-transform: uppercase; margin-bottom: 4px; font-weight: 700;">
                        <span>Tabungan</span>
                        <span>{{ round($health['savings']) }}%</span>
                    </div>
                    <div style="height: 4px; background: var(--border); border-radius: 2px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $health['savings'] }}%; background: var(--income);"></div>
                    </div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--muted); text-transform: uppercase; margin-bottom: 4px; font-weight: 700;">
                        <span>Budget</span>
                        <span>{{ round($health['budget']) }}%</span>
                    </div>
                    <div style="height: 4px; background: var(--border); border-radius: 2px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $health['budget'] }}%; background: var(--accent);"></div>
                    </div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--muted); text-transform: uppercase; margin-bottom: 4px; font-weight: 700;">
                        <span>Target</span>
                        <span>{{ round($health['goals']) }}%</span>
                    </div>
                    <div style="height: 4px; background: var(--border); border-radius: 2px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $health['goals'] }}%; background: var(--accent2);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
