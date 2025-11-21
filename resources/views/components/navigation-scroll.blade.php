<nav x-data="{
        open: false,
        dropdownOpen: false,
        scrolled: {{ Navigation::getPreScrolledRoute() }},
        logoScrolled: {{ Navigation::getPreScrolledRoute() }},
        showEstimate: false,
        isMobile: window.innerWidth < 640,
        transparentNav: {{  Navigation::isTransparentNavBackground() ? 'true' : 'false' }},
        logoSwap: {{  Navigation::isLogoSwapEnabled() ? 'true' : 'false' }}
    }" x-init="
        if (transparentNav) {
            scrolled = (window.scrollY > window.innerHeight * 0.05) || {{ Navigation::getPreScrolledRoute() }};
        }
        if (logoSwap) {
            logoScrolled = (window.scrollY > window.innerHeight * 0.05) || {{ Navigation::getPreScrolledRoute() }};
        }
        // Watch dropdownOpen to toggle logoScrolled for scrolled logo when mobile menu opens
            $watch('dropdownOpen', value => { if (value) { logoScrolled = true; scrolled = true; } else { /* reset handled by scroll listener */ } });
        showEstimate = (window.scrollY > window.innerHeight * 0.25);
        
        window.addEventListener('scroll', () => {
            if (!dropdownOpen && transparentNav && !{{ Navigation::getPreScrolledRoute() }}) {
                scrolled = (window.scrollY > window.innerHeight * 0.05);
            }
            // Only update logoScrolled on scroll when the mobile dropdown is closed
            if (!dropdownOpen && logoSwap && !{{ Navigation::getPreScrolledRoute() }}) {
                logoScrolled = (window.scrollY > window.innerHeight * 0.05);
            }
            showEstimate = (window.scrollY > window.innerHeight * 0.25);
        });
        
        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 640;
            if (!isMobile) showEstimate = false;
        });
     " :class="{
          // Base background for scrolled or pre-scrolled state
          'backdrop-blur-xl bg-white/70 text-nav-type {{ Navigation::isRoundedBottomScrolled() ? 'rounded-b-standard' : '' }} transition-colors duration-300 ease-in-out': scrolled || {{ Navigation::getPreScrolledRoute() }},
          // Transparent background when not scrolled
          '{{ Navigation::isTransparentNavBackground() ? 'bg-transparent text-nav-type-trans' : 'backdrop-blur-xl bg-white/70 text-nav-type' }} transition-colors duration-300 ease-in-out': !scrolled && !{{ Navigation::getPreScrolledRoute() }},
          // When the mobile dropdown is open, use the same solid background as the dropdown menu
          'bg-white': dropdownOpen
      }" class="duration-600 fixed inset-x-0 top-0 z-40 drop-shadow-2xl transition-all" x-cloak x-transition>

    {{ $slot }}
</nav>