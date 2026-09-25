@extends('layouts.omniva')

@section('title', 'Datenschutzerklärung')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Datenschutzerklärung', 'titleImageSlug' => 'banner-wide-1'])
    <section class="flat-spacing-9">
        <div class="container" style="max-width:820px;">
            <p class="text-caption">Stand: 20.09.2026</p>

            <h5>1. Verantwortliche Stelle</h5>
            <p>
                Heri Brennholz GmbH<br>
                Fiderholzstrasse 7<br>
                4562 Biberist, Schweiz<br>
                UID/IDE: CHE-228.719.493<br>
                E-Mail: <a href="mailto:kontakt@heribrennholzgmbh.com">kontakt@heribrennholzgmbh.com</a>
            </p>
            <p>Diese Erklärung informiert Sie über Art, Umfang und Zweck der Verarbeitung personenbezogener Daten auf unserer Website, im Einklang mit dem Schweizer Datenschutzgesetz (DSG).</p>

            <h5>2. Welche Daten wir erheben</h5>
            <p>Wir erheben nur die Daten, die zur Abwicklung Ihrer Bestellung, zur Kundenbetreuung und zur Beantwortung Ihrer Anfragen erforderlich sind: Name, Adresse, E-Mail-Adresse, Telefonnummer, Lieferadresse sowie Bestell- und Zahlungsdaten.</p>

            <h5>3. Zweck der Datenverarbeitung</h5>
            <ul>
                <li>Abwicklung und Auslieferung Ihrer Bestellungen in der Schweiz;</li>
                <li>Kundenbetreuung und Bearbeitung von Kontaktanfragen;</li>
                <li>Erfüllung gesetzlicher Aufbewahrungspflichten (Buchhaltung, Steuerrecht);</li>
                <li>mit Ihrer ausdrücklichen Einwilligung: Versand von Angeboten und Newslettern.</li>
            </ul>

            <h5>4. Weitergabe von Daten</h5>
            <p>Ihre Daten werden nur an Dritte weitergegeben, soweit dies zur Vertragserfüllung notwendig ist (z. B. an Transport- und Logistikunternehmen für die Lieferung in die Schweiz) oder wir gesetzlich dazu verpflichtet sind. Ein Verkauf Ihrer Daten an Dritte findet nicht statt.</p>

            <h5>5. Aufbewahrungsdauer</h5>
            <p>Bestelldaten werden für die gesetzlich vorgeschriebene Dauer im Rahmen unserer Buchhaltungs- und Steuerpflichten aufbewahrt und danach gelöscht, sofern keine weitere gesetzliche Aufbewahrungspflicht besteht.</p>

            <h5>6. Cookies</h5>
            <p>Unsere Website verwendet technisch notwendige Cookies (Sitzungs-Cookie für Warenkorb und Bestellung, Sicherheits-Cookie gegen Formularmissbrauch), die mit dem Schliessen des Browsers bzw. nach Ablauf der Sitzung enden. Wir setzen keine Werbe- oder Tracking-Cookies ein. Weitere Cookies werden nur mit Ihrer Einwilligung gesetzt.</p>

            <h5>7. Ihre Rechte</h5>
            <p>Sie haben das Recht auf Auskunft, Berichtigung, Löschung und Einschränkung der Verarbeitung Ihrer Daten sowie, soweit anwendbar, auf Datenübertragbarkeit und Widerspruch. Wenden Sie sich dazu an <a href="mailto:kontakt@heribrennholzgmbh.com">kontakt@heribrennholzgmbh.com</a>. Sie haben zudem das Recht, sich beim Eidgenössischen Datenschutz- und Öffentlichkeitsbeauftragten (EDÖB) zu beschweren.</p>

            <h5>8. Datensicherheit</h5>
            <p>Wir treffen angemessene technische und organisatorische Massnahmen, um Ihre Daten vor Verlust, Missbrauch und unbefugtem Zugriff zu schützen.</p>

            <h5>9. Änderungen dieser Erklärung</h5>
            <p>Wir passen diese Datenschutzerklärung an, wenn sich die Rechtslage oder unsere Datenverarbeitung ändert. Es gilt jeweils die auf dieser Seite veröffentlichte, aktuelle Fassung.</p>
        </div>
    </section>
@endsection
