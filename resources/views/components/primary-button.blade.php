<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-3 bg-primary-500 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-primary-600 hover:shadow-lg focus:bg-primary-600 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-secondary-400 focus:ring-offset-2 transition-all duration-300 transform active:scale-95']) }}>
    {{ $slot }}
</button>
