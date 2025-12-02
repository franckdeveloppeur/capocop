@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-coolGray-700 text-sm font-medium mb-2']) }}>
    {{ $value ?? $slot }}
</label>
