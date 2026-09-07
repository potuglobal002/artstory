<x-filament-panels::page>
    <style>
        .art-report{display:grid;gap:24px}
        .art-report-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;border:1px solid #e2e8f0;background:#fff;border-radius:8px;padding:16px;box-shadow:0 1px 3px rgba(15,23,42,.12)}
        .art-report-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end}
        .art-report-btn{height:40px;border-radius:8px;padding:0 16px;border:1px solid #d9dde7;background:#fff;color:#0f172a;font-size:14px;font-weight:800;box-shadow:0 1px 2px rgba(15,23,42,.06)}
        .art-report-icon-btn{width:40px;padding:0;display:inline-flex;align-items:center;justify-content:center}
        .art-report-icon-btn svg{width:20px;height:20px;stroke:currentColor}
        .art-report-btn.is-primary{border-color:#000;background:#000;color:#fff}
        .art-report-btn.is-success{border-color:#166534;background:#166534;color:#fff}
        .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
        .art-report-filters{display:flex;align-items:center;gap:28px;min-width:0}
        .art-report-field{display:flex;align-items:center;gap:10px;min-width:0}
        .art-report-field label{color:#5b6476;font-size:14px;font-weight:700}
        .art-report-field select{height:42px;min-width:220px;border:1px solid #d9dde7;border-radius:8px;background:#fff;padding:0 34px 0 12px;color:#0f172a;font-size:14px}
        .art-report-tabs{display:flex;align-items:center;justify-content:center;gap:14px;max-width:760px;margin:0 auto;border:1px solid #e2e8f0;background:#fff;border-radius:8px;padding:12px;box-shadow:0 1px 3px rgba(15,23,42,.12)}
        .art-report-tab{display:inline-flex;align-items:center;gap:9px;height:38px;border:0;border-radius:8px;background:transparent;color:#5b6476;padding:0 16px;font-size:15px;font-weight:700}
        .art-report-tab.is-active{background:#f8fafc;color:#155dfc}
        .art-report-tab-count{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:26px;border-radius:8px;border:1px solid #d9dde7;background:#fff;color:#334155;font-size:13px;font-weight:700;padding:0 8px}
        .art-report-tab.is-available .art-report-tab-count{border-color:#bbf7d0;background:#f0fdf4;color:#15803d}
        .art-report-tab.is-sold .art-report-tab-count{border-color:#fecaca;background:#fef2f2;color:#dc2626}
        .art-report-panel{border:1px solid #e2e8f0;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.12)}
        .art-report-table{width:100%;border-collapse:collapse}
        .art-report-table th{height:56px;padding:0 24px;text-align:left;color:#050816;font-size:15px;font-weight:800;border-bottom:1px solid #e5e7eb;background:#fff}
        .art-report-table td{padding:16px 24px;border-bottom:1px solid #e5e7eb;vertical-align:middle;color:#0f172a}
        .art-report-table tr:last-child td{border-bottom:0}
        .art-report-table tr:hover td{background:#fafafa}
        .artwork-image{width:64px;height:64px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0;background:#f8fafc}
        .artwork-image-empty{display:grid;place-items:center;color:#94a3b8;font-size:10px;font-weight:800;text-transform:uppercase}
        .artwork-title{margin:0;color:#050816;font-size:15px;font-weight:800;line-height:1.35}
        .artwork-artist{margin:5px 0 0;color:#000;font-size:13px;font-weight:800}
        .artwork-meta{margin:5px 0 0;color:#667085;font-size:13px;font-weight:600}
        .artwork-price{display:grid;gap:5px}
        .artwork-price span{color:#667085;font-size:12px;font-weight:700}
        .artwork-price strong{color:#050816;font-size:15px;font-weight:800}
        .artwork-price .sell strong{color:#15803d}
        .art-report-status{display:inline-flex;align-items:center;justify-content:center;border-radius:8px;padding:6px 10px;font-size:13px;font-weight:800;text-transform:capitalize}
        .art-report-status.available{background:#dcfce7;color:#166534}
        .art-report-status.sold{background:#fee2e2;color:#991b1b}
        .art-report-empty{padding:34px;text-align:center;color:#64748b;font-weight:700}
        @media (max-width:1100px){.art-report-toolbar{display:grid}.art-report-filters,.art-report-tabs{align-items:stretch;flex-direction:column;max-width:none}.art-report-field{justify-content:space-between}.art-report-field select{width:220px}.art-report-table{min-width:1020px}.art-report-panel{overflow-x:auto}}
        @media (max-width:700px){.art-report-actions{justify-content:flex-start}.art-report-btn:not(.art-report-icon-btn){width:100%}.art-report-field{display:grid}.art-report-field select{width:100%}}
    </style>

    @php
        $options = $this->getFilterOptions();
        $statusCounts = $this->getStatusCounts();
        $artworkRows = $this->getArtworkRows();
        $money = fn ($value, $currency) => strtoupper($currency) === 'USD'
            ? 'USD ' . number_format((float) $value, 2)
            : 'BDT ' . number_format((float) $value, 0);
    @endphp

    <div class="art-report">
        <div class="art-report-toolbar">
            <div class="art-report-filters">
                <div class="art-report-field">
                    <label>Artist</label>
                    <select wire:model.live="artistId">
                        <option value="">All artists</option>
                        @foreach ($options['artists'] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="art-report-field">
                    <label>Year</label>
                    <select wire:model.live="year">
                        <option value="">All years</option>
                        @foreach ($options['years'] as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="art-report-actions">
                <button type="button" class="art-report-btn" wire:click="resetFilters">Reset filters</button>
                <button type="button" class="art-report-btn art-report-icon-btn is-primary" wire:click="downloadPdf" title="Export PDF" aria-label="Export PDF">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M12 18v-6"/>
                        <path d="m9 15 3 3 3-3"/>
                    </svg>
                    <span class="sr-only">Export PDF</span>
                </button>
                <button type="button" class="art-report-btn art-report-icon-btn is-success" wire:click="downloadXlsx" title="Export XLSX" aria-label="Export XLSX">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M8 13h8"/>
                        <path d="M8 17h8"/>
                        <path d="M8 9h2"/>
                    </svg>
                    <span class="sr-only">Export XLSX</span>
                </button>
            </div>
        </div>

        <div class="art-report-tabs">
            <button type="button" class="art-report-tab {{ $this->status === 'all' ? 'is-active' : '' }}" wire:click="setStatus('all')">
                All <span class="art-report-tab-count">{{ number_format($statusCounts['all']) }}</span>
            </button>
            <button type="button" class="art-report-tab is-available {{ $this->status === 'available' ? 'is-active' : '' }}" wire:click="setStatus('available')">
                Available <span class="art-report-tab-count">{{ number_format($statusCounts['available']) }}</span>
            </button>
            <button type="button" class="art-report-tab is-sold {{ $this->status === 'sold' ? 'is-active' : '' }}" wire:click="setStatus('sold')">
                Sold <span class="art-report-tab-count">{{ number_format($statusCounts['sold']) }}</span>
            </button>
        </div>

        <div class="art-report-panel">
            <table class="art-report-table">
                <thead>
                    <tr>
                        <th>Artwork Image</th>
                        <th>Art Details</th>
                        <th>BDT Price</th>
                        <th>USD Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($artworkRows as $artwork)
                        <tr>
                            <td>
                                @if ($artwork->imageUrl())
                                    <img class="artwork-image" src="{{ $artwork->imageUrl() }}" alt="{{ $artwork->title }}">
                                @else
                                    <div class="artwork-image artwork-image-empty">No image</div>
                                @endif
                            </td>
                            <td>
                                <h3 class="artwork-title">{{ $artwork->title }}</h3>
                                <p class="artwork-artist">{{ $artwork->artist?->name ?: 'Unknown Artist' }}</p>
                                <p class="artwork-meta">{{ $artwork->year ?: 'Unknown Year' }} | {{ $artwork->style?->name ?: 'No style' }} | {{ $artwork->medium?->name ?: 'No medium' }}</p>
                            </td>
                            <td>
                                <div class="artwork-price">
                                    <div><span>Regular Price</span><strong>{{ $artwork->price ? $money($artwork->price, 'BDT') : '-' }}</strong></div>
                                    @if ($artwork->selling_price)
                                        <div class="sell"><span>Sell Price</span><strong>{{ $money($artwork->selling_price, 'BDT') }}</strong></div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="artwork-price">
                                    <div><span>Regular Price</span><strong>{{ $artwork->usd_price ? $money($artwork->usd_price, 'USD') : '-' }}</strong></div>
                                    @if ($artwork->usd_selling_price)
                                        <div class="sell"><span>Sell Price</span><strong>{{ $money($artwork->usd_selling_price, 'USD') }}</strong></div>
                                    @endif
                                </div>
                            </td>
                            <td><span class="art-report-status {{ $artwork->status }}">{{ $artwork->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="art-report-empty">No artworks found for these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
