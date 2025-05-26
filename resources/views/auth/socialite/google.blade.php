{{-- <x-filament::button :href="route('socialite.redirect', 'google')" tag="a" color="info" icon="heroicon-m-cloud" wire-navigate>
    Sign in with Google
</x-filament::button> --}}
<a href="{{ route('socialite.redirect', 'google') }}"
    style="--c-400:var(--info-400);--c-500:var(--info-500);--c-600:var(--info-600);"
    class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-info fi-color-info fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50">
    <svg class="fi-btn-icon transition duration-75 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
        <path
            d="M1 12.5A4.5 4.5 0 0 0 5.5 17H15a4 4 0 0 0 1.866-7.539 3.504 3.504 0 0 0-4.504-4.272A4.5 4.5 0 0 0 4.06 8.235 4.502 4.502 0 0 0 1 12.5Z">
        </path>
    </svg>

    <span class="fi-btn-label">
        Sign in with Google
    </span>
</a>
