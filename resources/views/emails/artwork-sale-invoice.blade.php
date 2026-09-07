<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ART Story Invoice</title>
</head>
<body style="margin:0;background:#f5f3ef;color:#151821;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f3ef;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border:1px solid #e5e0d8;">
                    <tr>
                        <td style="background:#111111;padding:24px 30px;">
                            @if ($siteLogoUrl)
                                <img src="{{ $siteLogoUrl }}" alt="{{ $siteTitle }}" style="display:block;max-height:58px;width:auto;filter:invert(1);">
                            @else
                                <div style="color:#ffffff;font-size:12px;font-weight:800;letter-spacing:4px;text-transform:uppercase;">{{ $siteTitle }}</div>
                            @endif
                            <div style="margin-top:8px;color:#cfcfcf;font-size:13px;">Invoice confirmation</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 30px;">
                            <h1 style="margin:0 0 18px;color:#111111;font-family:Georgia,'Times New Roman',serif;font-size:30px;line-height:1.2;">Thank you for your purchase</h1>
                            <p style="margin:0 0 20px;color:#4b5563;font-size:16px;line-height:1.7;">Dear {{ $sale->buyer_name ?: 'Customer' }},</p>
                            @php($lineItems = $sale->displayLineItems())
                            <p style="margin:0 0 24px;color:#4b5563;font-size:16px;line-height:1.7;">Your {{ $siteTitle }} invoice is attached with this email. Please keep it for your purchase record.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding:13px 16px;background:#f9fafb;color:#6b7280;font-size:13px;">Invoice</td>
                                    <td align="right" style="padding:13px 16px;background:#f9fafb;color:#111827;font-size:13px;font-weight:700;">{{ $sale->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Artworks</td>
                                    <td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $lineItems->count() }} {{ \Illuminate\Support\Str::plural('item', $lineItems->count()) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Payment</td>
                                    <td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $sale->paid_by ?: '-' }}</td>
                                </tr>
                                @foreach ($lineItems as $item)
                                    <tr>
                                        <td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">
                                            {{ $item['title'] ?? 'Untitled' }}
                                            <div style="margin-top:4px;color:#6b7280;font-size:12px;font-weight:400;">{{ $item['artist_name'] ?? 'Unknown Artist' }}</div>
                                        </td>
                                        <td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $sale->formattedMoney($item['sold_price'] ?? 0) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Subtotal</td>
                                    <td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $sale->formattedMoney($sale->subtotal()) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Tax {{ (float) ($sale->tax_percentage ?? 0) }}%</td>
                                    <td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $sale->formattedMoney($sale->tax_amount ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:15px 16px;border-top:1px solid #111111;color:#111111;font-size:14px;font-weight:700;">Total paid</td>
                                    <td align="right" style="padding:15px 16px;border-top:1px solid #111111;color:#111111;font-size:16px;font-weight:800;">{{ $sale->formattedMoney($sale->totalPaid()) }}</td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0;color:#4b5563;font-size:15px;line-height:1.7;">For any assistance, reply to this email or contact {{ $supportEmail ?? 'ART Story' }}@if(filled($supportPhone ?? null)) / {{ $supportPhone }}@endif.</p>
                            <p style="margin:24px 0 0;color:#111111;font-size:15px;line-height:1.7;">Regards,<br><strong>{{ $siteTitle }}</strong></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
