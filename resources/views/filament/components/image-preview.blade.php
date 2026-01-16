
@if($getState())
    <img src="{{ asset('storage/' . $getState()) }}" alt="Kapak Görseli" style="max-width: 200px; border-radius: 8px;">
@else
    <span>Görsel yok</span>
@endif
