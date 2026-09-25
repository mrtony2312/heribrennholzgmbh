@extends('layouts.omniva')

@section('title', 'Über uns')

@php
    $img = fn ($n, $fallback = 'assets/images/item/item-bg.jpg') =>
        is_file(public_path("media/site/{$n}.jpg")) ? asset("media/site/{$n}.jpg") : asset($fallback);
@endphp

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Über Heri Brennholz', 'titleImageSlug' => 'about-1'])
    <section class="flat-spacing-9">
        <div class="container">
            <div class="row align-items-center g-4 mb-5">
                <div class="col-lg-6">
                    <img src="{{ $img('about-1') }}" alt="Brennholzlager" class="rounded-16 w-100" style="object-fit:cover;max-height:420px;">
                </div>
                <div class="col-lg-6">
                    <p class="lead">Die Heri Brennholz GmbH mit Sitz in Biberist (Kanton Solothurn) vertreibt Brennholz, Kaminholz, Holzpellets, Holzbriketts sowie Öfen. Ein Teil des Sortiments stammt von Partnerherstellern; die Herstellerangaben finden Sie bei den einzelnen Produkten.</p>
                    <p>Wir liefern kostenlos in der ganzen Schweiz, in 1 bis 2 Werktagen.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <h5 class="mt-2">Unsere Zusagen</h5>
                    <ul>
                                                <li>Kostenlose Lieferung in der ganzen Schweiz, ohne Mindestbestellwert.</li>
                        <li>Lieferung in 1 bis 2 Werktagen.</li>
                        <li>Persönliche Beratung passend zu Ihrem Heizgerät (Ofen, Kamineinsatz, Heizkessel).</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="{{ $img('about-2') }}" alt="Brennholz vorbereiten" class="rounded-16 w-100" style="object-fit:cover;max-height:320px;">
                </div>
            </div>
        </div>
    </section>
@endsection
