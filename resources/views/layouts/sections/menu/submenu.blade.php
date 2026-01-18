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
            if ($currentRouteName === $submenu->slug) {
                $isActive = true;
            } elseif (
                isset($submenu->path) &&
                ($submenu->path !== '/' && request()->is(ltrim($submenu->path, '/') . '*'))
            ) {
                $isActive = true;
            }

            // Special case: if slug is 'something.index', also match 'something.*'
            if (!$isActive && isset($submenu->slug) && str_ends_with($submenu->slug, '.index')) {
                $prefix = substr($submenu->slug, 0, -6);
                if (request()->routeIs($prefix . '.*')) {
                    $isActive = true;
                }
            }

            if ($isActive) {
                $activeClass = 'active';
            }

            if ($hasSubChildren) {
                $subChildren = $submenu->submenu ?? $submenu->children;
                foreach ($subChildren as $subChild) {
                    $subChildActive = false;
                    if ($currentRouteName === $subChild->slug) {
                        $subChildActive = true;
                    } elseif (
                        isset($subChild->path) &&
                        ($subChild->path !== '/' && request()->is(ltrim($subChild->path, '/') . '*'))
                    ) {
                        $subChildActive = true;
                    }

                    if (!$subChildActive && isset($subChild->slug) && str_ends_with($subChild->slug, '.index')) {
                        $prefix = substr($subChild->slug, 0, -6);
                        if (request()->routeIs($prefix . '.*')) {
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
