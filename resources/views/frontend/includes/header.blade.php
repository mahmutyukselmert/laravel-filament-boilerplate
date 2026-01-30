<header class="header-main w-100">
    <div class="top-bar pt-2">
        <div class="container px-md-0">
            <div class="d-none d-sm-flex justify-content-between align-items-center">
                <address class="contact-info d-md-flex align-items-center gap-2 ms-1">
                    @if(settings()->email)
                    <a href="mailto:{{ settings()->email }}">
                        <i class="icon-envelope"></i>
                        <span class="animation-text">{{ settings()->email }}</span>
                    </a>
                    @endif
                    @if(settings()->phone)
                    <a href="tel:{{ settings()->phone }}">
                        <i class="icon-phone"></i>
                        <span class="animation-text">{{ formatPhone(settings()->phone) }}</span>
                    </a>
                    @endif
                    @if(settings()->whatsapp)
                    <a href="tel:{{ settings()->whatsapp }}">
                        <i class="icon-whatsapp"></i>
                        <span class="animation-text">{{ formatPhone(settings()->whatsapp) }}</span>
                    </a>
                    @endif
                </address>

                <div class="d-flex align-items-center">
                    {{-- Dil Değiştirici --}}
                    <div class="language-wrapper me-2">
                        @foreach(\App\Models\Language::where('active', true)->get() as $language)
                            @if(app()->getLocale() != $language->code)
                                <a href="{{ url($language->code) }}" class="text-decoration-none">
                                    <span> {{ strtoupper($language->code) }} </span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                    <hr class="vr mx-1" />
                    <div class="social-icons d-flex gap-2 px-2">
                        <a href="{{ settings()->facebook ?? '#' }}"><i class="icon-facebook"></i></a>
                        <a href="{{ settings()->x_twitter ?? '#' }}"><i class="icon-logo-x"></i></a>
                        <a href="{{ settings()->instagram ?? '#' }}"><i class="icon-instagram"></i></a>
                        <a href="{{ settings()->linkedin ?? '#' }}"><i class="icon-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container px-0">
        <nav class="navbar navbar-expand-lg py-1">
            <div class="logo-wrapper">
                <a class="navbar-brand text-primary fw-bold d-flex align-items-center justify-content-between" href="{{ url('/') }}">
                    <i class="icon-gem"></i>
                    <span class="fw-bold ms-2 animation-text">ELIZ VIP</span>
                </a>
            </div>

            <button class="navbar-toggler custom-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="icon-close-large menu-close-icon"></i>
                <i class="icon-menu-hamburger menu-open-icon"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="{{ url('/') }}" class="nav-link">@t('Ana Sayfa')</a></li>
                    <li class="nav-item"><a href="{{ url('/hakkimizda') }}" class="nav-link">@t('Hakkımızda')</a></li>
                    
                    <li class="nav-item dropdown custom-dropdown-menu">
                        <a href="#" class="nav-link dropdown-toggle" id="navbarDropdown2">
                            <span>@t('Hizmetlerimiz')</span>
                            <i class="icon-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach($services as $service)
                                <li>
                                    <a class="dropdown-item" href="{{ $service->getDynamicUrl() }}">
                                        {{ $service->activeTranslation->title ?? $service->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                    <li class="nav-item"><a href="{{ url('/bize-ulasin') }}" class="nav-link">@t('Bize Ulaşın')</a></li>
                </ul>

                <div class="header-right-button ms-lg-3 scroll-reveal-right">
                    <a href="#" class="btn btn-outline-primary btn-quote rounded-pill px-4 py-2 vip-offer-modal-btn">
                        <span>@t('TEKLİF AL')</span>
                        <i class="icon-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>

<div class="mobile-bar">
    <a href="tel:{{ settings()->phone_call }}">
        <i class="icon-phone"></i>
        <span class="animation-text">@t('Ara')</span>
    </a>
    <a href="#" class="vip-offer-modal-btn rounded-pill text-primary px-4 py-2">
        <i class="icon-gem"></i>
        <span class="animation-text">@t('TEKLİF AL')</span>
    </a>
    <a href="tel:{{ settings()->whatsapp }}" class="btn btn-whatsapp d-flex align-items-center justify-content-center">
        <i class="icon-whatsapp fs-3"></i>
        <span class="animation-text">@t('WhatsApp')</span>
    </a>
</div>