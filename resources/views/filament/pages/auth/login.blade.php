<div class="flex h-screen w-full overflow-hidden bg-[#181818]">

    {{-- LADO ESQUERDO / BRAND (Agora com 75%) --}}
    <div class="hidden lg:flex lg:w-[75%] h-full items-center justify-center bg-[#181818] pr-8 xl:pr-16">
        <img
            src="{{ asset('images/castellotraining_logo2.png') }}"
            alt="Logo Castello Training"
            class="block w-full max-w-[700px] xl:max-w-[850px] object-contain drop-shadow-[0_30px_80px_rgba(0,0,0,0.75)] transition-transform duration-500 hover:scale-[1.02]"
        >
    </div>

    {{-- LADO DIREITO / FORM (Reduzido para 25%) --}}
    <div class="flex w-full lg:w-[25%] h-full items-center justify-center px-6 sm:px-10 lg:px-12 bg-[#181818] shadow-2xl z-10">
        <div class="w-full max-w-sm">

            {{-- LOGO MOBILE --}}
            <div class="mb-8 flex justify-center lg:hidden">
                <img
                    src="{{ asset('images/castellotraining_logo2.png') }}"
                    alt="Logo Castello Training"
                    class="w-40 max-w-[220px] object-contain drop-shadow-xl"
                >
            </div>

            <h2 class="mb-8 text-center text-3xl font-bold text-white lg:text-left xl:text-4xl">
                Acesse sua conta
            </h2>

            <x-filament-panels::form wire:submit="authenticate">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </div>
    </div>

</div>