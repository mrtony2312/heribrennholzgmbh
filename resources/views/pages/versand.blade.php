@extends('layouts.omniva')

@section('title', 'Versand')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Versand', 'titleImageSlug' => 'banner-wide-1'])
    <section class="flat-spacing-9">
        <div class="container" style="max-width:820px;">
            <h5>Liefergebiet</h5>
            <p>Wir liefern ausschliesslich innerhalb der Schweiz. Eine Lieferung ins Ausland ist derzeit nicht möglich.</p>

            <h5>Kosten</h5>
            <p>Die Lieferung ist für alle Bestellungen in der Schweiz kostenlos – ohne Mindestbestellwert.</p>

            <h5>Lieferzeit</h5>
            <p>Die gesamte Lieferzeit beträgt <strong>1 bis 2 Werktage</strong> nach Bestelleingang (Bearbeitung und Transport zusammen).</p>
            <p>Bei Lieferverzögerungen, etwa durch Wetter, Verkehr oder Verfügbarkeit, informieren wir Sie so schnell wie möglich.</p>

            <h5>Fragen</h5>
            <p>Bei Fragen zu Ihrer Lieferung erreichen Sie uns über unsere <a href="{{ route('pages.contact') }}">Kontaktseite</a> oder telefonisch unter <a href="tel:+41786099516">+41 78 609 95 16</a>.</p>
        </div>
    </section>
@endsection
