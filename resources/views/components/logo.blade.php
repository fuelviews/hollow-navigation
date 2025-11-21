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
    <a href="{{ config('app.url') }}" class="h-full flex items-center">
        <span class="sr-only">
            {{ config('app.name') }}
        </span>
        @if(Navigation::getDefaultLogo())
            @if(Navigation::isLogoSwapEnabled())
                <img x-show="!logoScrolled" {{ glide()->src(Navigation::getTransparencyLogo(), 1000, lazy: false) }}
                    loading="eager" class="h-full w-auto" alt="{{ $attributes->get('alt', config('app.name')) }}" />

                <img x-show="logoScrolled" {{ glide()->src(Navigation::getDefaultLogo(), 1000, lazy: false) }} loading="eager"
                    class="h-full w-auto" alt="{{ $attributes->get('alt', config('app.name')) }}" />
            @else
                <img {{ glide()->src(Navigation::getDefaultLogo(), 1000, lazy: false) }} loading="eager" class="h-full w-auto"
                    alt="{{ $attributes->get('alt', config('app.name')) }}" />
            @endif
        @else
            <div class="h-full w-auto">
                <x-navigation::social.rocketman />
            </div>
        @endif
    </a>
</div>