<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($pages as $translation)
    @php
        $slug = is_object($translation) && isset($translation->slug)
            ? $translation->slug
            : ($translation['slug'] ?? null);

        if (!$slug || !$translation->page) continue;

        $loc = url($slug); // kesin string
    @endphp

    <url>
        <loc>{{ $loc }}</loc>
        <lastmod>{{ $translation->updated_at instanceof \DateTime ? $translation->updated_at->format('c') : now()->format('c') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>{{ $translation->page->template === 'home' ? '1.0' : '0.8' }}</priority>
    </url>
@endforeach
</urlset>
