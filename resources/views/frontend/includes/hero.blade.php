<section class="hero split-hero">
    <div id="carouselSlider" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($sliders as $index => $slide)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" data-type="{{ $slide->slide_type }}" @if($slide->slide_type === 'image') data-interval="5000" @endif>
                    <div class="slide-wrapper">
                        <div class="hero-left">
                            @if($slide->slide_type === 'video')
                                {{-- Video Çağırma --}}
                                <video autoplay muted loop playsinline class="img-fluid">
                                    <source src="{{ asset('storage/' . $slide->video_path) }}" type="video/mp4">
                                </video>
                            @else
                                {{-- Görsel Çağırma --}}
                                <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title . ' - ' . $slide->subtitle }}" class="img-fluid">
                            @endif
                        </div>

                        <div class="hero-right">
                            <div class="static-content">
                                <h1 class="hero-title">{{ $slide->active_translation->title }}</h1>
                                <h2 class="hero-subtitle">{{ $slide->active_translation->subtitle }}</h2>
                                @if($slide->active_translation->content)
                                    <p class="hero-text">{{ $slide->active_translation->content }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Kontroller --}}
        @if($sliders->count() > 1)
            <div class="carousel-bottom-area">
                <div class="container mx-auto">
                    <div class="carousel-controls">
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselSlider" data-bs-slide="prev">
                            <i class="icon-arrow-left-circle"></i>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselSlider" data-bs-slide="next">
                            <i class="icon-arrow-right-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>