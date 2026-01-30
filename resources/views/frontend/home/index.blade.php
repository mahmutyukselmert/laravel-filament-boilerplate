@extends('frontend.layouts.app')

@section('content')
    @include('frontend.includes.hero')

    @php
        $section1 = get_section('ana-sayfa-1-icerik');
        $translation1 = $section1?->active_translation;
        $images1 = $section1?->images ?? [];

        $section2 = get_section('ana-sayfa-2-icerik');
        $translation2 = $section2?->active_translation;
        $images2 = $section2?->images ?? [];

        $section_why_about = get_section('ana-sayfa-neden-biz');
        $translation_why_about = $section_why_about?->active_translation;
        $images_why_about = $section_why_about?->images ?? [];
    @endphp

    @php
        $pageSections = is_array($translation->sections) ? $translation->sections : json_decode($translation->sections, true);
        $currentLangId = session('language_id', 1);
    @endphp

    @if ($section1)
        <section class="about-section dark-gradient-bottom">
            <div class="container">
                <div class="row col-12 col-lg-12 mx-auto pb-5">

                    <div class="col-12 col-md-5">
                        <div class="about-image-area">
                            <div class="position-relative">
                                <div class="image-right-top">
                                    <img src="{{ asset('storage/' . ($images1[0] ?? 'default.jpg')) }}"
                                        alt="{{ $translation1?->title }}" class="shadow scroll-reveal-right">
                                </div>
                                <div class="image-dot-pattern">
                                    <img src="{{ asset('storage/' . ($images1[1] ?? 'default.jpg')) }}"
                                        alt="{{ $translation1?->title }}" class="img-fluid rounded shadow">
                                </div>
                                <div class="image-left-bottom">
                                    <img src="{{ asset('storage/' . ($images1[2] ?? 'default.jpg')) }}"
                                        alt="{{ $translation1?->title }}" class="shadow scroll-reveal-left">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 offset-lg-1 px-lg-3 scroll-reveal-right pt-3">
                        <div class="section-heading">
                            <h2 class="section-title animation-text">{{ $translation1?->title }}</h2>
                            <h3 class="section-subtitle animation-text">{{ $translation1?->subtitle }}</h3>
                        </div>
                        <div class="section-content">
                            {!! $translation1?->description !!}
                        </div>
                        <a href="{{ $translation1?->buttons[0]['url'] ?? '#' }}"
                            class="btn btn-outline-primary">{{ $translation1?->buttons[0]['text'] ?? 'Button Text' }}</a>
                    </div>

                </div>

                <div class="divider-line scroll-reveal-left">
                    <div class="divider-icon scroll-reveal-bottom">
                        <i class="icon-steering-wheel"></i>
                    </div>
                </div>

                <div class="row col-12 col-lg-12 mx-auto py-1 reveal-3d">

                    <div class="col-12 col-md-6 px-lg-3 scroll-reveal-right pt-2 pb-5">

                        <div class="section-heading">
                            <h2 class="section-title animation-text">{{ $translation2?->title }}</h2>
                            <h3 class="section-subtitle animation-text">{{ $translation2?->subtitle }}</h3>
                        </div>
                        <div class="section-content">
                            {!! $translation2?->description !!}
                        </div>
                        <a href="{{ $translation2?->buttons[0]['url'] }}"
                            class="btn btn-outline-primary">{{ $translation2?->buttons[0]['text'] }}</a>
                    </div>

                    <div class="col-12 col-md-5 offset-lg-1 d-flex align-items-center justify-content-center">
                        <div class="position-relative ">
                            <div class="image-dot-pattern right-top-dot left-bottom-dot">
                                <img src="{{ asset('storage/' . ($images2[0] ?? 'default.jpg')) }}"
                                    alt="{{ $translation2?->title }}" class="img-fluid rounded shadow">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    {{-- Neden Biz --}}
    @if ($section_why_about)
        <section class="why-elizvip-section top-gradient-dark bottom-gradient-dark py-5">
            <div class="parallax-bg"
                style="background-image: url('{{ asset('storage/' . $images_why_about[0] ?? asset('./assets/images/feature-1-bg.webp')) }}');">
            </div>
            <div class="container">
                <div class="row">
                    <h2 class="section-title text-center text-primary scroll-reveal-bottom">
                        {{ $translation_why_about?->title }}
                    </h2>
                    <h3 class="section-subtitle text-center text-white mb-5 scroll-reveal-bottom">
                        {{ $translation_why_about?->subtitle }}
                    </h3>
                </div>
                <div class="row align-items-center row-gap-5">
                    @foreach ($translation_why_about->content as $index => $item)
                        <div class="col-lg-4 scroll-reveal-left">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-icon">
                                        <i class="{{ $item['icon'] ?? 'icon-steering-wheel' }}"></i>
                                    </div>
                                    <h5 class="card-title mb-3">{{ $item['title'] ?? 'Title' }}</h5>
                                    <p class="card-text">{{ $item['text'] ?? 'Text' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

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
                                                // Mevcut dildeki çeviriyi al, yoksa ilk bulduğunu (muhtemelen TR) al
                                                $translation = $item->translations->first() ?? $item->translations->where('language_id', 1)->first();
                                            @endphp
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

    {{-- Hakkımızda --}}
    @if ($section1)
        <section class="about-section bg-white">
            <div class="container">
                <div class="row col-12 col-lg-12 mx-auto pb-5">

                    <div class="col-12 col-md-5">
                        <div class="about-image-area">
                            <div class="position-relative">
                                <div class="image-right-top">
                                    <img src="{{ asset('storage/' . ($images1[0] ?? 'default.jpg')) }}"
                                        alt="{{ $translation1?->title }}" class="shadow scroll-reveal-right">
                                </div>
                                <div class="image-dot-pattern">
                                    <img src="{{ asset('storage/' . ($images1[1] ?? 'default.jpg')) }}"
                                        alt="{{ $translation1?->title }}" class="img-fluid rounded shadow">
                                </div>
                                <div class="image-left-bottom">
                                    <img src="{{ asset('storage/' . ($images1[2] ?? 'default.jpg')) }}"
                                        alt="{{ $translation1?->title }}" class="shadow scroll-reveal-left">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 offset-lg-1 px-lg-3 scroll-reveal-right pt-3">
                        <div class="section-heading">
                            <h2 class="section-title animation-text text-primary">{{ $translation1?->title }}</h2>
                            <h3 class="section-subtitle animation-text">{{ $translation1?->subtitle }}</h3>
                        </div>
                        <div class="section-content">
                            {!! $translation1?->description !!}
                        </div>
                        <a href="{{ $translation1?->buttons[0]['url'] ?? '#' }}"
                            class="btn btn-outline-primary">{{ $translation1?->buttons[0]['text'] ?? 'Button Text' }}</a>
                    </div>

                </div>

                <div class="divider-line scroll-reveal-left">
                    <div class="divider-icon scroll-reveal-bottom">
                        <i class="icon-steering-wheel"></i>
                    </div>
                </div>

                <div class="row col-12 col-lg-12 mx-auto py-1 reveal-3d">

                    <div class="col-12 col-md-6 px-lg-3 scroll-reveal-right pt-2 pb-5">

                        <div class="section-heading">
                            <h2 class="section-title animation-text">{{ $translation2?->title }}</h2>
                            <h3 class="section-subtitle animation-text text-primary">{{ $translation2?->subtitle }}</h3>
                        </div>
                        <div class="section-content">
                            {!! $translation2?->description !!}
                        </div>
                        <a href="{{ $translation2?->buttons[0]['url'] }}"
                            class="btn btn-outline-primary">{{ $translation2?->buttons[0]['text'] }}</a>
                    </div>

                    <div class="col-12 col-md-5 offset-lg-1 d-flex align-items-center justify-content-center">
                        <div class="position-relative ">
                            <div class="image-dot-pattern right-top-dot left-bottom-dot">
                                <img src="{{ asset('storage/' . ($images2[0] ?? 'default.jpg')) }}"
                                    alt="{{ $translation2?->title }}" class="img-fluid rounded shadow">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    @foreach ($pageSections as $block)
        @if ($block['type'] === 'global_section_ref')
            @php
                $section = \App\Models\Section::with(['translations' => function($q) use ($currentLangId) {
                    $q->where('language_id', $currentLangId);
                }])->find($block['data']['section_id']);

                $sectionTrans = $section?->translations->first();
                $sectionImages = $section?->images ?? [];
            @endphp

            @if($section && $sectionTrans)
                @include('frontend.components.' . $section->type, [
                    'section' => $section,
                    'trans'   => $sectionTrans,
                    'images'  => $sectionImages
                ])
            @endif
        @endif
    @endforeach

    @include('frontend.components.our-partners')

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/slider.min.js') }}"></script>
@endpush