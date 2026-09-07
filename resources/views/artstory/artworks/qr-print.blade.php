<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Artwork QR Labels</title>
    <style>
        * { box-sizing: border-box; }
        body { background: #f3f4f6; color: #111827; font-family: Arial, sans-serif; margin: 0; }
        .toolbar { align-items: center; background: #111827; color: #fff; display: flex; gap: 14px; justify-content: space-between; padding: 14px 22px; position: sticky; top: 0; z-index: 10; }
        .toolbar h1 { font-size: 16px; margin: 0; }
        .toolbar button { background: #fff; border: 0; color: #111827; cursor: pointer; font-size: 12px; font-weight: 800; letter-spacing: .08em; padding: 10px 16px; text-transform: uppercase; }
        .sheet { display: grid; gap: 18px; grid-template-columns: repeat(2, minmax(0, 1fr)); margin: 24px auto; max-width: 980px; padding: 0 18px; }
        .label { align-items: center; background: #fff; border: 1px solid #d1d5db; display: grid; gap: 16px; grid-template-columns: 132px minmax(0, 1fr); min-height: 190px; padding: 18px; page-break-inside: avoid; }
        .label__qr { align-items: center; display: flex; justify-content: center; }
        .label__qr img { height: 124px; width: 124px; }
        .label__brand { color: #6b7280; font-size: 10px; font-weight: 900; letter-spacing: .18em; margin-bottom: 8px; text-transform: uppercase; }
        .label__code { font-size: 22px; font-weight: 900; letter-spacing: .05em; margin-bottom: 8px; }
        .label__title { font-family: Georgia, 'Times New Roman', serif; font-size: 18px; font-weight: 700; line-height: 1.15; margin-bottom: 10px; }
        .label__meta { color: #4b5563; font-size: 12px; line-height: 1.45; }
        @page { margin: 12mm; size: A4; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { gap: 8mm; grid-template-columns: repeat(2, 1fr); margin: 0; max-width: none; padding: 0; }
            .label { break-inside: avoid; min-height: 48mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <h1>Artwork QR Labels · {{ $artworks->count() }} {{ \Illuminate\Support\Str::plural('artwork', $artworks->count()) }}</h1>
        <button type="button" onclick="window.print()">Print QR Labels</button>
    </div>

    <main class="sheet">
        @foreach ($artworks as $artwork)
            <section class="label">
                <div class="label__qr">
                    <img src="{{ route('artworks.qr', $artwork->artwork_code) }}" alt="QR code for {{ $artwork->artwork_code }}">
                </div>
                <div>
                    <div class="label__brand">{{ $siteSettings?->site_title ?: 'ART Story' }}</div>
                    <div class="label__code">{{ $artwork->artwork_code }}</div>
                    <div class="label__title">{{ $artwork->title ?: 'Untitled' }}</div>
                    <div class="label__meta">
                        Artist: {{ $artwork->artist?->name ?: 'Unknown Artist' }}<br>
                        Medium: {{ $artwork->medium?->name ?: 'Not specified' }}<br>
                        Dimensions: {{ $artwork->size?->label() ?: 'Not specified' }}<br>
                        Year: {{ $artwork->year ?: 'Not specified' }}
                    </div>
                </div>
            </section>
        @endforeach
    </main>
</body>
</html>
