@extends('frontend.layouts.app')

@section('main_class', 'sub-page')
@section('content')
    <section class="page-header">
        <div class="page-header__bg"
            style="background-image: url('{{ asset('storage/' . $page->image ?? asset('./assets/images/feature-1-bg.webp')) }}');">
        </div>
        <div class="page-header__overlay"></div>

        <div class="container page-header__content">
            <h1 class="hero-title">{{ $translation?->title }}</h1>
            <h2 class="hero-subtitle">{{ $translation?->subtitle }}</h2>
        </div>
    </section>

    @php
        $pageSections = is_array($translation->sections)
            ? $translation->sections
            : json_decode($translation->sections, true);
        $currentLangId = session('language_id', 1);

        // Section referanslarını topluyoruz
        $refs = collect($pageSections)->where('type', 'global_section_ref')->values();
        $skipNext = false; // İkili tasarımı kontrol etmek için
    @endphp

    @foreach ($refs as $index => $ref)
        @php
            if ($skipNext) {
                $skipNext = false;
                continue;
            }

            $section = \App\Models\Section::with([
                'translations' => function ($q) use ($currentLangId) {
                    $q->where('language_id', $currentLangId);
                },
            ])->find($ref['data']['section_id']);

            $trans = $section?->translations->first();
            $images = $section?->images ?? [];
        @endphp

        @if ($section && $trans)
            {{-- DURUM 1: STATS TASARIMI --}}
            @if ($section->type == 'stats')
                <section class="stats-section py-5">
                    <div class="parallax-image">
                        <img src="{{ asset('storage/' . ($images[0] ?? 'default.jpg')) }}" alt="{{ $trans->title }}"
                            class="img-fluid">
                    </div>
                    <div class="container">
                        <div class="row text-center mb-4 scroll-reveal-top">
                            <span class="section-title">{{ $trans->subtitle }}</span>
                            <h2 class="section-subtitle">{{ $trans->title }}</h2>
                        </div>
                        <div class="row col-12 col-lg-12 mx-auto position-relative">
                            <div class="d-flex flex-column flex-lg-row gap-md-3 gap-lg-5 col-md-12 justify-content-center px-0 scroll-reveal-bottom">
                                {{-- Extra Fields içinde stats verilerini döndüğünü varsayıyorum --}}
                                @if (!empty($trans->content) && is_array($trans->content))
                                    @foreach ($trans->content as $stat)
                                        <div class="stats-item">
                                            <i class="{{ $stat['icon'] ?? 'icon-chauffeur' }}"></i>
                                            <h3 class="stats-number">
                                                <output class="countup"
                                                    data-value="{{ $stat['number'] ?? 0 }}">{{ $stat['number'] ?? 0 }}</output>+
                                            </h3>
                                            <p class="stats-text">{{ $stat['label'] }}</p>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                {{-- DURUM 2: VARSAYILAN İKİLİ TASARIM (Default Tipindeyse) --}}
            @elseif($section->type == 'default')
                @php
                    // Bir sonraki section da "default" ise onu "ikinci row" olarak al
                    $nextRef = $refs->get($index + 1);
                    $section2 = null;
                    if ($nextRef) {
                        $nextSection = \App\Models\Section::find($nextRef['data']['section_id']);
                        if ($nextSection && $nextSection->type == 'default') {
                            $section2 = \App\Models\Section::with([
                                'translations' => function ($q) use ($currentLangId) {
                                    $q->where('language_id', $currentLangId);
                                },
                            ])->find($nextRef['data']['section_id']);
                            $skipNext = true; // Sonrakini bu döngüde kullandık, pas geç
                        }
                    }
                    $trans2 = $section2?->translations->first();
                    $images2 = $section2?->images ?? [];
                @endphp

                <section class="about-section dark-gradient-bottom">
                    <div class="container">
                        {{-- BİRİNCİ ROW --}}
                        <div class="row col-12 col-lg-12 mx-auto pb-5">
                            <div class="col-12 col-md-5">
                                <div class="about-image-area">
                                    <div class="position-relative">
                                        <div class="image-right-top"><img
                                                src="{{ asset('storage/' . ($images[0] ?? 'default.jpg')) }}"
                                                class="shadow"></div>
                                        <div class="image-dot-pattern"><img
                                                src="{{ asset('storage/' . ($images[1] ?? 'default.jpg')) }}"
                                                class="img-fluid rounded shadow"></div>
                                        <div class="image-left-bottom"><img
                                                src="{{ asset('storage/' . ($images[2] ?? 'default.jpg')) }}"
                                                class="shadow"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 offset-lg-1 px-lg-3 pt-3">
                                <div class="section-heading">
                                    <h2 class="section-title">{{ $trans->title }}</h2>
                                    <h3 class="section-subtitle">{{ $trans->subtitle }}</h3>
                                </div>
                                <div class="section-content">{!! $trans->description !!}</div>
                                @if (!empty($trans->buttons))
                                    <a href="{{ $trans->buttons[0]['url'] ?? '#' }}"
                                        class="btn btn-outline-primary">{{ $trans->buttons[0]['text'] }}</a>
                                @endif
                            </div>
                        </div>

                        {{-- İKİNCİ ROW (Varsa) --}}
                        @if ($section2 && $trans2)
                            <div class="divider-line">
                                <div class="divider-icon"><i class="icon-steering-wheel"></i></div>
                            </div>
                            <div class="row col-12 col-lg-12 mx-auto py-1">
                                <div class="col-12 col-md-6 px-lg-3 pt-2 pb-5">
                                    <div class="section-heading">
                                        <h2 class="section-title">{{ $trans2->title }}</h2>
                                        <h3 class="section-subtitle">{{ $trans2->subtitle }}</h3>
                                    </div>
                                    <div class="section-content">{!! $trans2->description !!}</div>
                                    @if (!empty($trans2->buttons))
                                    <a href="{{ $trans2->buttons[0]['url'] ?? '#' }}"
                                        class="btn btn-outline-primary">{{ $trans2->buttons[0]['text'] }}</a>
                                    @endif
                                </div>
                                <div class="col-12 col-md-5 offset-lg-1 d-flex align-items-center justify-content-center">
                                    <div class="image-dot-pattern right-top-dot left-bottom-dot">
                                        <img src="{{ asset('storage/' . ($images2[0] ?? 'default.jpg')) }}"
                                            class="img-fluid rounded shadow">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

        @endif
    @endforeach

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/slider.min.js') }}"></script>
@endpush
