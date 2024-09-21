<x-filament-breezy::grid-section md=2 title="Informações pessoais" description="Gerencie suas informações pessoais.">
    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">

            {{ $this->form }}

        </form>
    </x-filament::card>
</x-filament-breezy::grid-section>
