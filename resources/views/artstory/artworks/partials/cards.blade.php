@foreach ($artworks as $artwork)
    @include('artstory.partials.artwork-card', ['artwork' => $artwork, 'currency' => $currency, 'catalogCard' => true])
@endforeach
