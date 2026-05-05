@inject('forecaster', 'App\Services\ForecastingService')

@php
    $forecast = $forecaster->getForecast();
    $colors = [
        'critical' => [
            'bg' => 'rgba(255, 87, 87, 0.08)', 
            'border' => 'rgba(255, 87, 87, 0.25)', 
            'text' => 'var(--expense)', 
            'icon' => 'alert-circle'
        ],
        'warning'  => [
            'bg' => 'rgba(247, 183, 49, 0.08)', 
            'border' => 'rgba(247, 183, 49, 0.25)', 
            'text' => 'var(--accent2)', 
            'icon' => 'alert-triangle'
        ],
        'normal'   => [
            'bg' => 'rgba(108, 141, 250, 0.08)', 
            'border' => 'rgba(108, 141, 250, 0.25)', 
            'text' => 'var(--accent)', 
            'icon' => 'trending-up'
        ],
        'safe'     => [
            'bg' => 'rgba(34, 212, 124, 0.08)', 
            'border' => 'rgba(34, 212, 124, 0.25)', 
            'text' => 'var(--income)', 
            'icon' => 'check-circle'
        ],
    ];
    $style = $colors[$forecast['status']] ?? $colors['normal'];
@endphp

<div class="card" style="background: {{ $style['bg'] }}; border: 1px solid {{ $style['border'] }}; margin-bottom: 24px; position: relative; overflow: hidden;">
    {{-- Decorative background glow --}}
    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: {{ $style['text'] }}; opacity: 0.05; filter: blur(40px); border-radius: 50%;"></div>
    
    <div style="display: flex; gap: 18px; align-items: flex-start; position: relative; z-index: 1;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--card); border: 1px solid {{ $style['border'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="{{ $style['icon'] }}" style="width: 22px; height: 22px; color: {{ $style['text'] }};"></i>
        </div>
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <div style="font-family: var(--font-head); font-size: 16px; font-weight: 700; color: {{ $style['text'] }}; letter-spacing: -0.3px;">
                    Smart Budget Forecast
                </div>
                @if(isset($forecast['days_remaining']) && $forecast['days_remaining'] < 366)
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: {{ $style['text'] }}; opacity: 0.8; background: {{ $style['bg'] }}; padding: 2px 8px; border-radius: 6px; border: 1px solid {{ $style['border'] }};">
                        {{ $forecast['days_remaining'] }} Hari Lagi
                    </div>
                @endif
            </div>
            
            <div style="font-size: 14px; color: var(--text); line-height: 1.6; opacity: 0.95;">
                {!! Str::markdown($forecast['message']) !!}
            </div>
            
            @if($forecast['daily_average'] > 0)
                <div style="display: flex; align-items: center; gap: 12px; margin-top: 12px; padding-top: 12px; border-top: 1px solid {{ $style['border'] }};">
                    <div style="font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="calculator" style="width: 14px; height: 14px; opacity: 0.6;"></i>
                        Pengeluaran harian: <span style="color: var(--text); font-weight: 600;">Rp {{ number_format($forecast['daily_average'], 0, ',', '.') }}</span>
                    </div>
                    <div style="font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="wallet" style="width: 14px; height: 14px; opacity: 0.6;"></i>
                        Saldo saat ini: <span style="color: var(--text); font-weight: 600;">Rp {{ number_format($forecast['current_balance'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
