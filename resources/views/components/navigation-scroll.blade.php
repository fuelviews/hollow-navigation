@php
    $frostedGlass = Navigation::getNavFrostedGlass();
    $solidDefault = Navigation::getNavSolidDefault();
    $roundedBottom = Navigation::getNavRoundedBottom();
    $sticky = Navigation::getNavSticky();
    
    // Base positioning classes - use fixed if sticky
    // For transparent nav when not sticky, use absolute to overlay content but scroll with page
    // For solid nav when not sticky, use relative to take up space in flow
    if ($sticky) {
        $basePositionClasses = 'fixed inset-x-0 top-0 z-40 drop-shadow-2xl transition-all';
    } elseif ($solidDefault) {
        // Solid nav when not sticky: relative positioning (takes up space)
        $basePositionClasses = 'relative z-40 drop-shadow-2xl transition-all';
    } else {
        // Transparent nav when not sticky: absolute positioning (overlays content, scrolls with page)
        $basePositionClasses = 'absolute inset-x-0 top-0 z-40 drop-shadow-2xl transition-all';
    }
    
    // Pre-compute classes for solid default mode (server-side rendering for performance)
    if ($solidDefault) {
        // Solid default: always has background, no transparency
        // Rounded corners are applied dynamically when scrolled
        $bgClasses = $frostedGlass ? 'backdrop-blur-xl bg-nav/70' : 'bg-nav';
        $textClasses = 'text-nav-type';
        $staticClasses = "{$basePositionClasses} {$bgClasses} {$textClasses} transition-colors duration-300 ease-in-out";
        // For solid default, always show scrolled logo (no logo swap needed since nav is always solid)
        $initialLogoScrolled = 'true';
    } else {
        // Transparent mode: base classes only, background handled by Alpine
        $preScrolledRoute = Navigation::getPreScrolledRoute();
        $preScrolled = $preScrolledRoute === 'true';
        $initialLogoScrolled = $preScrolled ? 'true' : 'false';
        // Pre-compute scrolled background classes (rounded corners applied separately via :class)
        $scrolledBgClasses = ($frostedGlass ? 'backdrop-blur-xl bg-nav/70' : 'bg-nav') . ' text-nav-type transition-colors duration-300 ease-in-out';
        
        // Set initial background - scrolled style for pre-scrolled routes, transparent for others
        if ($preScrolled) {
            $staticClasses = $basePositionClasses . ' ' . $scrolledBgClasses;
            if ($roundedBottom) {
                $staticClasses .= ' rounded-b-standard';
            }
        } else {
            // Set transparent background in static classes to prevent flash
            // Alpine will override with scrolled background when needed
            $initialBg = Navigation::isTransparentNavBackground() ? 'bg-transparent text-nav-type-trans' : 'bg-nav/70 text-nav-type';
            $staticClasses = $basePositionClasses . ' ' . $initialBg . ' transition-colors duration-300 ease-in-out';
        }
    }
@endphp

