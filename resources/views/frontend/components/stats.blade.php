<section class="stats-section py-5">
    <div class="parallax-image">
        <img src="{{ asset('storage/' . ($images[0] ?? 'default.jpg')) }}" alt="{{ $trans->title }}" class="img-fluid">
    </div>
    <div class="container">
        <div class="row text-center mb-4 scroll-reveal-top">
            <span class="section-title">{{ $trans->title }}</span>
            <h2 class="section-subtitle">{{ $trans->subtitle }}</h2>
        </div>
        
        <div class="row col-12 col-lg-12 mx-auto position-relative">
            <div class="d-flex flex-column flex-lg-row gap-md-3 gap-lg-5 col-md-12 justify-content-center px-0 scroll-reveal-bottom">
                @foreach($trans->content as $item)
                    <div class="stats-item">
                        <i class="{{ $item['icon'] ?? 'icon-chauffeur' }}"></i>
                        <h3 class="stats-number">
                            <output class="countup" data-value="{{ $item['value'] ?? 0 }}">{{ $item['value'] ?? 0 }}</output>+
                        </h3>
                        <p class="stats-text">{{ $item['title'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>