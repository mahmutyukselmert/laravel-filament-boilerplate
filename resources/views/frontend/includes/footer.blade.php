<!-- Footer -->
<footer id="footer">
    <div class="container">

        <div class="footer-top d-flex flex-wrap justify-content-between align-items-center py-4">
            <div class="footer-logo">
                <a href="{{ url('/') }}" class="text-primary fw-bold d-flex align-items-center justify-content-between">
                    @if (settings()->footer_logo)
                        <img src="{{ asset(settings()->footer_logo) }}" alt="{{ settings()->site_name }}" class="img-fluid">
                    @else
                        <i class="icon-gem"></i>
                        <span class="fw-bold ms-2 animation-text">{{ settings()->site_name }}</span>
                    @endif
                </a>
            </div>

            <div class="footer-desc d-flex align-items-center mb-0">
                <p>@t('“Bugün bizimle iletişime geçin ve lüksü deneyimleyin.”')</p>
            </div>

            <address class="footer-social mb-0">
                <div class="social-icons d-flex gap-3">
                    @if (settings()->linkedin)
                        <a href="{{ settings()->linkedin ?? '#' }}" aria-label="LinkedIn"><i class="icon-linkedin"></i></a>
                    @endif
                    
                    @if (settings()->instagram)
                        <a href="{{ settings()->instagram ?? '#' }}" aria-label="Instagram"><i class="icon-instagram"></i></a>
                    @endif
                    
                    @if (settings()->facebook)
                        <a href="{{ settings()->facebook ?? '#' }}" aria-label="Facebook"><i class="icon-facebook"></i></a>
                    @endif
                    
                    @if (settings()->x_twitter)
                        <a href="{{ settings()->x_twitter ?? '#' }}" aria-label="Twitter"><i class="icon-logo-x"></i></a>
                    @endif
                </div>
            </address>
        </div>

        <hr class="footer-divider">

        <div class="footer-links py-4">
            <div class="footer-item">
                <h5 class="footer-title">@t('Bize Ulaşın')</h5>
                <address class="contact-info d-flex flex-column align-items-start justify-content-start gap-2 ms-1">
                    <a href="mailto:{{ settings()->email }}">
                        <i class="icon-envelope"></i>
                        <span> {{ settings()->email }}</span>
                    </a>
                    <a href="tel:{{ settings()->phone }}">
                        <i class="icon-phone"></i>
                        <span>{{ formatPhone(settings()->phone) }}</span>
                    </a>
                    <a href="https://wa.me/{{ settings()->whatsapp }}">
                        <i class="icon-whatsapp"></i>
                        <span>{{ formatPhone(settings()->whatsapp) }}</span>
                    </a>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(settings()->address) }}" target="_blank">
                        <i class="icon-map-pin"></i>
                        <span>{{ settings()->address }}</span>
                    </a>
                </address>
            </div>

            @foreach($footer_menus as $menu)
                <div class="footer-widget">
                    <h5 class="footer-title">{{ $menu->translations->first()?->title ?? $menu->name }}</h5>
                    <ul class="list-unstyled">
                        @foreach($menu->items as $item)
                            <li>
                                <a href="{{ $item->url }}">
                                    {{ $item->translations->first()?->label ?? 'Başlıksız' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            
            @php 
            /*
            <div class="footer-item">
                <h5 class="footer-title">@t('Kurumsal')</h5>
                <ul class="list-unstyled">
                    <li><a href="/hakkimizda">Hakkımızda</a></li>
                    <li><a href="/neden-eliz-vip-transfer">Neden ELiz VIP Transfer?</a></li>
                    <li><a href="/vizyon-misyon">Vizyon & Misyon</a></li>
                    <li><a href="/kalite-politikasi">Hizmet Kalite Politikamız</a></li>
                    <li><a href="/kurumsal-cozumler">Çerez Politikası</a></li>
                    <li><a href="/guvenli-transfer">Kişisel Verileri Korunması Kanunu</a></li>
                </ul>
            </div>

            <div class="footer-item">
                <h5 class="footer-title">@t('Hizmetlerimiz')</h5>
                <ul class="list-unstyled">
                    @foreach (footer_menu('footer-hizmetlerimiz') as $item)
                        <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                    @endforeach
                    <li><a href="/havaalani-vip-transfer">Havalimanı VIP Transfer</a></li>
                    <li><a href="/sehir-ici-vip-transfer">Şehir İçi VIP Transfer</a></li>
                    <li><a href="/sehirler-arasi-transfer">Şehirler Arası VIP Transfer</a></li>
                    <li><a href="/ozel-soforlu-arac">Özel Şoförlü Araç Hizmeti</a></li>
                    <li><a href="/is-seyahati-transfer">İş Seyahati Transfer Hizmetleri</a></li>
                </ul>
            </div>

            <div class="footer-item">
                <h5 class="footer-title">@t('Araç Filomuz')</h5>
                <ul class="list-unstyled">
                    <li><a href="/vip-minivan">VIP Minivan Araçlar</a></li>
                    <li><a href="/luxury-sedan">Lüks Sedan Araçlar</a></li>
                    <li><a href="/genis-aile-araclari">Geniş Aile Araçları</a></li>
                    <li><a href="/konforlu-transfer-araclari">Konforlu Transfer Araçları</a></li>
                </ul>
            </div>
            */
            @endphp
        </div>
    </div>

    <div class="footer-bottom pt-3 pb-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-start">
                    <div class="copyright-text">
                        <p>© {{ date('Y') }} {{ settings()->site_name }} @t('Tüm hakları saklıdır.')</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <a href="#">
                        <i class="icon-arrow-up-circle back-to-top-icon"></i>
                    </a>
                </div>
                <div class="col-md-4 text-end">
                    <a href="https://elizyazilim.com" target="_blank" class="text-white text-decoration-none fs-7">Eliz Yazılım A.Ş.</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<div id="vip-modal" class="vip-modal">
    <div class="vip-modal-content">
        <a class="close-modal">
            <i class="icon-close-large"></i>
        </a>
        <h3><i class="icon-gem"></i> VIP Transfer Talebi</h3>
        <p>Hızlı teklif için varış noktanızı yazın.</p>

        <div class="input-group">
            <label>Nereden:</label>
            <input type="text" id="origin-input" value="Konumunuz Alınıyor..." required>
        </div>

        <div class="input-group">
            <label>Nereye:</label>
            <input type="text" id="destination-input" placeholder="Örn: Anıtkabir, Esenboğa..." required>
        </div>

        <button id="send-vip-request" class="btn btn-primary w-100 py-3 mt-3">
            TEKLİFİ WHATSAPP'TAN AL
        </button>
    </div>
</div>

<script src="{{ asset('assets/js/main.js') }}"></script>