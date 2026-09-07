<x-artstory.layout :site-settings="$siteSettings" :title="$gallery->name" active="virtual-galleries" :immersive="true">
    <main class="virtual-gallery" data-virtual-gallery>
        <script id="virtual-gallery-data" type="application/json">@json($scene)</script>

        <div class="virtual-gallery__canvas" data-virtual-gallery-canvas aria-label="Interactive 3D gallery"></div>

        <div class="virtual-gallery__topbar">
            <a href="{{ route('virtual-galleries.index') }}" class="virtual-gallery__back">Back to galleries</a>
            <div class="virtual-gallery__actions">
                <button type="button" data-gallery-fullscreen title="Fullscreen gallery" aria-label="Fullscreen gallery">Fullscreen</button>
                <span data-gallery-vr></span>
            </div>
        </div>

        <aside class="virtual-gallery__artwork-drawer" data-gallery-artwork-drawer aria-hidden="true" aria-live="polite">
            <button type="button" class="virtual-gallery__drawer-close" data-gallery-artwork-close aria-label="Close artwork details">
                <span class="material-symbols-rounded" aria-hidden="true">close</span>
            </button>
            <div class="virtual-gallery__drawer-image">
                <img data-gallery-artwork-image alt="" hidden>
            </div>
            <div class="virtual-gallery__drawer-copy">
                <p class="virtual-gallery__eyebrow" data-gallery-artwork-code></p>
                <h1 data-gallery-artwork-title></h1>
                <p class="virtual-gallery__artist" data-gallery-artwork-artist></p>
                <dl class="virtual-gallery__artwork-facts">
                    <div><dt>Medium</dt><dd data-gallery-artwork-medium></dd></div>
                    <div><dt>Dimensions</dt><dd data-gallery-artwork-dimensions></dd></div>
                    <div><dt>Year</dt><dd data-gallery-artwork-year></dd></div>
                </dl>
                <a data-gallery-artwork-link class="virtual-gallery__artwork-link" hidden>View artwork</a>
            </div>
        </aside>

        <nav class="virtual-gallery__rooms" aria-label="Gallery rooms">
            @foreach ($scene['rooms'] as $index => $room)
                <button type="button" data-gallery-room="{{ $index }}" @class(['is-active' => $index === 0])>{{ $room['name'] }}</button>
            @endforeach
        </nav>

        <div class="virtual-gallery__movement" aria-label="Move through gallery">
            <button type="button" data-gallery-move="forward" aria-label="Move forward"><span class="material-symbols-rounded" aria-hidden="true">keyboard_arrow_up</span></button>
            <button type="button" data-gallery-move="left" aria-label="Move left"><span class="material-symbols-rounded" aria-hidden="true">keyboard_arrow_left</span></button>
            <button type="button" data-gallery-move="back" aria-label="Move back"><span class="material-symbols-rounded" aria-hidden="true">keyboard_arrow_down</span></button>
            <button type="button" data-gallery-move="right" aria-label="Move right"><span class="material-symbols-rounded" aria-hidden="true">keyboard_arrow_right</span></button>
        </div>

        <p class="virtual-gallery__hint">Drag or swipe to look around. Scroll to walk. Use Shift + scroll to move sideways.</p>
    </main>
</x-artstory.layout>
