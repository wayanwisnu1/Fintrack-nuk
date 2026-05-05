@inject('insightService', 'App\Services\InsightService')

@php
    $insights = $insightService->getInsights();
@endphp

<div style="margin-bottom: 24px;">
    <div style="font-family: var(--font-head); font-size: 14px; font-weight: 700; color: var(--muted); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
        <i data-lucide="sparkles" style="width: 14px; height: 14px; color: var(--accent);"></i> AI Smart Insights
    </div>
    
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @forelse($insights as $insight)
            <div class="card" style="padding: 20px; border-left: 4px solid {{ $insight['color'] }}; background: {{ $insight['color'] }}05; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                <div style="display: flex; gap: 18px; align-items: center;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--card); border: 1px solid {{ $insight['color'] }}20; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        <i data-lucide="{{ $insight['icon'] }}" style="width: 22px; height: 22px; color: {{ $insight['color'] }};"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px;">
                            {{ $insight['title'] }}
                        </div>
                        <div style="font-size: 13px; color: var(--muted); line-height: 1.5;">
                            {!! Str::markdown($insight['message']) !!}
                        </div>
                    </div>
                    <div style="opacity: 0.2;">
                        <i data-lucide="chevron-right" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        @empty
            {{-- Fallback jika tidak ada anomali --}}
            <div class="card" style="padding: 20px; border-left: 4px solid var(--income); background: rgba(34, 212, 124, 0.05); border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
                <div style="display: flex; gap: 18px; align-items: center;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--card); border: 1px solid rgba(34, 212, 124, 0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        <i data-lucide="check-circle" style="width: 22px; height: 22px; color: var(--income);"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px;">
                            Sistem Siap Analisis
                        </div>
                        <div style="font-size: 13px; color: var(--muted); line-height: 1.5;">
                            Belum ada anomali atau lonjakan pengeluaran terdeteksi. Terus catat transaksimu agar AI bisa memberikan insight yang lebih akurat!
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
