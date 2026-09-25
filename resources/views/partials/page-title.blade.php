@php
    // Kontextbild je Seite: expliziter $titleImage-Wert, sonst Kategoriebild, sonst Standard.
    $ptCandidates = array_filter([
        $titleImage ?? null,
        isset($titleImageSlug) ? "media/site/{$titleImageSlug}.jpg" : null,
        'media/site/hero-1.jpg',
        'assets/images/item/page-title-bg.jpg',
    ]);
    $ptImage = null;
    foreach ($ptCandidates as $cand) {
        if (is_file(public_path($cand))) { $ptImage = asset($cand); break; }
    }
    $ptImage ??= asset('assets/images/item/page-title-bg.jpg');
@endphp
<div class="tf-page-title-3 flat-spacing-10" style="position:relative;background-image:linear-gradient(rgba(20,14,10,.55),rgba(20,14,10,.55)),url('{{ $ptImage }}');background-size:cover;background-position:center;">
    <div class="container-full" style="position:relative;">
        <div class="row">
            <div class="col-12">
                <ul class="breadcrumbs d-flex align-items-center justify-content-center flex-wrap gap-2 text-caption" style="color:#fff;">
                    <li><a href="{{ route('home') }}" class="link" style="color:#fff;">Startseite</a></li>
                    <li><i class="icon icon-ArrowRight"></i></li>
                    @isset($crumbParent)
                        <li><a href="{{ $crumbParent['url'] }}" class="link" style="color:#fff;">{{ $crumbParent['label'] }}</a></li>
                        <li><i class="icon icon-ArrowRight"></i></li>
                    @endisset
                    <li class="js-page-title">{{ $pageTitle }}</li>
                </ul>
                <h3 class="heading text-center js-page-title" style="color:#fff;">{{ $pageTitle }}</h3>
            </div>
        </div>
    </div>
</div>
