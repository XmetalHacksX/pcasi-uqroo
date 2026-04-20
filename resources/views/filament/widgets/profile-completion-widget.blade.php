<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-y-6 sm:flex-row sm:items-center sm:justify-between sm:gap-x-4">
            {{-- Lado Izquierdo: Avatar de iniciales y Texto --}}
            <div class="flex items-center gap-x-4">
                {{-- Avatar Negro Circular --}}
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-black">
                    <span class="text-xl font-bold uppercase text-white">
                        {{ collect(explode(' ', auth()->user()->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('') }}
                    </span>
                </div>

                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                        Bienvenido, {{ auth()->user()->name }}
                    </h2>
                    <p class="text-base text-gray-500 dark:text-gray-400">
                        {{ auth()->user()->email }} 
                        @if(auth()->user()->user_type) · {{ auth()->user()->user_type }} @endif
                    </p>
                </div>
            </div>

            {{-- Lado Derecho: Acciones (Alineación Estética) --}}
            <div class="flex flex-col gap-y-3 sm:items-end">
                @if($profileComplete)
                    <div class="w-full sm:w-auto">
                        {{ $this->editProfile }}
                    </div>
                @endif

                {{-- Botón Sign Out oficial con ancho completo --}}
                <form action="{{ filament()->getLogoutUrl() }}" method="post" class="w-full sm:w-auto">
                    @csrf
                    <x-filament::button 
                        type="submit" 
                        color="gray" 
                        icon="heroicon-m-arrow-left-on-rectangle"
                        size="sm"
                        outlined
                        class="w-full justify-center"
                    >
                        Sign out
                    </x-filament::button>
                </form>
            </div>
        </div>

        {{-- Formulario para completar (solo si falta info) --}}
        @if(!$profileComplete)
            <div class="mt-8 border-t border-gray-100 pt-6 dark:border-gray-800">
                <div class="mb-4 flex items-center gap-2 text-amber-600">
                    <x-heroicon-m-exclamation-triangle class="h-5 w-5" />
                    <span class="text-sm font-medium">Información institucional incompleta</span>
                </div>
                
                <form wire:submit="save">
                    {{ $this->profileForm }}
                    
                    <div class="mt-6">
                        <x-filament::button type="submit">
                            Guardar y continuar
                        </x-filament::button>
                    </div>
                </form>
            </div>
        @endif
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>