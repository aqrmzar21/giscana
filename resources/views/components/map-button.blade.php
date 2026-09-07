@props(['icon' => 'filter', 'title' => ''])

<button 
    class="p-2 rounded-full bg-white shadow hover:bg-gray-100 absolute z-50"
    title="{{ $title }}"
>
    @if($icon === 'filter')
        <!-- Ikon Filter (Funnel) -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-6 w-6 text-gray-700" 
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M3 4h18l-7 8v6l-4 2v-8L3 4z" />
        </svg>
    @elseif($icon === 'point')
        <!-- Ikon Point (Map Pin) -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-6 w-6 text-red-600" 
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
        </svg>
    @endif
</button>
