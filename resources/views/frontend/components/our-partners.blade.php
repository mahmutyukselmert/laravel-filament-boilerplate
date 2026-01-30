@php
    $section_our_parners = get_section('cozum-ortaklarimiz');
    $translation_our_parners = $section_our_parners?->active_translation;
@endphp
<section class="our-companies bg-white">
    <div class="container">
        <div class="row text-center mb-4">
            <h1 class="section-subtitle">{{ $translation_our_parners?->title }}</h1>
            <h2 class="section-title">{{ $translation_our_parners?->subtitle }}</h2>
        </div>
        <div class="brand-box-container py-4 embla" id="partnerCarousel">
            <div class="embla__viewport">
                <div class="embla__container brand-box">
                    @foreach ($translation_our_parners->content as $item)
                        <a href="#" class="brand-logo embla__slide ">
                            <img src="{{ asset('storage/' . $item['image'] ) }}" alt="{{ $item['title'] }}"
                                class="partner-logo" loading="lazy">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
