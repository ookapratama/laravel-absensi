@php
   $width = $width ?? '40';
   $color = $color ?? '#000000';
@endphp
<span class="app-brand-logo demo">
   <svg width="{{ $width }}" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Outer Chevron -->
      <path d="M70 10L30 50L70 90" stroke="{{ $color }}" stroke-width="12" stroke-linecap="butt"
         stroke-linejoin="miter" />
      <!-- Middle Chevron -->
      <path d="M75 25L45 55L75 85" stroke="{{ $color }}" stroke-width="10" stroke-linecap="butt"
         stroke-linejoin="miter" transform="translate(5, 0)" />
      <!-- Inner Chevron -->
      <path d="M80 40L60 60L80 80" stroke="{{ $color }}" stroke-width="8" stroke-linecap="butt"
         stroke-linejoin="miter" transform="translate(10, 0)" />
      <!-- Diamond -->
      <rect x="85" y="55" width="8" height="8" fill="{{ $color }}" transform="rotate(45, 89, 59)" />
   </svg>
</span>
