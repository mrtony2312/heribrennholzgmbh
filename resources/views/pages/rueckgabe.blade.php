@extends('layouts.omniva')

@section('title', 'Rückgabe')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Rückgabe', 'titleImageSlug' => 'banner-wide-1'])
    <section class="flat-spacing-9">
        <div class="container" style="max-width:820px;">
            <p>Nach Schweizer Recht besteht für online abgeschlossene Kaufverträge kein gesetzliches Widerrufsrecht. Wir räumen unseren Kundinnen und Kunden dennoch freiwillig folgendes Rückgaberecht ein.</p>

            <h5>Frist</h5>
            <p>Sie können innerhalb von 14 Tagen ab Erhalt der Ware eine Rückgabe beantragen. Zur Ausübung genügt eine formlose Mitteilung, z. B. per E-Mail an <a href="mailto:kontakt@heribrennholzgmbh.com">kontakt@heribrennholzgmbh.com</a>.</p>

            <h5>Zustand der Ware</h5>
            <p>Die Ware muss unbenutzt und in wiederverkaufsfähigem Zustand sein.</p>

            <h5>Rücksendekosten</h5>
            <p>Die Kosten der Rücksendung trägt die Kundin bzw. der Kunde, ausser die Ware war defekt oder es wurde ein falscher Artikel geliefert – in diesen Fällen übernehmen wir die Rücksendekosten.</p>

            <h5>Erstattung</h5>
            <p>Nach Eingang und Prüfung der zurückgesandten Ware erstatten wir den Kaufpreis innerhalb von 14 Tagen auf dasselbe Zahlungsmittel.</p>
            <p>Für sperrige Güter (Paletten, Öfen) können abweichende Rückgabebedingungen gelten – bitte kontaktieren Sie uns.</p>

            <h5>Fragen</h5>
            <p>Bei Fragen zu einer Rückgabe erreichen Sie uns über unsere <a href="{{ route('pages.contact') }}">Kontaktseite</a>.</p>
        </div>
    </section>
@endsection
