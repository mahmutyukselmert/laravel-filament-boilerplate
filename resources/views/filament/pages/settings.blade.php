<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-4 justify-start mt-3">
            <x-filament::button type="submit">
                Değişiklikleri Kaydet
            </x-filament::button>
            
            <x-filament::button color="gray" tag="a" :href="static::getNavigationUrl()">
                İptal Et
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>