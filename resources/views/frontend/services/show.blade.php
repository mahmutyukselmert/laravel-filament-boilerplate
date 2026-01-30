@extends('frontend.layouts.app')

@section('title', $translation->meta_title ?? $translation->title)
@section('meta_description', $translation->meta_description)
@section('meta_keywords', $translation->meta_keywords)

@push('styles')
    <style>
        .content h3 {
            color: var(--primary);
            margin-bottom: 3rem;
        }
    </style>
@endpush

@section('main_class', 'sub-page')
@section('content')
    <section class="page-header">
        <div class="page-header__bg"
            style="background-image: url('{{ asset('storage/' . $service->image ?? asset('assets/images/feature-1-bg.webp')) }}');">
        </div>
        <div class="page-header__overlay"></div>

        <div class="container page-header__content">
            <h1 class="hero-title">{{ $translation?->title }}</h1>
            <h2 class="hero-subtitle">{{ $translation?->subtitle }}</h2>
        </div>
    </section>

    <section class="wrapper service-detail-section py-5">
        <div class="container">
            <div class="row align-items-center col-md-12 mx-auto justify-content-between">
                <div class="col-md-6 scroll-reveal-left">
                    <div class="hero-description mt-4 mt-lg-0">
                        <div class="col-lg-12 col-12">
                            <div class="content">
                                {!! $translation?->content !!}
                            </div>
                            <div class="row">
                                <div class="col-auto mt-4">
                                    <a href="#faq-section" class="btn btn-outline-primary rounded-full w-100 "> Aklıma
                                        Takılan Sorular Var? </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($service->gallery->count() > 0)
                <div class="col-md-5 col-11 mt-5 mt-md-0">

                    <div class="row position-relative">
                        <div class="col-lg-9 col-12 mx-auto">
                            <div class="product-detail-image scroll-reveal-right">
                                <div class="embla slider-view-1" id="productDetailCarousel">
                                    <div class="embla__viewport">
                                        <div class="embla__container product-cards-container">
                                            
                                            @foreach ($service->gallery as $image)
                                            <div class="embla__slide">
                                                <div class="product-card">
                                                    <div class="product-image">
                                                        <img src="{{ asset('storage/' . ($image->file_path ?? 'default.jpg')) }}"
                                                            alt="{{ $image->localized_title }}"
                                                            class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-4 col-lg-0 mx-auto align-self-center justify-content-center product-detail-slide-controls">
                            <div class="embla-controls">
                                <button class="prev-btn-pd">
                                    <i class="icon-arrow-left-circle"></i>
                                </button>
                                <button class="next-btn-pd">
                                    <i class="icon-arrow-right-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                @endif

            </div>
        </div>
    </section>

    {{-- Ürünler (Slider) --}}
    <section class="homepage-products-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 mx-auto">
                    <div class="section-heading scroll-reveal-right">
                        <div class="row d-flex align-items-center">
                            <div class="col-lg-6">
                                <h2 class="section-title animation-text">@t('ELIZ VIP TRANSFER HİZMETLERİ')</h2>
                                <h3 class="section-subtitle animation-text">@t('Size bugün nereye götürelim?')</h3>
                            </div>
                            <div class="col-lg-6">
                                <div class="content animation-text">
                                    <p>@t('Güvenli Yolculuk İçin Doğru Seçim.')</p>
                                </div>
                                <div class="embla-controls">
                                    <button class="prev-btn">
                                        <i class="icon-arrow-left-circle"></i>
                                    </button>
                                    <button class="next-btn">
                                        <i class="icon-arrow-right-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-11 mx-auto">

                    <div class="divider-line">
                        <div class="divider-icon">
                            <i class="icon-steering-wheel"></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <div class="product-slider-wrapper">
                                <div class="embla slider-view-4" id="productCarousel">
                                    <div class="embla__viewport">
                                        <div class="embla__container product-cards-container">
                                            @foreach ($services as $item)
                                            @php
                                                $translation = $item->translations->first() ?? $item->translations->where('language_id', 1)->first();
                                            @endphp
                                            @if ($item->id != $service->id)
                                                <div class="embla__slide col-lg-4 col-md-6 col-sm-12">
                                                    <a href="{{ $translation ? url('/' . $translation->slug) : '#' }}"
                                                        class="product-card-link">
                                                        <div class="product-card">
                                                            <div class="product-image">
                                                                <img src="{{ asset('storage/' . ($item->image ?? 'default.jpg')) }}"
                                                                    alt="{{ $translation ? $translation->title : $item->title }}"
                                                                    class="img-fluid">
                                                            </div>
                                                            <div class="product-content">
                                                                <h3 class="product-title">
                                                                    {{ $translation ? $translation->title : $item->title }} </h3>
                                                                <button class="product-btn"> @t('Hizmet Al') </button>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    @php
        $pageSections = $translation->sections ?? [];
        $currentLangId = $language->id;
    @endphp

    @foreach ($pageSections as $block)
        @if ($block['type'] === 'global_section_ref')
            @php
                $section = \App\Models\Section::with([
                    'translations' => function ($q) use ($currentLangId) {
                        $q->where('language_id', $currentLangId);
                    },
                ])->find($block['data']['section_id']);

                $trans = $section?->translations->first();
                $images = $section?->images ?? [];
            @endphp

            @if ($section && $trans)
                {{-- Bölüm tipine göre (faq, stats, default) blade dosyasını çağır --}}
                @include('frontend.sections.' . $section->type, [
                    'section' => $section,
                    'trans' => $trans,
                    'images' => $images,
                ])
            @endif
        @endif
    @endforeach

@endsection
