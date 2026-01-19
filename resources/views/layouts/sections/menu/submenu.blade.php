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
            // Second priority: exact path match
            elseif (isset($submenu->path) && $submenu->path !== null && $submenu->path !== '/') {
                $path = ltrim($submenu->path, '/');
                $currentPath = request()->path();

                // Only exact path match for leaf nodes (no children)
                if (!$hasSubChildren && $currentPath === $path) {
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
                    } elseif (isset($subChild->path) && $subChild->path !== null && $subChild->path !== '/') {
                        $path = ltrim($subChild->path, '/');
                        $currentPath = request()->path();

                        if ($currentPath === $path) {
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
