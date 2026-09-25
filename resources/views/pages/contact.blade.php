@extends('layouts.omniva')

@section('title', 'Kontakt')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Kontakt aufnehmen', 'titleImageSlug' => 'contact-1'])
    <section class="flat-spacing-9">
        <div class="container">
            @php $c = is_file(public_path('media/site/contact-1.jpg')) ? asset('media/site/contact-1.jpg') : null; @endphp
            @if ($c)
                <img src="{{ $c }}" alt="Brennholz" class="rounded-16 w-100 mb-5" style="object-fit:cover;max-height:340px;">
            @endif
            <div class="row">
                <div class="col-lg-5 mb-4">
                    <h6>Kontaktdaten</h6>
                    <p class="text-caption">
                        Heri Brennholz GmbH<br>
                        Fiderholzstrasse 7<br>
                        4562 Biberist, Schweiz<br>
                        Tel.: <a href="tel:+41786099516">+41 78 609 95 16</a><br>
                        E-Mail: <a href="mailto:kontakt@heribrennholzgmbh.com">kontakt@heribrennholzgmbh.com</a></p>
                    <p class="text-caption">Montag bis Freitag, 8–18 Uhr.</p>
                    <p class="text-caption">Kostenlose Lieferung in der ganzen Schweiz in 1 bis 2 Werktagen.</p>
                </div>
                <div class="col-lg-7">
                    @if ($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif
                    <form action="{{ route('pages.contact.submit') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-6"><input class="form-control" name="name" placeholder="Name *" value="{{ old('name') }}" required></div>
                        <div class="col-md-6"><input class="form-control" type="email" name="email" placeholder="E-Mail *" value="{{ old('email') }}" required></div>
                        <div class="col-12"><input class="form-control" name="subject" placeholder="Betreff" value="{{ old('subject') }}"></div>
                        <div class="col-12"><textarea class="form-control" name="message" rows="5" placeholder="Ihre Nachricht *" required>{{ old('message') }}</textarea></div>
                        <div class="col-12"><button class="tf-btn btn-fill animate-btn" type="submit"><span>Senden</span></button></div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
