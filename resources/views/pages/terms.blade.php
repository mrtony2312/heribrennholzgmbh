@extends('layouts.omniva')

@section('title', 'Allgemeine Geschäftsbedingungen')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Allgemeine Geschäftsbedingungen', 'titleImageSlug' => 'banner-wide-1'])
    <section class="flat-spacing-9">
        <div class="container" style="max-width:820px;">
            <p class="text-caption">Stand: 20.09.2026</p>

            <h5>1. Geltungsbereich und Anbieter</h5>
            <p>Diese Allgemeinen Geschäftsbedingungen (AGB) regeln den Verkauf von Holzbrennstoffen (Brennholz, Kaminholz, Holzpellets, Holzbriketts) über die Website von:</p>
            <p>
                Heri Brennholz GmbH<br>
                Fiderholzstrasse 7<br>
                4562 Biberist, Schweiz<br>
                Handelsregister-Nr.: CH-241.4.020.905-9, UID/IDE: CHE-228.719.493<br>
                E-Mail: <a href="mailto:kontakt@heribrennholzgmbh.com">kontakt@heribrennholzgmbh.com</a>
            </p>
            <p>Die AGB gelten für alle Bestellungen von Kundinnen und Kunden mit Lieferadresse in der Schweiz.</p>

            <h5>2. Vertragsschluss</h5>
            <p>Die Darstellung der Produkte im Shop stellt kein bindendes Angebot dar, sondern eine Aufforderung zur Bestellung. Mit dem Absenden der Bestellung geben Sie ein verbindliches Angebot zum Kauf ab. Der Vertrag kommt mit unserer Bestellbestätigung per E-Mail zustande.</p>

            <h5>3. Preise und Zahlung</h5>
            <p>Alle Preise verstehen sich in Schweizer Franken (CHF) als Endpreise inklusive der gesetzlichen Schweizer Mehrwertsteuer (MWST, Normalsatz {{ rtrim(rtrim(number_format(config('shop.vat_rate'), 1), '0'), '.') }} %). Die Lieferung ist kostenlos. Zahlungsarten: {{ payment_methods_text() }}. Die Zahlungsdetails bzw. die Rechnung erhalten Sie nach Bestelleingang per E-Mail. Bei Vorkasse erfolgt der Versand nach Zahlungseingang.</p>

            <h5>4. Lieferung</h5>
            <p>Wir liefern ausschliesslich innerhalb der Schweiz. Details finden Sie auf unserer <a href="/versand">Versandseite</a>.</p>
            <ul>
                <li><strong>Lieferzeit:</strong> 1 bis 2 Werktage.</li>
                <li><strong>Versandkosten:</strong> Die Lieferung ist für alle Bestellungen in der Schweiz kostenlos, unabhängig vom Bestellwert.</li>
            </ul>
            <p>Bei Lieferverzögerungen, etwa durch Wetter, Verkehr oder Verfügbarkeit, informieren wir Sie so schnell wie möglich.</p>

            <h5>5. Freiwilliges Rückgaberecht</h5>
            <p>Nach Schweizer Recht besteht für online abgeschlossene Kaufverträge kein gesetzliches Widerrufsrecht. Wir räumen unseren Kundinnen und Kunden dennoch freiwillig ein 14-tägiges Rückgaberecht ein. Details dazu finden Sie auf unserer <a href="/rueckgabe">Rückgabeseite</a>. Bei Reklamationen zur Produktqualität kontaktieren Sie uns bitte umgehend, wir finden gemeinsam eine Lösung.</p>

            <h5>6. Gewährleistung</h5>
            <p>Es gelten die gesetzlichen Gewährleistungsrechte nach Schweizer Obligationenrecht (Art. 197 ff. OR). Bitte prüfen Sie die Ware bei Erhalt und melden Sie Mängel oder Transportschäden umgehend, mit Fotos, per E-Mail an kontakt@heribrennholzgmbh.com oder telefonisch unter +41 78 609 95 16.</p>

            <h5>7. Eigentumsvorbehalt</h5>
            <p>Die gelieferte Ware bleibt bis zur vollständigen Bezahlung Eigentum der Heri Brennholz GmbH.</p>

            <h5>8. Haftung</h5>
            <p>Wir haften nach den gesetzlichen Bestimmungen. Eine weitergehende Haftung ist, soweit gesetzlich zulässig, ausgeschlossen.</p>

            <h5>9. Anwendbares Recht und Gerichtsstand</h5>
            <p>Es gilt materielles Schweizer Recht unter Ausschluss des UN-Kaufrechts, unbeschadet zwingender verbraucherschützender Bestimmungen am Wohnsitz der Kundin oder des Kunden. Gerichtsstand für Streitigkeiten mit Kaufleuten ist Solothurn, Schweiz.</p>

            <h5>10. Kontakt</h5>
            <p>Für Fragen zu diesen AGB erreichen Sie uns über unsere <a href="{{ route('pages.contact') }}">Kontaktseite</a> oder per E-Mail an <a href="mailto:kontakt@heribrennholzgmbh.com">kontakt@heribrennholzgmbh.com</a>.</p>
        </div>
    </section>
@endsection
