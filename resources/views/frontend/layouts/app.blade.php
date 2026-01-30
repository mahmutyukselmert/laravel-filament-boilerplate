<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ELİZ VIP TRANSFER | Ankara Konforlu Ulaşım')</title>
    
    {{-- Dinamik Meta Etiketleri --}}
    <meta name="description" content="@yield('meta_description', 'Eliz Vip Transfer ile şehir içi, şehir dışı ve havaalanı VIP seyahat hizmetleri. Güvenli ve lüks ulaşım.')" />
    <meta name="keywords" content="@yield('meta_keywords', 'Eliz VIP Transfer, VIP transfer, havaalanı transfer, Ankara transfer')" />
    
    {{-- Canonical (SEO için önemli) --}}
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- CSS Dosyaları --}}
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    
    @stack('styles')
</head>
<body>
    @include('frontend.includes.header')

    <main class="@yield('main_class')">
        @yield('content') {{-- Sayfa içeriği buraya gelecek --}}
    </main>

    @include('frontend.includes.footer')

    {{-- Sadece bazı sayfalara özel JS eklemek için --}}
    @stack('scripts')
</body>
</html>