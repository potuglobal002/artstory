<x-filament-widgets::widget>
    @php
        $total = max($this->getTotalCount(), 1);
        $actualTotal = $this->getTotalCount();
        $available = $this->getAvailableCount();
        $sold = $this->getSoldCount();
        $availablePercent = round(($available / $total) * 100);
        $soldPercent = round(($sold / $total) * 100);
        $recentSales = $this->getRecentSales();
        $topArtists = $this->getTopArtists();
    @endphp

    <style>
        .art-dashboard { display: grid; gap: 24px; }
        .art-dashboard__metrics { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 22px; }
        .art-dashboard__card,
        .art-dashboard__panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 4px rgba(15, 23, 42, .06); min-width: 0; overflow: hidden; }
        .art-dashboard__card { min-height: 132px; padding: 26px 30px; }
        .art-dashboard__label { color: #667085; font-size: 15px; font-weight: 600; margin-bottom: 16px; }
        .art-dashboard__value { color: #030712; font-size: 34px; font-weight: 800; line-height: 1; }
        .art-dashboard__hint { color: #667085; font-size: 13px; margin-top: 14px; }
        .art-dashboard__body { align-items: start; display: grid; grid-template-columns: minmax(0, 1fr) minmax(280px, 340px); gap: 22px; }
        .art-dashboard__panel-header { align-items: center; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; padding: 20px 24px; }
        .art-dashboard__title { color: #030712; font-size: 18px; font-weight: 800; margin: 0; }
        .art-dashboard__subtle { color: #667085; font-size: 13px; }
        .art-dashboard__table-wrap { max-width: 100%; overflow-x: auto; }
        .art-dashboard__table { border-collapse: collapse; min-width: 760px; width: 100%; }
        .art-dashboard__table th { background: #fafafa; color: #111827; font-size: 13px; font-weight: 800; padding: 15px 18px; text-align: left; white-space: nowrap; }
        .art-dashboard__table td { border-top: 1px solid #edf0f3; color: #111827; font-size: 14px; padding: 17px 18px; vertical-align: middle; }
        .art-dashboard__art-title { font-size: 15px; font-weight: 800; margin-bottom: 5px; }
        .art-dashboard__muted { color: #667085; font-size: 13px; font-weight: 600; }
        .art-dashboard__amount { font-weight: 800; text-align: right; white-space: nowrap; }
        .art-dashboard__badge { background: #dcfce7; border-radius: 8px; color: #166534; display: inline-flex; font-size: 13px; font-weight: 800; padding: 7px 11px; }
        .art-dashboard__side { display: grid; gap: 22px; }
        .art-dashboard__panel-content { padding: 22px 24px; }
        .art-dashboard__row { align-items: center; display: flex; gap: 14px; justify-content: space-between; margin-bottom: 18px; }
        .art-dashboard__row:last-child { margin-bottom: 0; }
        .art-dashboard__bar-wrap { flex: 1; }
        .art-dashboard__bar { background: #f1f5f9; border-radius: 999px; height: 8px; margin-top: 8px; overflow: hidden; }
        .art-dashboard__bar span { border-radius: 999px; display: block; height: 100%; }
        .art-dashboard__artist { align-items: center; border: 1px solid #edf0f3; border-radius: 10px; display: flex; justify-content: space-between; margin-bottom: 10px; padding: 12px 14px; }
        .art-dashboard__artist:last-child { margin-bottom: 0; }
        .art-dashboard__empty { color: #667085; font-size: 14px; padding: 34px 24px; text-align: center; }
        @media (max-width: 1100px) {
            .art-dashboard__metrics,
            .art-dashboard__body { grid-template-columns: 1fr; }
        }
    </style>

    <div class="art-dashboard">
        <div class="art-dashboard__metrics">
            <div class="art-dashboard__card">
                <div class="art-dashboard__label">Total Artworks</div>
                <div class="art-dashboard__value">{{ number_format($actualTotal) }}</div>
                <div class="art-dashboard__hint">{{ number_format($available) }} available · {{ number_format($sold) }} sold</div>
            </div>

            <div class="art-dashboard__card">
                <div class="art-dashboard__label">Sales Revenue</div>
                <div class="art-dashboard__value">{{ $this->money($this->getTotalRevenue('BDT'), 'BDT') }}</div>
                <div class="art-dashboard__hint">{{ $this->money($this->getTotalRevenue('USD'), 'USD') }} international</div>
                <div class="art-dashboard__hint">{{ number_format($this->getTotalSales()) }} confirmed sales</div>
            </div>

            <div class="art-dashboard__card">
                <div class="art-dashboard__label">Artists</div>
                <div class="art-dashboard__value">{{ number_format($this->getArtistCount()) }}</div>
                <div class="art-dashboard__hint">{{ number_format($this->getBuyerCount()) }} customers recorded</div>
            </div>
        </div>

        <div class="art-dashboard__body">
            <div class="art-dashboard__panel">
                <div class="art-dashboard__panel-header">
                    <h2 class="art-dashboard__title">Recent Sales</h2>
                    <span class="art-dashboard__subtle">Latest confirmed artwork sales</span>
                </div>

                <div class="art-dashboard__table-wrap">
                    <table class="art-dashboard__table">
                        <thead>
                            <tr>
                                <th>Artwork</th>
                                <th>Customer</th>
                                <th>Paid By</th>
                                <th style="text-align: right;">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentSales as $sale)
                                <tr>
                                    <td>
                                        <div class="art-dashboard__art-title">{{ $sale->artwork?->title ?: 'Untitled' }}</div>
                                        <div class="art-dashboard__muted">{{ $sale->artist?->name ?: 'Unknown Artist' }}</div>
                                    </td>
                                    <td>{{ $sale->buyer_name ?: $sale->buyer?->name ?: 'Walk-in Customer' }}</td>
                                    <td>{{ $sale->paid_by ?: '-' }}</td>
                                    <td class="art-dashboard__amount">{{ $this->money($sale->sold_price, $sale->currency ?: 'BDT') }}</td>
                                    <td><span class="art-dashboard__badge">Confirmed</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="art-dashboard__empty">No sales recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="art-dashboard__side">
                <div class="art-dashboard__panel">
                    <div class="art-dashboard__panel-header">
                        <h2 class="art-dashboard__title">Inventory Status</h2>
                    </div>
                    <div class="art-dashboard__panel-content">
                        <div class="art-dashboard__row">
                            <div class="art-dashboard__bar-wrap">
                                <div class="art-dashboard__muted">Available</div>
                                <div class="art-dashboard__bar"><span style="width: {{ $availablePercent }}%; background: #16a34a;"></span></div>
                            </div>
                            <strong>{{ number_format($available) }}</strong>
                        </div>
                        <div class="art-dashboard__row">
                            <div class="art-dashboard__bar-wrap">
                                <div class="art-dashboard__muted">Sold</div>
                                <div class="art-dashboard__bar"><span style="width: {{ $soldPercent }}%; background: #111827;"></span></div>
                            </div>
                            <strong>{{ number_format($sold) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="art-dashboard__panel">
                    <div class="art-dashboard__panel-header">
                        <h2 class="art-dashboard__title">Top Artists</h2>
                    </div>
                    <div class="art-dashboard__panel-content">
                        @forelse ($topArtists as $row)
                            <div class="art-dashboard__artist">
                                <strong>{{ $row->artist?->name ?: 'Unknown Artist' }}</strong>
                                <span class="art-dashboard__muted">{{ number_format($row->artworks_count) }}</span>
                            </div>
                        @empty
                            <div class="art-dashboard__empty">No artist inventory yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
