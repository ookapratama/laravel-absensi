@php
use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)

    {{-- active menu method --}}
    @php
      $activeClass = null;
      $active = ($configData["layout"] ?? 'vertical') === 'vertical' ? 'active open' : 'active';
      $currentRouteName = Route::currentRouteName();
      $currentUrl = request()->path();

      $hasSubChildren = (isset($submenu->submenu) && count($submenu->submenu) > 0) || (isset($submenu->children) && count($submenu->children) > 0);

      if ($currentRouteName === $submenu->slug || (isset($submenu->path) && request()->is(ltrim($submenu->path, '/') . '*')) || (isset($submenu->url) && request()->is(ltrim($submenu->url, '/') . '*'))) {
          $activeClass = 'active';
      }
      
      if ($hasSubChildren) {
        $subChildren = $submenu->submenu ?? $submenu->children;
        foreach($subChildren as $subChild) {
          if ($currentRouteName === $subChild->slug || (isset($subChild->path) && request()->is(ltrim($subChild->path, '/') . '*')) || (isset($subChild->url) && request()->is(ltrim($subChild->url, '/') . '*'))) {
            $activeClass = $active;
            break;
          }
        }
      }
    @endphp

      <li class="menu-item {{$activeClass}}">
        <a href="{{ isset($submenu->path) ? url($submenu->path) : (isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)') }}" class="{{ $hasSubChildren ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
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
          @include('layouts.sections.menu.submenu',['menu' => $nextSubmenu, 'configData' => $configData])
        @endif
      </li>
    @endforeach
  @endif
</ul>
