{{--
    Komponen Modal Alpine.js yang dapat digunakan ulang.

    Props:
      - name (string)  : Nama unik modal, digunakan sebagai event trigger
      - maxWidth (string, optional): sm | md | lg | xl | 2xl (default: md)

    Cara penggunaan:
      {{-- Trigger --}}
      <button @click="$dispatch('open-modal', { name: 'nama-modal' })">Buka Modal</button>

      {{-- Modal --}}
      <x-modal name="nama-modal">
          <div class="p-6">Konten modal di sini</div>
      </x-modal>
--}}

@props(['name', 'maxWidth' => 'md'])

@php
$maxWidthClass = match($maxWidth) {
    'sm'  => 'max-w-sm',
    'md'  => 'max-w-md',
    'lg'  => 'max-w-lg',
    'xl'  => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    default => 'max-w-md',
};
@endphp

<div
    x-data="{ show: false, focusable: null }"
    x-on:open-modal.window="$event.detail.name === '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    {{-- Overlay / Backdrop --}}
    <div
        class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
        @click="show = false"
    ></div>

    {{-- Panel Modal --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full {{ $maxWidthClass }} bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden"
        @click.stop
    >
        {{ $slot }}
    </div>
</div>
