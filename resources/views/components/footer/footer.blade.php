@php
    /* social media accounts */
    if (config('business-info.social_media') !== null) {
        $socialMedia = config('business-info.social_media');
    }

    // Footer always uses transparency logo shape
    $logoShape = Navigation::getTransparencyLogoShape();
    $logoClasses = '';
    if ($logoShape === 'horizontal') {
        $logoClasses = 'w-72 lg:w-80';
    } elseif ($logoShape === 'vertical') {
        $logoClasses = 'w-40 lg:w-56';
    } elseif ($logoShape === 'square') {
        $logoClasses = 'w-56 lg:w-72';
    }
@endphp

<div>
    <footer class="bg-footer-back">
        <div class="mx-auto max-w-standard px-6 py-20 lg:py-24">

            {{-- Top Section: Logo, Pitch, CTAs --}}
            <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:gap-20 pb-16 border-b border-gray-400/10">
                {{-- Left Column: Logo and Social --}}
                <div class="flex flex-col items-center lg:items-start space-y-10">
                    <div class="flex justify-center lg:justify-start text-footer-type">
                        @if (Navigation::getDefaultLogo())
                            @php
                                $transparencyLogo = Navigation::getTransparencyLogo();
                                $defaultLogo = Navigation::getDefaultLogo();
                            @endphp
                            @if (Navigation::isLogoSwapEnabled())
                                <img src="{{ asset($transparencyLogo) }}" class="{{ $logoClasses }}"
                                    alt="{{ config('app.name') }}" />
                            @else
                                <img src="{{ asset($defaultLogo) }}" class="{{ $logoClasses }}"
                                    alt="{{ config('app.name') }}" />
                            @endif
                        @else
                            <div class="{{ $logoClasses }}">
                                <x-navigation::social.rocketman />
                            </div>
                        @endif
                    </div>

                    {{-- Social Media Icons --}}
                    @if(isset($socialMedia) && !empty(array_filter($socialMedia)))
                        <div class="flex flex-wrap justify-center lg:justify-start gap-5">
                            @isset($socialMedia['youtube'])
                                <x-navigation::social.youtube :socialMedia="$socialMedia['youtube']" />
                            @endisset
                            @isset($socialMedia['facebook'])
                                <x-navigation::social.facebook :socialMedia="$socialMedia['facebook']" />
                            @endisset
                            @isset($socialMedia['instagram'])
                                <x-navigation::social.instagram :socialMedia="$socialMedia['instagram']" />
                            @endisset
                            @isset($socialMedia['xitter'])
                                <x-navigation::social.xitter :socialMedia="$socialMedia['xitter']" />
                            @endisset
                            @isset($socialMedia['linkedin'])
                                <x-navigation::social.linkedin :socialMedia="$socialMedia['linkedin']" />
                            @endisset
                            @isset($socialMedia['tiktok'])
                                <x-navigation::social.tiktok :socialMedia="$socialMedia['tiktok']" />
                            @endisset
                        </div>
                    @endif
                </div>

                {{-- Right Column: Elevator Pitch and CTAs --}}
                <div class="flex flex-col items-start justify-center space-y-8">
                    @if (config('business-info.elevator-pitch') !== null)
                        <p class="text-left text-xl leading-relaxed text-footer-type font-light max-w-xl">
                            {{ config('business-info.elevator-pitch') }}
                        </p>
                    @endif


                    <div class="flex flex-col md:flex-row gap-2 md:gap-1 w-full md:w-auto mt-6 mb-8">
                        <x-navigation::free-estimate-button class="w-full md:w-auto" />
                        <x-navigation::phone-button class="w-full md:w-auto" />
                    </div>
                </div>
            </div>

            {{-- Navigation Columns Section --}}
            <div class="py-16">
                <div
                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-x-8 gap-y-12 text-footer-type">

                    {{-- Column 1: Menu (Non-Dropdown Links) --}}
                    @php
                        $nonDropdownLinks = collect(Navigation::getNavigationItems())->where('type', 'link');
                    @endphp

                    @if($nonDropdownLinks->isNotEmpty())
                        <div class="space-y-5">
                            <h3
                                class="font-bold text-lg uppercase tracking-wide pb-4 border-b-2 border-prime text-footer-type/90">
                                {{ __('Menu') }}
                            </h3>
                            <ul class="space-y-3">
                                @foreach ($nonDropdownLinks as $item)
                                    <li>
                                        <x-navigation::footer.footer-navigation-link :href="$item['url'] ?? '#'"
                                            :active="request()->routeIs($item['route'])">
                                            {{ __($item['name']) }}
                                        </x-navigation::footer.footer-navigation-link>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Dropdown Columns --}}
                    @foreach (Navigation::getNavigationItems() as $item)
                        @if ($item['type'] === 'dropdown')
                            <div class="space-y-5">
                                <h3
                                    class="font-bold text-lg uppercase tracking-wide pb-4 border-b-2 border-prime text-footer-type/90">
                                    {{ __($item['name']) }}
                                </h3>
                                <ul class="space-y-3">
                                    @foreach ($item['links'] as $link)
                                        <li>
                                            <x-navigation::footer.footer-navigation-link :href="$link['url'] ?? '#'"
                                                :active="request()->routeIs($link['route'])">
                                                {{ __($link['name']) }}
                                            </x-navigation::footer.footer-navigation-link>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Legal Footer --}}
            <div class="border-t border-gray-400/10 pt-10">
                <div
                    class="flex flex-col md:flex-row md:justify-between md:items-center gap-6 text-sm text-gray-400/80">
                    {{-- Copyright --}}
                    <p class="text-center md:text-left font-light">
                        &copy; {{ date('Y') }}
                        @if (config('business-info.legal-name') !== null)
                            {{ config('business-info.legal-name') }}
                        @endif
                    </p>

                    {{-- Legal Links --}}
                    <div class="flex flex-wrap justify-center md:justify-end items-center gap-x-6 gap-y-2">
                        <span class="text-gray-400/60 font-light">All rights reserved</span>

                        @if(Route::has('terms-and-conditions'))
                            <a class="text-legal-link hover:text-prime transition-colors duration-200 font-light"
                                href="{{ route('terms-and-conditions') }}" title="Terms & Conditions">
                                Terms & Conditions
                            </a>
                        @endif

                        @if(Route::has('privacy-policy'))
                            <a class="text-legal-link hover:text-prime transition-colors duration-200 font-light"
                                href="{{ route('privacy-policy') }}" title="Privacy Policy">
                                Privacy Policy
                            </a>
                        @endif

                        @if(Route::has('sitemap'))
                            <a class="text-legal-link hover:text-prime transition-colors duration-200 font-light"
                                href="{{ route('sitemap') }}" title="Sitemap">
                                Sitemap
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
