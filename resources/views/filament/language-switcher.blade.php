@php
    $locales = [
        'nl' => 'Nederlands',
        'en' => 'English',
    ];
    $current = app()->getLocale();
    $current = array_key_exists($current, $locales) ? $current : 'nl';
@endphp

<x-filament::dropdown placement="bottom-end" teleport>
    <x-slot name="trigger">
        <x-filament::button
            tag="button"
            type="button"
            color="gray"
            size="sm"
            icon="heroicon-m-language"
        >
            {{ strtoupper($current) }}
        </x-filament::button>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($locales as $code => $label)
            <x-filament::dropdown.list.item
                tag="a"
                href="{{ route('locale.switch', $code) }}"
                :icon="$code === $current ? 'heroicon-m-check' : null"
            >
                {{ $label }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
