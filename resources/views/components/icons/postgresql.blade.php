{{-- Logo SVG PostgreSQL từ assets --}}
@props(['class' => 'w-7 h-7'])

<img src="{{ asset('assets/svg/postgresql-logo-svgrepo-com.svg') }}" alt="PostgreSQL" {{ $attributes->merge(['class' => $class]) }} />