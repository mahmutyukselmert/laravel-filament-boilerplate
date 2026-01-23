<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">

    <title>{{ $translation->meta_title ?? $translation->title }}</title>

    <meta name="description" content="{{ $translation->meta_description }}">
    <meta name="keywords" content="{{ $translation->meta_keywords }}">
</head>
<body>

    <main>
        <h1>{{ $translation->title }}</h1>

        @if($translation->subtitle)
            <h2>{{ $translation->subtitle }}</h2>
        @endif

        @if($translation->short_description)
            <p>{{ $translation->short_description }}</p>
        @endif

        {!! $translation->content !!}
    </main>

</body>
</html>