@php
    /* social media accounts */
    if (config('business-info.social_media') !== null) {
        $socialMedia = config('business-info.social_media');
    }

    // Footer always uses transparency logo shape
    $logoShape = Navigation::getTransparencyLogoShape();
    $logoClasses = '';
    if ($logoShape === 'horizontal') {
        $logoClasses = 'mx-auto w-64 lg:w-72';
    } elseif ($logoShape === 'vertical') {
        $logoClasses = 'mx-auto w-32 lg:w-48';
    } elseif ($logoShape === 'square') {
        $logoClasses = 'mx-auto w-48 lg:w-64';
    }
@endphp

<div>
    <footer class="bg-footer-back">
        <div class="mx-auto max-w-standard px-6 py-16 lg:py-20">

            {{-- Top Section: Logo, Pitch, CTAs --}}
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-16 mb-16">
                {{-- Left Column: Logo and Social --}}
                <div class="flex flex-col items-center lg:items-start space-y-8">
                    <div class="flex justify-center lg:justify-start text-footer-type">
                        @if (Navigation::getDefaultLogo())
                            @if (Navigation::isLogoSwapEnabled())
                                <img {{ glide()->src(Navigation::getTransparencyLogo()) }} class="{{ $logoClasses }}"
                                    alt="{{ config('app.name') }}" />
                            @else
                                <img {{ glide()->src(Navigation::getDefaultLogo()) }} class="{{ $logoClasses }}"
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
                        <div class="flex flex-wrap justify-center lg:justify-start gap-4">
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
                <div class="flex flex-col items-center lg:items-start space-y-6">
                    @if (config('business-info.elevator-pitch') !== null)
                        <p class="text-center lg:text-left text-lg leading-relaxed text-footer-type max-w-xl">
                            {{ config('business-info.elevator-pitch') }}
                        </p>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                        <x-navigation::free-estimate-button />
                        <x-navigation::phone-button />
                    </div>
                </div>
            </div>

            {{-- Navigation Columns Section --}}
            <div class="border-t border-gray-400/20 pt-12 mb-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 text-footer-type">

                    {{-- Column 1: Menu (Non-Dropdown Links) --}}
                    @php
                        $nonDropdownLinks = collect(Navigation::getNavigationItems())->where('type', 'link');
                    @endphp

                    @if($nonDropdownLinks->isNotEmpty())
                        <div class="space-y-4">
                            <h3 class="font-bold text-xl pb-3 border-b-2 border-prime/50">
                                {{ __('Menu') }}
                            </h3>
                            <ul class="space-y-3">
                                @foreach ($nonDropdownLinks as $item)
                                    <li>
                                        <x-navigation::footer.footer-navigation-link :href="route($item['route'])"
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
                            <div class="space-y-4">
                                <h3 class="font-bold text-xl pb-3 border-b-2 border-prime/50">
                                    {{ __($item['name']) }}
                                </h3>
                                <ul class="space-y-3">
                                    @foreach ($item['links'] as $link)
                                        <li>
                                            <x-navigation::footer.footer-navigation-link :href="route($link['route'])"
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
            <div class="border-t border-gray-400/20 pt-8">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 text-sm text-gray-400">
                    {{-- Copyright --}}
                    <p class="text-center md:text-left">
                        &copy; {{ date('Y') }}
                        @if (config('business-info.legal-name') !== null)
                            {{ config('business-info.legal-name') }}
                        @endif
                    </p>

                    {{-- Legal Links --}}
                    <div class="flex flex-wrap justify-center md:justify-end gap-x-4 gap-y-2">
                        <span class="text-gray-400/75">All rights reserved</span>

                        @if(Route::has('terms-and-conditions'))
                            <span class="text-gray-400/50">&middot;</span>
                            <a class="text-legal-link hover:text-legal-link/75 transition-colors underline"
                                href="{{ route('terms-and-conditions') }}" title="Terms & Conditions">
                                Terms & Conditions
                            </a>
                        @endif

                        @if(Route::has('privacy-policy'))
                            <span class="text-gray-400/50">&middot;</span>
                            <a class="text-legal-link hover:text-legal-link/75 transition-colors underline"
                                href="{{ route('privacy-policy') }}" title="Privacy Policy">
                                Privacy Policy
                            </a>
                        @endif

                        @if(Route::has('sitemap'))
                            <span class="text-gray-400/50">&middot;</span>
                            <a class="text-legal-link hover:text-legal-link/75 transition-colors underline"
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