@props([
    'tabs' => [],
    'active' => null,
])

<div class="border-b border-gray-200">
    <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
        @foreach ($tabs as $key => $tab)
            @php
                $isActive = $active === $key;
                $label = is_array($tab) ? ($tab['label'] ?? $key) : $tab;
                $count = is_array($tab) ? ($tab['count'] ?? null) : null;
                $href = is_array($tab) ? ($tab['href'] ?? '#') : '#';
            @endphp
            <a
                href="{{ $href }}"
                class="{{ $isActive ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-3 px-1 border-b-2 text-sm flex items-center gap-2 transition-colors"
                @if ($isActive) aria-current="page" @endif
            >
                <span>{{ $label }}</span>
                @if ($count !== null)
                    <span class="{{ $isActive ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2 rounded-full text-xs font-medium">
                        {{ $count }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>
</div>

