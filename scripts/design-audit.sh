#!/usr/bin/env bash

set -uo pipefail

if [ "$#" -eq 0 ]; then
    echo "Pemakaian: bash scripts/design-audit.sh <berkas-atau-folder> [...]"
    exit 2
fi

files=()

for target in "$@"; do
    if [ -d "$target" ]; then
        while IFS= read -r file; do
            files+=("$file")
        done < <(find "$target" -type f -name '*.blade.php' | sort)
    elif [ -f "$target" ]; then
        files+=("$target")
    fi
done

if [ "${#files[@]}" -eq 0 ]; then
    echo "Tidak ada berkas Blade yang diperiksa."
    exit 2
fi

failures=0

report() {
    local level="$1"
    local file="$2"
    local line="$3"
    local rule="$4"

    echo "$level $file:$line $rule"

    if [ "$level" = "FAIL" ]; then
        failures=$((failures + 1))
    fi
}

while IFS= read -r file; do
    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "warna hex langsung di Blade"
    done < <(grep -nE '#[0-9a-fA-F]{3,8}\b' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "emoji atau simbol teks sebagai ikon"
    done < <(grep -nP '[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{25A0}-\x{25FF}]' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "gradien"
    done < <(grep -nE 'bg-gradient-|from-[a-z]|via-[a-z]|to-[a-z]' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "keluarga warna terlarang"
    done < <(grep -nE '\b(indigo|violet|purple|fuchsia)-[0-9]' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "radius besar di luar modal (rounded-xl/2xl/3xl)"
    done < <(grep -nE '\brounded-(xl|2xl|3xl)\b' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "bayangan pada panel/kartu"
    done < <(grep -nE '\bshadow-(sm|md|lg|xl|2xl)\b' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "label huruf kapital dengan tracking"
    done < <(grep -nE 'uppercase.*tracking-|tracking-.*uppercase' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "em dash berspasi"
    done < <(grep -nP ' — ' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "panah teks pada tautan/tombol"
    done < <(grep -nP '→|&rarr;' "$file" || true)

    while IFS=: read -r line _; do
        report FAIL "$file" "$line" "pemisah titik tengah"
    done < <(grep -nP ' · ' "$file" || true)
done < <(printf '%s\n' "${files[@]}")

echo "Berkas diperiksa: ${#files[@]}; pelanggaran FAIL: $failures"

if [ "$failures" -gt 0 ]; then
    exit 1
fi
