<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Support\NumberParser;
use InvalidArgumentException;

class NumberParserTest extends TestCase
{
    /**
     * Test parsing format Inggris dan Indonesia sesuai aturan Bagian C
     */
    public function test_parsing_valid_cases(): void
    {
        // 1. Format Inggris dengan ribuan dan 2 desimal
        $this->assertEquals(436000.00, NumberParser::parse('436,000.00'));

        // 2. Format Inggris desimal pecahan
        $this->assertEquals(94876.35, NumberParser::parse('94,876.35'));

        // 3. Format Indonesia dengan ribuan dan 2 desimal
        $this->assertEquals(1500000.00, NumberParser::parse('1.500.000,00'));

        // 4. Desimal Inggris tanpa pemisah ribuan
        $this->assertEquals(1500.50, NumberParser::parse('1500.50'));

        // 5. Desimal Indonesia 1 digit
        $this->assertEquals(4.50, NumberParser::parse('4,5'));

        // 6. Desimal Indonesia 2 digit
        $this->assertEquals(4.50, NumberParser::parse('4,50'));

        // 7. Ribuan Inggris tanpa desimal (grup 3 digit)
        $this->assertEquals(436000.00, NumberParser::parse('436,000'));

        // 8. Ribuan Indonesia tanpa desimal (grup 3 digit)
        $this->assertEquals(436000.00, NumberParser::parse('436.000'));

        // 9. Ribuan bertingkat Inggris
        $this->assertEquals(1234567.00, NumberParser::parse('1,234,567'));

        // 10. Ribuan bertingkat Indonesia
        $this->assertEquals(1234567.00, NumberParser::parse('1.234.567'));

        // 11. Angka bulat biasa
        $this->assertEquals(50000.00, NumberParser::parse('50000'));

        // 12. Angka dengan prefix Rp
        $this->assertEquals(436000.00, NumberParser::parse('Rp 436,000.00'));
        $this->assertEquals(1500000.00, NumberParser::parse('Rp. 1.500.000,00'));

        // 13. Nol
        $this->assertEquals(0.00, NumberParser::parse('0'));
        $this->assertEquals(0.00, NumberParser::parse('0,00'));
        $this->assertEquals(0.00, NumberParser::parse('0.00'));
    }

    /**
     * Test pola invalid yang harus ditolak dengan pesan "Format angka tidak dikenali"
     */
    public function test_parsing_invalid_cases(): void
    {
        $invalidCases = [
            'abc',          // Huruf
            '1,2.3,4',      // Pemisah campur aduk tidak beraturan
            '',             // String kosong
            '   ',          // Spasi saja
            '123.4567',     // Lebih dari 2 desimal pada titik tunggal
            '123,4567',     // Lebih dari 2 desimal pada koma tunggal
            '1.23.456',     // Grup titik bukan 3 digit
            '1,23,456',     // Grup koma bukan 3 digit
            '100..00',      // Titik ganda
            '100,,00',      // Koma ganda
        ];

        foreach ($invalidCases as $case) {
            try {
                NumberParser::parse($case);
                $this->fail("Expected InvalidArgumentException for input: '{$case}'");
            } catch (InvalidArgumentException $e) {
                $this->assertEquals('Format angka tidak dikenali', $e->getMessage());
            }
        }
    }
}
