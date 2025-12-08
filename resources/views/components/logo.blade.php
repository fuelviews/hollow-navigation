@php
    $solidDefault = Navigation::getNavSolidDefault();
    $defaultLogoShape = Navigation::getDefaultLogoShape();
    $transparencyLogoShape = Navigation::getTransparencyLogoShape();

    // Classes for default logo
    $defaultLogoClasses = 'mx-auto h-12 lg:h-14 w-auto';

    // Classes for transparency logo
    $transparencyLogoClasses = 'mx-auto h-12 lg:h-14 w-auto';

    // For transparent mode, compute initial logo state server-side (matches navigation-scroll logic)
    if (!$solidDefault && Navigation::isLogoSwapEnabled()) {
        $preScrolledRoute = Navigation::getPreScrolledRoute();
        $preScrolled = $preScrolledRoute === 'true';
        $initialShowTransparencyLogo = !$preScrolled; // Show transparency logo if not pre-scrolled
    }
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
                    @php
                        $transparencyLogo = Navigation::getTransparencyLogo();
                        $defaultLogo = Navigation::getDefaultLogo();
                    @endphp

                    @if($solidDefault)
                        {{-- Solid default mode: only show default logo (no Alpine needed) --}}
                        <img src="{{ asset($defaultLogo) }}" loading="eager" class="h-full w-auto"
                            alt="{{ $attributes->get('alt', config('app.name')) }}" />
                    @else
                        {{-- Transparent mode: both logos use x-show, initial visibility set via style --}}
                        <img x-show="!logoScrolled" src="{{ asset($transparencyLogo) }}" loading="eager" class="h-full w-auto"
                            alt="{{ $attributes->get('alt', config('app.name')) }}" style="{{ $initialShowTransparencyLogo ? '' : 'display: none;' }}" />
                        <img x-show="logoScrolled" src="{{ asset($defaultLogo) }}" loading="eager" class="h-full w-auto"
                            alt="{{ $attributes->get('alt', config('app.name')) }}" style="{{ $initialShowTransparencyLogo ? 'display: none;' : '' }}" />
                    @endif
                @else
                    @php
                        $defaultLogo = Navigation::getDefaultLogo();
                    @endphp

                    <img src="{{ asset($defaultLogo) }}" loading="eager" class="h-full w-auto"
                        alt="{{ $attributes->get('alt', config('app.name')) }}" />
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
            @php
                $wordmarkColorDefault = config('navigation.wordmark_color_default', '#1E293B');
                $wordmarkColorScrolled = config('navigation.wordmark_color_scrolled', '#FFFFFF');
            @endphp
            <div class="hidden md:block gap-{{ $gap }}">
                @if($solidDefault)
                    {{-- Solid default mode: only show scrolled wordmark --}}
                    <span class="font-bold {{ $textSizeClasses }} whitespace-pre-line leading-tight" style="color: {{ $wordmarkColorDefault }};">{{ config('navigation.wordmark') }}</span>
                @else
                    {{-- Transparent mode: both wordmarks use x-show, initial visibility set via style --}}
                    <span x-show="!logoScrolled"
                        class="font-bold {{ $textSizeClasses }} whitespace-pre-line leading-tight" style="color: {{ $wordmarkColorScrolled }}; {{ $initialShowTransparencyLogo ? '' : 'display: none;' }}">{{ config('navigation.wordmark') }}</span>
                    <span x-show="logoScrolled"
                        class="font-bold {{ $textSizeClasses }} whitespace-pre-line leading-tight" style="color: {{ $wordmarkColorDefault }}; {{ $initialShowTransparencyLogo ? 'display: none;' : '' }}">{{ config('navigation.wordmark') }}</span>
                @endif
            </div>
        @endif
    </a>
</div>
