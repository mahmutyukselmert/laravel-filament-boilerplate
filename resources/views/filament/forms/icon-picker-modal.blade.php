{{-- icon-picker-modal.blade.php --}}
@php
    // CSS dosyasının yolunu belirle (public_path kullanarak)
    $cssPath = public_path('css/icons.css'); // Kendi dosya yolunla değiştir
    $icons = [];

    if (file_exists($cssPath)) {
        $content = file_get_contents($cssPath);
        
        // Regex ile ".icon-herhangibirismi:before" kalıplarını yakala
        // Sadece sınıf ismini (örn: icon-chauffeur) almak için parantez kullanıyoruz
        preg_match_all('/\.(icon-[\w-]+):before/', $content, $matches);
        
        if (!empty($matches[1])) {
            $icons = array_unique($matches[1]); // Tekrar edenleri temizle
        }
    }

    // Eğer dosya okunamazsa boş kalmasın diye fallback (opsiyonel)
    if (empty($icons)) {
        $icons = ['icon-chauffeur', 'icon-car-1']; 
    }
@endphp
<style>
    .icon-picker-modal {
        @apply fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center;
    }
</style>

<div class="space-y-4">
    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3 p-2">
        @foreach($icons as $icon)
            <button 
                type="button"
                {{-- DEĞİŞİKLİK BURADA: $statePath değişkenini kullanıyoruz --}}
                @click="
                    $wire.set('{{ $statePath }}', '{{ $icon }}'); 
                    close();
                "
                class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl hover:bg-primary-50 hover:border-primary-500 transition-all group"
            >
                <i class="{{ $icon }} text-3xl text-gray-700 group-hover:text-primary-600"></i>
                <span class="text-[9px] mt-2 text-gray-400 truncate w-full text-center">
                    {{ str_replace('icon-', '', $icon) }}
                </span>
            </button>
        @endforeach
    </div>
</div>