<x-filament-widgets::widget>
    <style>
        .sts-index-wrap{display:grid;gap:18px}
        .sts-index-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
        .sts-index-summary{display:flex;align-items:center;gap:10px;color:#475569;font-size:14px}
        .sts-index-count{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;border-radius:999px;background:#fff7ed;border:1px solid #fed7aa;color:#c2410c;font-weight:800}
        .sts-index-actions{display:flex;gap:10px;flex-wrap:wrap}
        .sts-index-button{border:1px solid #d1d5db;background:#fff;color:#111827;border-radius:12px;padding:10px 13px;font-size:14px;font-weight:700;cursor:pointer;transition:background .18s,border-color .18s,transform .18s,box-shadow .18s}
        .sts-index-button:hover{background:#f8fafc;border-color:#f38020;box-shadow:0 8px 18px rgba(15,23,42,.08);transform:translateY(-1px)}
        .sts-index-button.is-primary{background:#f38020;border-color:#f38020;color:#fff}
        .sts-index-button.is-primary:hover{background:#e96f0d;border-color:#e96f0d}
        .sts-index-table-wrap{overflow-x:auto;border:1px solid #e5e7eb;border-radius:14px}
        .sts-index-table{width:100%;border-collapse:separate;border-spacing:0;font-size:14px}
        .sts-index-table th{padding:13px 14px;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#64748b;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;text-align:left;white-space:nowrap}
        .sts-index-table td{padding:14px;border-bottom:1px solid #f1f5f9;color:#111827;vertical-align:top}
        .sts-index-table tbody tr:last-child td{border-bottom:0}
        .sts-index-table tbody tr:hover td{background:#fff7ed}
        .sts-index-name{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;font-size:13px;font-weight:800;color:#0f172a}
        .sts-index-columns{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;font-size:12px;color:#475569}
        .sts-index-badge{display:inline-flex;align-items:center;border-radius:999px;padding:5px 10px;font-size:12px;font-weight:800;white-space:nowrap}
        .sts-index-badge.is-ok{background:#ecfdf5;border:1px solid #bbf7d0;color:#15803d}
        .sts-index-badge.is-missing{background:#fff7ed;border:1px solid #fed7aa;color:#c2410c}
        .sts-index-badge.is-skip{background:#f1f5f9;border:1px solid #e2e8f0;color:#64748b}
        .sts-index-note{border:1px solid #fed7aa;background:#fff7ed;color:#9a3412;border-radius:12px;padding:12px 14px;font-size:14px}
        @media (max-width:700px){.sts-index-toolbar{align-items:flex-start}.sts-index-actions{width:100%}.sts-index-button{flex:1}}
    </style>

    <x-filament::section>
        <x-slot name="heading">
            Database index management
        </x-slot>

        <x-slot name="description">
            Detect and apply recommended indexes for faster dashboard, frontend, checkout and activity log queries.
        </x-slot>

        <div class="sts-index-wrap">
            <div class="sts-index-toolbar">
                <div class="sts-index-summary">
                    <span class="sts-index-count">{{ $this->getMissingCount() }}</span>
                    <span>recommended indexes missing</span>
                </div>

                <div class="sts-index-actions">
                    <button type="button" wire:click="refreshIndexStatus" wire:loading.attr="disabled" class="sts-index-button">
                        Refresh status
                    </button>

                    <button type="button" wire:click="applyMissingIndexes" wire:loading.attr="disabled" class="sts-index-button is-primary">
                        Apply missing indexes
                    </button>
                </div>
            </div>

            <div class="sts-index-table-wrap">
                <table class="sts-index-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Table</th>
                            <th>Index</th>
                            <th>Columns</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->getRows() as $row)
                            <tr>
                                <td>
                                    @if (! $row['available'])
                                        <span class="sts-index-badge is-skip">Skipped</span>
                                    @elseif ($row['installed'])
                                        <span class="sts-index-badge is-ok">Installed</span>
                                    @else
                                        <span class="sts-index-badge is-missing">Missing</span>
                                    @endif
                                </td>
                                <td><strong>{{ $row['table'] }}</strong></td>
                                <td><span class="sts-index-name">{{ $row['name'] }}</span></td>
                                <td><span class="sts-index-columns">{{ implode(', ', $row['columns']) }}</span></td>
                                <td>{{ $row['reason'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($lastAction)
                <div class="sts-index-note">
                    Last action: <strong>{{ $lastAction }}</strong>
                    @if ($lastActionAt)
                        <span>at {{ $lastActionAt }}</span>
                    @endif
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
