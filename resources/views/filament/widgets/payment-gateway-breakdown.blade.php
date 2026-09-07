<x-filament-widgets::widget>
    <style>
        .sts-widget-table-wrap{overflow-x:auto}
        .sts-widget-table{width:100%;border-collapse:separate;border-spacing:0;font-size:14px}
        .sts-widget-table th{padding:13px 14px;border-bottom:1px solid #e5e7eb;color:#6b7280;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;text-align:left;white-space:nowrap}
        .sts-widget-table th.sts-num,.sts-widget-table td.sts-num{text-align:right}
        .sts-widget-table td{padding:14px;border-bottom:1px solid #f1f5f9;color:#111827;vertical-align:middle;white-space:nowrap}
        .sts-widget-table tbody tr:last-child td{border-bottom:0}
        .sts-widget-table tbody tr:hover td{background:#fff7ed}
        .sts-gateway-badge{display:inline-flex;align-items:center;border-radius:999px;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;padding:5px 10px;font-size:12px;font-weight:700}
        .sts-success{color:#15803d!important;font-weight:700}
        .sts-warning{color:#b45309!important;font-weight:700}
        .sts-danger{color:#b91c1c!important;font-weight:700}
        .sts-muted{color:#64748b!important}
        .sts-empty{padding:26px!important;text-align:center!important;color:#64748b!important}
    </style>

    <x-filament::section>
        <x-slot name="heading">
            Payment gateway breakdown
        </x-slot>

        <x-slot name="description">
            Enrollment count and confirmed payment amount by gateway.
        </x-slot>

        <div class="sts-widget-table-wrap">
            <table class="sts-widget-table">
                <thead>
                    <tr>
                        <th>Gateway</th>
                        <th class="sts-num">Total enrollments</th>
                        <th class="sts-num">Paid</th>
                        <th class="sts-num">Pending</th>
                        <th class="sts-num">Failed</th>
                        <th class="sts-num">Paid amount</th>
                        <th class="sts-num">Latest enrollment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->getRows() as $row)
                        <tr>
                            <td><span class="sts-gateway-badge">{{ $row->gateway_name ?: 'Unknown' }}</span></td>
                            <td class="sts-num"><strong>{{ number_format((int) $row->total_enrollments) }}</strong></td>
                            <td class="sts-num sts-success">{{ number_format((int) $row->paid_enrollments) }}</td>
                            <td class="sts-num sts-warning">{{ number_format((int) $row->pending_enrollments) }}</td>
                            <td class="sts-num sts-danger">{{ number_format((int) $row->failed_enrollments) }}</td>
                            <td class="sts-num"><strong>{{ $this->money($row->paid_amount) }}</strong></td>
                            <td class="sts-num sts-muted">
                                {{ $row->latest_enrollment_at ? \Illuminate\Support\Carbon::parse($row->latest_enrollment_at)->format('M d, Y h:i A') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="sts-empty">No course enrollments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
