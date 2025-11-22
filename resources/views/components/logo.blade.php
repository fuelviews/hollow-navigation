@php
    $defaultLogoShape = Navigation::getDefaultLogoShape();
    $transparencyLogoShape = Navigation::getTransparencyLogoShape();

    // Classes for default logo
    $defaultLogoClasses = 'mx-auto h-12 lg:h-14 w-auto';

    // Classes for transparency logo
    $transparencyLogoClasses = 'mx-auto h-12 lg:h-14 w-auto';
@endphp

<div {{ $attributes->only(['class']) }}
    class="h-16 lg:h-20 transition-all duration-500 ease-in-out flex items-center justify-center">
    @php
        $gap = config('navigation.wordmark_gap', '3');
    @endphp
    <a href="{{ config('app.url') }}" class="h-full flex items-center gap-{{ $gap }}">
        <span class="sr-only">
            {{ config('app.name') }}
        </span>
        @if(Navigation::getDefaultLogo())
            <div class="h-full flex items-center">
                @if(Navigation::isLogoSwapEnabled())
                    <img x-show="!logoScrolled" {{ glide()->src(Navigation::getTransparencyLogo(), 1000, lazy: false) }}
                        loading="eager" class="h-full w-auto" alt="{{ $attributes->get('alt', config('app.name')) }}" />

                    <img x-show="logoScrolled" {{ glide()->src(Navigation::getDefaultLogo(), 1000, lazy: false) }}
                        loading="eager" class="h-full w-auto" alt="{{ $attributes->get('alt', config('app.name')) }}" />
                @else
                    <img {{ glide()->src(Navigation::getDefaultLogo(), 1000, lazy: false) }} loading="eager"
                        class="h-full w-auto" alt="{{ $attributes->get('alt', config('app.name')) }}" />
                @endif
            </div>
        @else
            <div class="h-full w-auto">
                <x-navigation::social.rocketman />
            </div>
        @endif

        @if(config('navigation.wordmark'))
            @php
                $textSize = config('navigation.wordmark_text_size', 'lg');
                $gap = config('navigation.wordmark_gap', '3');
                $textSizeClasses = match ($textSize) {
                    'sm' => 'text-sm lg:text-base',
                    'base' => 'text-base lg:text-lg',
                    'lg' => 'text-lg lg:text-xl',
                    'xl' => 'text-xl lg:text-2xl',
                    '2xl' => 'text-2xl lg:text-3xl',
                    default => 'text-lg lg:text-xl',
                };
            @endphp
            <div class="hidden md:block gap-{{ $gap }}">
                <span x-show="!logoScrolled"
                    class="text-white font-bold {{ $textSizeClasses }} whitespace-pre-line leading-tight">{{ config('navigation.wordmark') }}</span>
                <span x-show="logoScrolled"
                    class="text-gray-700 font-bold {{ $textSizeClasses }} whitespace-pre-line leading-tight">{{ config('navigation.wordmark') }}</span>
            </div>
        @endif
    </a>
</div>