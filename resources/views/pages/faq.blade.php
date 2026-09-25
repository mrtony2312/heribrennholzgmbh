@extends('layouts.omniva')

@section('title', 'FAQ')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Häufige Fragen', 'titleImageSlug' => 'hero-3'])
    <section class="flat-spacing-9">
        <div class="container" style="max-width:820px;">
            @php
                $faqs = [
                    ['Wie lange dauert die Lieferung?', 'Wir liefern kostenlos innerhalb der Schweiz in 1 bis 2 Werktagen.'],
                    ['Ist die Lieferung kostenlos?', 'Ja. Wir liefern kostenlos in der ganzen Schweiz, unabhängig vom Bestellwert.'],
                    ['Liefert Heri Brennholz auch ins Ausland?', 'Nein. Wir liefern ausschliesslich innerhalb der Schweiz.'],
                    ['Sind Ihre Produkte zertifiziert?', 'Zertifizierungen und Herstellerangaben (z. B. Norm, Herkunft) sind bei den jeweiligen Produkten aufgeführt. Bei Fragen zu einem Produkt kontaktieren Sie uns bitte.'],
                    ['Wie lagere ich Brennholz richtig?', 'An einem belüfteten, regengeschützten Ort und vom Boden abgehoben. Holzbriketts und Pellets müssen trocken gelagert werden.'],
                    ['Welche Zahlungsmöglichkeiten habe ich?', 'Sie können per '.payment_methods_text().' bezahlen. Alle Preise verstehen sich in CHF inklusive Mehrwertsteuer.'],
                    ['Wie kann ich Heri Brennholz kontaktieren?', 'Sie erreichen uns über unser Kontaktformular oder per E-Mail an kontakt@heribrennholzgmbh.com. Details finden Sie auf unserer Kontaktseite.'],
                ];
            @endphp
            <div class="accordion" id="faqAccordion">
                @foreach ($faqs as $i => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                                {{ $faq[0] }}
                            </button>
                        </h2>
                        <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">{{ $faq[1] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
