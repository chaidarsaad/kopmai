<x-filament-widgets::widget>
    <x-filament::section>
        <form wire:submit.prevent="create">
            {{ $this->form }}
            {{-- Menampilkan pesan error jika ada --}}
            @if ($errors->any())
                <div class="mt-3 text-red-600">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <x-filament::button type="submit" form="create" class="mt-3" wire:loading.attr="disabled">
                {{ __('filament-panels::resources/pages/create-record.form.actions.create.label') }}
            </x-filament::button>
        </form>

    </x-filament::section>
</x-filament-widgets::widget>
