<?php

namespace App\Support;

use InvalidArgumentException;

class NumberParser
{
    /**
     * Parse nominal format Inggris / Indonesia ke float sesuai aturan:
     * 1. Ada titik dan koma: pemisah terakhir adalah desimal, yang lain ribuan.
     * 2. Hanya koma: tiap grup setelah koma persis 3 digit -> ribuan Inggris; 1-2 digit -> desimal Indonesia.
     * 3. Hanya titik: tiap grup setelah titik persis 3 digit -> ribuan Indonesia; 1-2 digit -> desimal Inggris.
     * 4. Selain pola valid -> lempar InvalidArgumentException("Format angka tidak dikenali").
     */
    public static function parse($val): float
    {
        if (is_numeric($val) && !is_string($val)) {
            return (float) $val;
        }

        if ($val === null) {
            throw new InvalidArgumentException('Format angka tidak dikenali');
        }

        $str = trim((string) $val);

        // Hapus prefix Rp atau Rp. jika ada
        $str = preg_replace('/^Rp\.?\s*/i', '', $str);
        $str = trim($str);

        if ($str === '') {
            throw new InvalidArgumentException('Format angka tidak dikenali');
        }

        // Tidak boleh ada karakter selain digit, titik, koma
        if (preg_match('/[^\d.,]/', $str)) {
            throw new InvalidArgumentException('Format angka tidak dikenali');
        }

        $hasDot = strpos($str, '.') !== false;
        $hasComma = strpos($str, ',') !== false;

        // KASUS 1: Mengandung titik dan koma
        if ($hasDot && $hasComma) {
            $lastDot = strrpos($str, '.');
            $lastComma = strrpos($str, ',');

            if ($lastDot > $lastComma) {
                // Titik adalah desimal (Format Inggris: 436,000.00 / 94,876.35)
                if (substr_count($str, '.') !== 1 || !preg_match('/\.\d{1,2}$/', $str)) {
                    throw new InvalidArgumentException('Format angka tidak dikenali');
                }
                $intPart = substr($str, 0, $lastDot);
                if (!preg_match('/^\d{1,3}(,\d{3})*$/', $intPart)) {
                    throw new InvalidArgumentException('Format angka tidak dikenali');
                }
                $clean = str_replace(',', '', $str);
                return (float) $clean;
            } else {
                // Koma adalah desimal (Format Indonesia: 436.000,00 / 1.500.000,00)
                if (substr_count($str, ',') !== 1 || !preg_match('/,\d{1,2}$/', $str)) {
                    throw new InvalidArgumentException('Format angka tidak dikenali');
                }
                $intPart = substr($str, 0, $lastComma);
                if (!preg_match('/^\d{1,3}(\.\d{3})*$/', $intPart)) {
                    throw new InvalidArgumentException('Format angka tidak dikenali');
                }
                $clean = str_replace('.', '', $intPart) . '.' . substr($str, $lastComma + 1);
                return (float) $clean;
            }
        }

        // KASUS 2: Hanya mengandung koma
        if ($hasComma) {
            // Cek ribuan Inggris: setiap grup setelah koma persis 3 digit (436,000 atau 1,234,567)
            if (preg_match('/^\d{1,3}(,\d{3})+$/', $str)) {
                $clean = str_replace(',', '', $str);
                return (float) $clean;
            }

            // Cek desimal Indonesia: 1 koma diikuti 1-2 digit (4,5 -> 4.50)
            if (substr_count($str, ',') === 1 && preg_match('/^\d+,\d{1,2}$/', $str)) {
                $clean = str_replace(',', '.', $str);
                return (float) $clean;
            }

            throw new InvalidArgumentException('Format angka tidak dikenali');
        }

        // KASUS 3: Hanya mengandung titik
        if ($hasDot) {
            // Cek ribuan Indonesia: setiap grup setelah titik persis 3 digit (436.000 atau 1.234.567)
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $str)) {
                $clean = str_replace('.', '', $str);
                return (float) $clean;
            }

            // Cek desimal Inggris: 1 titik diikuti 1-2 digit (1500.50 -> 1500.50)
            if (substr_count($str, '.') === 1 && preg_match('/^\d+\.\d{1,2}$/', $str)) {
                return (float) $str;
            }

            throw new InvalidArgumentException('Format angka tidak dikenali');
        }

        // KASUS 4: Angka bulat tanpa pemisah
        if (preg_match('/^\d+$/', $str)) {
            return (float) $str;
        }

        throw new InvalidArgumentException('Format angka tidak dikenali');
    }

    /**
     * Cek apakah format angka valid
     */
    public static function isValid($val): bool
    {
        try {
            self::parse($val);
            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Format angka ke format tampilan Indonesia: 1.500.000,00
     */
    public static function formatId(float $val): string
    {
        return number_format($val, 2, ',', '.');
    }
}
