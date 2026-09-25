@extends('layouts.omniva')

@section('title', 'Impressum')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Impressum', 'titleImageSlug' => 'banner-wide-1'])
    <section class="flat-spacing-9">
        <div class="container" style="max-width:820px;">
            <h5>Anbieterkennzeichnung</h5>
            <p>
                Heri Brennholz GmbH<br>
                Fiderholzstrasse 7<br>
                4562 Biberist<br>
                Schweiz
            </p>

            <h5>Vertretungsberechtigt</h5>
            <p>Markus Heri, Geschäftsführer (Einzelunterschrift)</p>

            <h5>Handelsregister</h5>
            <p>
                Handelsregister-Nr.: CH-241.4.020.905-9<br>
                UID/IDE: CHE-228.719.493<br>
                {{-- TODO: MWST-Nummer ausstehend, sobald verfügbar ergänzen --}}
                Rechtsform: Gesellschaft mit beschränkter Haftung (GmbH)<br>
                Sitz: Biberist, Kanton Solothurn, Schweiz
            </p>

            <h5>Kontakt</h5>
            <p>
                E-Mail: <a href="mailto:kontakt@heribrennholzgmbh.com">kontakt@heribrennholzgmbh.com</a><br>
                Telefon: <a href="tel:+41786099516">+41 78 609 95 16</a>
            </p>

            <h5>Unternehmensgegenstand</h5>
            <p>Produktion von Brennholz und Holzenergie sowie Handel mit Waren aller Art.</p>

            <h5>Streitschlichtung</h5>
            <p>Wir sind nicht verpflichtet und nicht bereit, an einem Streitschlichtungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
        </div>
    </section>
@endsection
