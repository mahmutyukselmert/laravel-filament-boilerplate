@extends('frontend.layouts.app')

@php
    $pageSections = is_array($translation->sections) ? $translation->sections : json_decode($translation->sections, true);
    $currentLangId = session('language_id', 1);
@endphp

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

    <section class="contact-us-section">
        <div class="container position-relative">

            <div class="row justify-content-between align-items-center mb-5">
                <div class="row col-lg-12 col-12 mx-auto d-flex justify-content-between align-items-center">

                    <div class="col-xl-5 col-12 scroll-reveal-bottom">
                        <ul class="list-unstyled">

                            @if (settings()->email)
                            <li>
                                <span>
                                    <i class="icon-envelope"></i>
                                </span>
                                <p> <a href="mailto:{{ settings()->email }}">{{ settings()->email }}</a></p>
                            </li>
                            @endif

                            @if (settings()->phone)
                            <li>
                                <span>
                                    <i class="icon-phone"></i>
                                </span>
                                <p> <a href="tel:{{ settings()->phone }}">{{ formatPhone(settings()->phone) }}</a></p>
                            </li>
                            @endif

                            @if (settings()->whatsapp)
                            <li>
                                <span>
                                    <i class="icon-whatsapp"></i>
                                </span>
                                <p> <a href="https://wa.me/{{ settings()->whatsapp }}">{{ formatPhone(settings()->whatsapp) }}</a></p>
                            </li>
                            @endif

                            @if (settings()->address)
                            <li>
                                <span>
                                    <i class="icon-map-pin"></i>
                                </span>
                                <p>{{ settings()->address }}</p>
                            </li>
                            @endif
                        </ul>

                        <div class="d-flex mt-5 align-items-center">
                            <span class="text-white me-4">@t("Bizi Takip Edin")</span>
                            <div class="social-icons d-flex gap-3">
                                <a href="{{ settings()->linkedin }}" aria-label="LinkedIn"><i class="icon-linkedin"></i></a>
                                <a href="{{ settings()->instagram }}" aria-label="Instagram"><i class="icon-instagram"></i></a>
                                <a href="{{ settings()->facebook }}" aria-label="Facebook"><i class="icon-facebook"></i></a>
                                <a href="{{ settings()->twitter_x }}" aria-label="x"><i class="icon-logo-x"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 col-12 mt-5 mt-xl-0 scroll-reveal-bottom">
                        <div class="map">
                            @if (settings()->map)
                                {!! settings()->map !!}
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>
@endsection