@if($solidDefault)
    <div x-data="{
            open: false,
            dropdownOpen: false,
            logoScrolled: {{ $initialLogoScrolled }},
            showEstimate: false,
            isMobile: window.innerWidth < 640,
            navHeight: 0,
            scrolled: (window.scrollY > window.innerHeight * 0.05),
            roundedBottom: {{ json_encode($roundedBottom) }}
        }" x-init="
            $watch('dropdownOpen', value => { if (value) { logoScrolled = true; } });
            showEstimate = (window.scrollY > window.innerHeight * 0.25);
            
            // Measure nav height and update spacer
            // If rounded corners are enabled, reduce height by border radius to account for rounding
            const updateNavHeight = () => {
                const nav = $refs.nav;
                if (nav) {
                    let height = nav.offsetHeight;
                    if (roundedBottom) {
                        // Get border radius from CSS variable or use default (0.8rem = ~12.8px)
                        const computedStyle = getComputedStyle(nav);
                        const borderRadiusValue = computedStyle.getPropertyValue('--border-radius-standard') || '0.8rem';
                        // Parse the value (handles both rem and px)
                        const borderRadius = parseFloat(borderRadiusValue);
                        const unit = borderRadiusValue.replace(borderRadius.toString(), '').trim();
                        // Convert to pixels (rem assumes 16px base)
                        const borderRadiusPx = unit === 'rem' ? borderRadius * 16 : borderRadius;
                        // Subtract the border radius to account for the rounded bottom corners
                        height = Math.max(0, height - borderRadiusPx);
                    }
                    navHeight = height;
                }
            };
            
            // Initial measurement after DOM is ready
            $nextTick(() => {
                updateNavHeight();
            });
            
            window.addEventListener('scroll', () => {
                showEstimate = (window.scrollY > window.innerHeight * 0.25);
                scrolled = (window.scrollY > window.innerHeight * 0.05);
            });
            
            window.addEventListener('resize', () => {
                isMobile = window.innerWidth < 640;
                if (!isMobile) showEstimate = false;
                updateNavHeight();
            });
            
            // Watch for content changes that might affect nav height
            const nav = $refs.nav;
            if (nav) {
                new MutationObserver(updateNavHeight).observe(nav, { 
                    childList: true, 
                    subtree: true, 
                    attributes: true,
                    attributeFilter: ['class', 'style']
                });
            }
        ">
        {{-- Solid default mode: simpler Alpine logic, pre-rendered classes for no layout shift --}}
        <nav x-ref="nav" 
             :class="{ 'rounded-b-standard': roundedBottom }"
             class="{{ $staticClasses }}">

            {{ $slot }}
        </nav>
        {{-- Spacer to prevent content from being hidden behind fixed solid nav - only needed when sticky --}}
        @if($sticky)
        <div :style="'height: ' + navHeight + 'px'"></div>
        @endif
    </div>
@else
    {{-- Transparent/default mode: full Alpine logic for scroll-based transparency --}}
    {{-- Note: No spacer needed here - nav is absolute when sticky is off (overlays content), fixed when sticky is on --}}
    <nav x-data="{
            open: false,
            dropdownOpen: false,
            scrolled: {{ $sticky && Navigation::getPreScrolledRoute() === 'true' ? 'true' : 'false' }},
            logoScrolled: {{ $sticky ? $initialLogoScrolled : 'false' }},
            showEstimate: false,
            isMobile: window.innerWidth < 640,
            transparentNav: {{ json_encode(Navigation::isTransparentNavBackground()) }},
            logoSwap: {{ json_encode(Navigation::isLogoSwapEnabled()) }},
            roundedBottom: {{ json_encode($roundedBottom) }},
            preScrolled: {{ $sticky ? Navigation::getPreScrolledRoute() : 'false' }},
            isSticky: {{ json_encode($sticky) }}
        }" x-init="
            // Convert scrolled to boolean if it's a string
            scrolled = scrolled === true || scrolled === 'true';
            preScrolled = preScrolled === true || preScrolled === 'true';
            
            // Ensure transparent background is applied immediately on mount
            if (isSticky && !preScrolled) {
                // Start as not scrolled (transparent)
                scrolled = false;
            }
            
            $watch('dropdownOpen', value => { 
                if (value) { 
                    logoScrolled = true; 
                    if (isSticky) scrolled = true; 
                } else { 
                    // Reset handled by scroll listener
                } 
            });
            
            showEstimate = (window.scrollY > window.innerHeight * 0.25);
            
            // Update scroll state on scroll
            const updateScrollState = () => {
                if (isSticky && !preScrolled) {
                    const isScrolled = window.scrollY > window.innerHeight * 0.05;
                    if (transparentNav) {
                        scrolled = isScrolled;
                    }
                    if (logoSwap) {
                        logoScrolled = isScrolled;
                    }
                }
                showEstimate = (window.scrollY > window.innerHeight * 0.25);
            };
            
            // Initial check
            updateScrollState();
            
            // Listen for scroll events
            window.addEventListener('scroll', updateScrollState, { passive: true });
            
            window.addEventListener('resize', () => {
                isMobile = window.innerWidth < 640;
                if (!isMobile) showEstimate = false;
            });
        " :class="{
            '{{ $scrolledBgClasses }}': isSticky && scrolled && !preScrolled,
            'bg-transparent text-nav-type-trans': isSticky && !scrolled && !preScrolled && transparentNav,
            'bg-nav/70 text-nav-type': isSticky && !scrolled && !preScrolled && !transparentNav,
            'bg-nav': dropdownOpen,
            'rounded-b-standard': isSticky && roundedBottom && (scrolled || preScrolled)
        }" class="{{ $staticClasses }}" x-transition>

        {{ $slot }}
    </nav>
@endif
