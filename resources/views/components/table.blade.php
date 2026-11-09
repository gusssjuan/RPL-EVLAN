<div class="overflow-x-auto rounded-[12px] border border-gray-200">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 text-sm']) }}>
        @isset($header)
            <thead class="bg-gray-50 text-gray-600 font-semibold text-xs uppercase tracking-wider text-left">
                {{ $header }}
            </thead>
        @endisset
        <tbody class="divide-y divide-gray-100 bg-white text-gray-800">
            {{ $slot }}
        </tbody>
        @isset($footer)
            <tfoot class="bg-gray-50 font-semibold text-gray-800">
                {{ $footer }}
            </tfoot>
        @endisset
    </table>
</div>

