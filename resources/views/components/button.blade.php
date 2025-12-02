<button {{ $attributes->merge(['type' => 'submit', 'class' => 'rounded-sm py-3 px-4 bg-purple-500 shadow-md text-white font-medium text-sm w-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
