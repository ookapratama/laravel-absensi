@php
   use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
   @if (isset($menu))
      @foreach ($menu as $submenu)
         {{-- active menu method --}}
         @php
            $activeClass = null;
            $active = ($configData['layout'] ?? 'vertical') === 'vertical' ? 'active open' : 'active';
            $currentRouteName = Route::currentRouteName();

            $hasSubChildren =
                (isset($submenu->submenu) && count($submenu->submenu) > 0) ||
                (isset($submenu->children) && count($submenu->children) > 0);

            $isActive = false;

            // First priority: exact route name match
            if (isset($submenu->slug) && $currentRouteName === $submenu->slug) {
                $isActive = true;
            }
            // Second priority: route prefix match for .index routes
            elseif (isset($submenu->slug) && str_ends_with($submenu->slug, '.index')) {
                $prefix = substr($submenu->slug, 0, -6);
                if (str_starts_with($currentRouteName, $prefix . '.')) {
                    $isActive = true;
                }
            }
            // Third priority: path matching
            elseif (isset($submenu->path) && $submenu->path !== '/') {
                $path = ltrim($submenu->path, '/');
                $currentPath = request()->path();

                if ($currentPath === $path || str_starts_with($currentPath, $path . '/')) {
                    $isActive = true;
                }
            }

            if ($isActive) {
                $activeClass = 'active';
            }

            // Check if any sub-child is active
            if ($hasSubChildren && !$isActive) {
                $subChildren = $submenu->submenu ?? $submenu->children;
                foreach ($subChildren as $subChild) {
                    $subChildActive = false;

                    if (isset($subChild->slug) && $currentRouteName === $subChild->slug) {
                        $subChildActive = true;
                    } elseif (isset($subChild->slug) && str_ends_with($subChild->slug, '.index')) {
                        $prefix = substr($subChild->slug, 0, -6);
                        if (str_starts_with($currentRouteName, $prefix . '.')) {
                            $subChildActive = true;
                        }
                    } elseif (isset($subChild->path) && $subChild->path !== '/') {
                        $path = ltrim($subChild->path, '/');
                        $currentPath = request()->path();

                        if ($currentPath === $path || str_starts_with($currentPath, $path . '/')) {
                            $subChildActive = true;
                        }
                    }

                    if ($subChildActive) {
                        $activeClass = $active;
                        break;
                    }
                }
            }
         @endphp

         <li class="menu-item {{ $activeClass }}">
            <a href="{{ isset($submenu->path) ? url($submenu->path) : (isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)') }}"
               class="{{ $hasSubChildren ? 'menu-link menu-toggle' : 'menu-link' }}"
               @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
               @if (isset($submenu->icon))
                  <i class="menu-icon tf-icons {{ $submenu->icon }} me-3"></i>
               @endif
               <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
               @isset($submenu->badge)
                  <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}</div>
               @endisset
            </a>

            {{-- submenu --}}
            @if ($hasSubChildren)
               @php
                  $nextSubmenu = $submenu->submenu ?? $submenu->children;
               @endphp
               @include('layouts.sections.menu.submenu', [
                   'menu' => $nextSubmenu,
                   'configData' => $configData,
               ])
            @endif
         </li>
      @endforeach
   @endif
</ul>
