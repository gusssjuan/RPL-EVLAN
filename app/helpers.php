<?php

if (! function_exists('format_rupiah')) {
    /**
     * Format an integer amount into Rupiah currency format.
     * Example: 18450000 -> "Rp 18.450.000"
     */
    function format_rupiah(int|float|null $nominal): string
    {
        $amount = (int) ($nominal ?? 0);

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

