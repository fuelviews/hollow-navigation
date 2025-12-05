<?php

namespace Fuelviews\Navigation;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Navigation
{
    public function __construct(protected array $config)
    {
    }

    public function getNavigationItems(): Collection
    {
        $navigation = $this->config['navigation'] ?? [];

        // If navigation is a closure, evaluate it
        if (is_callable($navigation)) {
            $navigation = $navigation();
        }

        return collect($navigation)->sortBy('position')->values();
    }

    public function isDropdownRouteActive(array $links): bool
    {
        return collect($links)->contains(fn(array $link) => request()->routeIs($link['route']));
    }

    public function getDefaultLogo(): string
    {
        return $this->config['default_logo'] ?? '';
    }

    public function getDefaultLogoShape(): string
    {
        return $this->config['default_logo_shape'] ?? 'square';
    }

    public function getTransparencyLogoShape(): string
    {
        return $this->config['transparency_logo_shape'] ?? 'horizontal';
    }

    public function getTransparencyLogo(): string
    {
        return $this->config['transparency_logo'] ?? '';
    }

    public function getPhone(): string
    {
        return $this->config['phone'] ?? '';
    }

    public function isTopNavEnabled(): bool
    {
        return $this->config['top_nav_enabled'] ?? false;
    }

    public function isLogoSwapEnabled(): bool
    {
        return $this->config['logo_swap_enabled'] ?? true;
    }

    public function isTransparentNavBackground(): bool
    {
        return $this->config['transparent_nav_background'] ?? true;
    }

    public function isPreScrolledRoute(): bool
    {
        $currentRoute = Route::currentRouteName();
        $preScrolledRoutes = $this->config['pre_scrolled_routes'] ?? [];

        foreach ($preScrolledRoutes as $route) {
            // Check for exact match first
            if ($currentRoute === $route) {
                return true;
            }

            // Check for wildcard match
            if (str_contains($route, '*')) {
                $pattern = preg_quote($route, '/');
                $pattern = str_replace('\\*', '.*', $pattern);
                if (preg_match('/^' . $pattern . '$/', $currentRoute)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getPreScrolledRoute(): string
    {
        return $this->isPreScrolledRoute() ? 'true' : 'false';
    }

    public function isRoundedBottomScrolled(): bool
    {
        return $this->getNavRoundedBottom();
    }

    /**
     * Get theme setting with fallback to config
     */
    protected function getThemeSetting(string $key, $default = null)
    {
        if (class_exists(\App\Models\Theme::class)) {
            $theme = \App\Models\Theme::current();
            if ($theme && isset($theme->$key)) {
                return $theme->$key;
            }
        }

        return $this->config[$key] ?? $default;
    }

    public function getNavFrostedGlass(): bool
    {
        return (bool) $this->getThemeSetting('nav_frosted_glass', false);
    }

    public function getNavSolidDefault(): bool
    {
        return (bool) $this->getThemeSetting('nav_solid_default', false);
    }

    public function getNavRoundedBottom(): bool
    {
        return (bool) $this->getThemeSetting('nav_rounded_bottom', false);
    }

    public function getNavSticky(): bool
    {
        return (bool) $this->getThemeSetting('nav_sticky', true);
    }

    /**
     * Get nav color with opacity for frosted glass effect
     */
    public function getNavColorWithOpacity(float $opacity = 0.7): string
    {
        $navColor = $this->getThemeSetting('nav', '#FFFFFF');

        // Convert hex to rgba
        $hex = ltrim($navColor, '#');

        // Handle 3-digit hex
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r}, {$g}, {$b}, {$opacity})";
    }
}
