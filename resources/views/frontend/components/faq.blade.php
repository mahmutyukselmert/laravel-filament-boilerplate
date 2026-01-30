@php
    // Admin panelinde 'content' alanına [{ 'question': '...', 'answer': '...' }] formatında veri girdiğini varsayıyoruz
    $faqItems = is_array($trans->content) ? $trans->content : json_decode($trans->content, true) ?? [];
    
    // Sağ alttaki ikonlu özellikleri de 'extra_fields' içinde tutabiliriz
    $features = is_array($trans->extra_fields) ? $trans->extra_fields : json_decode($trans->extra_fields, true) ?? [];
@endphp

<section class="faq-section bg-white pt-5" id="faq-section">
    <div class="container">
        <div class="row align-items-center g-5">
            {{-- SOL TARAF: AKORDİYON --}}
            <div class="col-lg-5">
                <div class="faq-card">
                    <div class="decor-lines-top-1"></div>
                    <div class="decor-lines-top-2"></div>
                    <div class="decor-lines-top-3"></div>
                    
                    <div class="accordion accordion-flush" id="faqAccordion">
                        @foreach($faqItems as $index => $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" 
                                            type="button" 
                                            data-bs-toggle="collapse"
                                            data-bs-target="#q{{ $index }}">
                                        {{ $item['question'] ?? ($item['title'] ?? '') }}
                                    </button>
                                </h2>
                                <div id="q{{ $index }}" 
                                     class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {!! $item['answer'] ?? ($item['description'] ?? '') !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="decor-lines-1"></div>
                    <div class="decor-lines-2"></div>
                    <div class="decor-lines-3"></div>
                </div>
            </div>

            {{-- SAĞ TARAF: İÇERİK VE ÖZELLİKLER --}}
            <div class="col-lg-7">
                <div class="content-box ps-lg-4">
                    <span class="section-subtitle"> {{ $trans->title }}</span>
                    <h2 class="section-title mb-4"> {{ $trans->subtitle }} </h2>
                    
                    <div class="description">
                        {!! $trans->description !!}
                    </div>

                    @if(!empty($trans->buttons))
                        <a href="{{ $trans->buttons[0]['url'] ?? '#' }}" class="btn btn-outline-dark rounded-pill px-4 py-2">
                            {{ $trans->buttons[0]['text'] ?? 'Bize Ulaşın' }}
                        </a>
                    @endif

                    {{-- İKONLU ÖZELLİK LİSTESİ --}}
                    <div class="features-list mt-5">
                        @foreach($features as $feature)
                            <div class="feature-item">
                                <div class="icon">
                                    @if(str_contains($feature['icon'] ?? '', '<svg'))
                                        {!! $feature['icon'] !!}
                                    @else
                                        <i class="{{ $feature['icon'] ?? 'bi bi-check-circle' }}"></i>
                                    @endif
                                </div>
                                <div class="text">
                                    <h4>{{ $feature['title'] ?? '' }}</h4>
                                    <p>{{ $feature['description'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="divider-line scroll-reveal-left">
            <div class="divider-icon scroll-reveal-bottom">
                <i class="icon-steering-wheel"></i>
            </div>
        </div>
    </div>
</section>