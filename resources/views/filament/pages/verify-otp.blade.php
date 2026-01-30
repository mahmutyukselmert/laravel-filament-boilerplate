<x-filament-panels::page>
    <div class="max-w-md mx-auto">
        <form wire:submit="verify">
            {{ $this->form }}
            
            <div class="mt-4">
                {{ $this->verifyAction }}
            </div>
        </form>
    </div>
</x-filament-panels::page>