<x-filament-widgets::widget>
    <style>
        .sts-cache-wrap{display:grid;gap:22px}
        .sts-cache-status-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px}
        .sts-cache-card{border:1px solid #e5e7eb;background:#fff;border-radius:14px;padding:15px;box-shadow:0 1px 2px rgba(15,23,42,.05)}
        .sts-cache-label{font-size:13px;font-weight:700;color:#64748b}
        .sts-cache-value{display:flex;align-items:center;gap:9px;margin-top:8px;font-size:16px;font-weight:800;color:#111827}
        .sts-cache-dot{width:10px;height:10px;border-radius:999px;background:#cbd5e1;flex:none}
        .sts-cache-dot.is-active{background:#22c55e}
        .sts-cache-actions{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
        .sts-cache-button{border:1px solid #d1d5db;background:#fff;color:#111827;border-radius:12px;padding:11px 13px;font-size:14px;font-weight:700;cursor:pointer;transition:background .18s,border-color .18s,transform .18s,box-shadow .18s}
        .sts-cache-button:hover{background:#f8fafc;border-color:#f38020;box-shadow:0 8px 18px rgba(15,23,42,.08);transform:translateY(-1px)}
        .sts-cache-button.is-danger{background:#fff1f2;border-color:#fecdd3;color:#be123c}
        .sts-cache-button.is-danger:hover{background:#ffe4e6;border-color:#fb7185}
        .sts-cache-button.is-success{background:#ecfdf5;border-color:#bbf7d0;color:#15803d}
        .sts-cache-button.is-success:hover{background:#dcfce7;border-color:#4ade80}
        .sts-cache-button:disabled{opacity:.55;cursor:wait;transform:none}
        .sts-cache-note{border:1px solid #fed7aa;background:#fff7ed;color:#9a3412;border-radius:12px;padding:12px 14px;font-size:14px}
        @media (max-width:1100px){.sts-cache-status-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.sts-cache-actions{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media (max-width:640px){.sts-cache-status-grid,.sts-cache-actions{grid-template-columns:1fr}}
    </style>

    <x-filament::section>
        <x-slot name="heading">
            Cache management
        </x-slot>

        <x-slot name="description">
            Clear or rebuild Laravel and Filament cache from the dashboard.
        </x-slot>

        <div class="sts-cache-wrap">
            <div class="sts-cache-status-grid">
                @foreach ($this->getCacheStatuses() as $status)
                    <div class="sts-cache-card">
                        <div class="sts-cache-label">{{ $status['label'] }}</div>
                        <div class="sts-cache-value">
                            <span @class(['sts-cache-dot', 'is-active' => $status['active']])></span>
                            <span>{{ $status['value'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="sts-cache-actions">
                <button type="button" wire:click="clearAll" wire:loading.attr="disabled" class="sts-cache-button is-danger">
                    Clear all cache
                </button>

                <button type="button" wire:click="rebuildOptimizedCache" wire:loading.attr="disabled" class="sts-cache-button is-success">
                    Rebuild optimized cache
                </button>

                <button type="button" wire:click="clearApplicationCache" wire:loading.attr="disabled" class="sts-cache-button">
                    App cache
                </button>

                <button type="button" wire:click="clearFilamentCache" wire:loading.attr="disabled" class="sts-cache-button">
                    Filament cache
                </button>

                <button type="button" wire:click="clearConfigCache" wire:loading.attr="disabled" class="sts-cache-button">
                    Config cache
                </button>

                <button type="button" wire:click="clearRouteCache" wire:loading.attr="disabled" class="sts-cache-button">
                    Route cache
                </button>

                <button type="button" wire:click="clearViewCache" wire:loading.attr="disabled" class="sts-cache-button">
                    View cache
                </button>

                <button type="button" wire:click="clearEventCache" wire:loading.attr="disabled" class="sts-cache-button">
                    Event cache
                </button>
            </div>

            @if ($lastAction)
                <div class="sts-cache-note">
                    Last action: <strong>{{ $lastAction }}</strong>
                    @if ($lastActionAt)
                        <span>at {{ $lastActionAt }}</span>
                    @endif
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
