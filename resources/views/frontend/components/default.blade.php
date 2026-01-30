<section class="about-section dark-gradient-bottom ">
    <div class="container">
        <div class="row col-12 col-lg-12 mx-auto pb-5">

            <div class="col-12 col-md-5">
                <div class="about-image-area">
                    <div class="position-relative">
                        <div class="image-right-top">
                            <img src="{{ asset('storage/' . ($images[0] ?? 'default.jpg')) }}" alt="{{ $trans->title }}"
                                class="shadow scroll-reveal-right">
                        </div>
                        <div class="image-dot-pattern">
                            <img src="{{ asset('storage/' . ($images[1] ?? 'default.jpg')) }}" alt="{{ $trans->title }}"
                                class="img-fluid rounded shadow">
                        </div>
                        <div class="image-left-bottom">
                            <img src="{{ asset('storage/' . ($images[2] ?? 'default.jpg')) }}" alt="{{ $trans->title }}"
                                class="shadow scroll-reveal-left">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 offset-lg-1 px-lg-3 scroll-reveal-right pt-3">
                <div class="section-heading">
                    <h2 class="section-title animation-text">{{ $trans->title }}</h2>
                    <h3 class="section-subtitle animation-text">{{ $trans->subtitle }}</h3>
                </div>
                <div class="section-content">
                    {!! $trans->description !!}
                </div>
                <a href="{{ $trans->buttons[0]['url'] ?? '#' }}"
                    class="btn btn-outline-primary">{{ $trans->buttons[0]['text'] ?? 'BİZE ULAŞIN' }}</a>
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
                    <h2 class="section-title animation-text">{{ $trans->title }}</h2>
                    <h3 class="section-subtitle animation-text">{{ $trans->subtitle }}</h3>
                </div>
                <div class="section-content">
                    {{ $trans->description }}
                </div>
                <a href="{{ $trans->buttons[0]['url'] ?? '#' }}"
                    class="btn btn-outline-primary">{{ $trans->buttons[0]['text'] ?? 'BİZE ULAŞIN' }}</a>
            </div>

            <div class="col-12 col-md-5 offset-lg-1 d-flex align-items-center justify-content-center">
                <div class="position-relative ">
                    <div class="image-dot-pattern right-top-dot left-bottom-dot">
                        <img src="{{ asset('storage/' . ($images[3] ?? 'default.jpg')) }}" alt="{{ $trans->title }}"
                            class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
