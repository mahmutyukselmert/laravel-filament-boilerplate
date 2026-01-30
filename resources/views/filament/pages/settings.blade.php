<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-3" style="margin-top: 2rem; margin-bottom: 1rem; margin-right: 1rem;">
             <x-filament::button type="submit">
                Değişiklikleri Kaydet
            </x-filament::button>
            
            <x-filament::button color="gray" tag="a" :href="static::getNavigationUrl()" style="margin-left: 1rem;">
                İptal Et
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>