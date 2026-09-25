@extends('layouts.omniva')

@section('title', 'Meine Favoriten')

@section('content')
    @include('partials.page-title', ['pageTitle' => 'Meine Favoriten'])

    <section class="flat-spacing-9">
        <div class="container">
            @if ($items->isEmpty())
                <div class="text-center py-5">
                    <p class="mb-3">Votre liste de favoris est vide.</p>
                    <a href="{{ route('shop.index') }}" class="tf-btn btn-fill animate-btn"><span>Zum Shop</span></a>
                </div>
            @else
                <div class="tf-grid-layout tf-col-2 lg-col-4 gap-20">
                    @foreach ($items as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
