@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'h-20 px-5 border-primary-700 border-1 focus:border-indigo-500 focus:ring-indigo-500 rounded-md custom-box-shadow']) !!}>
