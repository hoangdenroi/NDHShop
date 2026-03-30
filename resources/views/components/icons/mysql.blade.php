{{-- Logo SVG MySQL từ assets --}}
@props(['class' => 'w-7 h-7'])

<img src="{{ asset('assets/svg/mysql-logo-svgrepo-com.svg') }}" alt="MySQL" {{ $attributes->merge(['class' => $class]) }} />