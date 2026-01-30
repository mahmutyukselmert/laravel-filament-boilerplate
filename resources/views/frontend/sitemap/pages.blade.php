<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    
    {{-- 1. ANA SAYFA --}}
    @if($homePage)
    <url>
        <loc>{{ url($language->code == 'tr' ? '/' : $language->code) }}</loc>
        <lastmod>{{ $homePage->updated_at?->toIso8601String() ?? now()->toIso8601String() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    @endif

    {{-- 2. HİZMETLER (Senin modelindeki metodu kullanıyoruz) --}}
    @foreach($services as $service)
        @php $trans = $service->translations->first(); @endphp
        @if($trans && $trans->slug)
        <url>
            <loc>{{ $service->getDynamicUrl() }}</loc>
            {{-- ServiceTranslation'da timestamp yoksa, Service ana tablosunun tarihini kullanıyoruz --}}
            <lastmod>{{ $service->updated_at?->toIso8601String() ?? now()->toIso8601String() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
        @endif
    @endforeach

    {{-- 3. DİĞER SAYFALAR --}}
    @foreach($others as $page)
    <url>
        <loc>{{ url(($language->code != 'tr' ? $language->code . '/' : '') . $page->slug) }}</loc>
        <lastmod>{{ $page->updated_at?->toIso8601String() ?? now()->toIso8601String() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

</urlset>