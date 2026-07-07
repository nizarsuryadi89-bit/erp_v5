<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Simple_barcode
{

    // Code 128B Patterns (Widths: Bar Space Bar Space Bar Space)
    // 1=Module, 2=Double Module, etc.
    // Sum of widths = 11 modules (except Stop = 13)
    private $patterns = [
        32 => '212222',
        33 => '222122',
        34 => '222221',
        35 => '121223',
        36 => '121322',
        37 => '131222',
        38 => '122213',
        39 => '122312',
        40 => '132212',
        41 => '221213',
        42 => '221312',
        43 => '231212',
        44 => '112232',
        45 => '122132',
        46 => '122231',
        47 => '113222',
        48 => '123122',
        49 => '123221',
        50 => '223211',
        51 => '221132',
        52 => '221231',
        53 => '213212',
        54 => '223112',
        55 => '312131',
        56 => '311222',
        57 => '321122',
        58 => '321221',
        59 => '312212',
        60 => '322112',
        61 => '322211',
        62 => '212123',
        63 => '212321',
        64 => '232121',
        65 => '111323',
        66 => '131123',
        67 => '131321',
        68 => '112313',
        69 => '132113',
        70 => '132311',
        71 => '211313',
        72 => '231113',
        73 => '231311',
        74 => '112133',
        75 => '112331',
        76 => '132131',
        77 => '113123',
        78 => '113321',
        79 => '133121',
        80 => '313121',
        81 => '211331',
        82 => '231131',
        83 => '213113',
        84 => '213311',
        85 => '213131',
        86 => '311123',
        87 => '311321',
        88 => '331121',
        89 => '312113',
        90 => '312311',
        91 => '332111',
        92 => '314111',
        93 => '221411',
        94 => '431111',
        95 => '111224',
        96 => '111422',
        97 => '121124',
        98 => '121421',
        99 => '141122',
        100 => '141221',
        101 => '112214',
        102 => '112412',
        103 => '122114', // Start A
        104 => '122411', // Start B
        105 => '142112', // Start C
        106 => '2331112' // Stop
    ];

    public function generate($text, $filepath, $height = 50, $scale = 2)
    {
        // Enforce Code 128 Set B (ASCII 32-127) for simplicity
        // Valid chars: Space (32) to Del (127)
        // We generally map 32-126 directly.

        $code = [];
        $checksum = 104; // Start B value

        // Start B
        $code[] = $this->patterns[104];

        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $char = ord($text[$i]);
            // Map ASCII to Value (ASCII 32 is value 0)
            if ($char < 32 || $char > 126)
                $char = 32; // Sanitize
            $val = $char - 32;

            $code[] = $this->patterns[$val + 32]; // My array keys are offset by +32 to match ASCII for readability except control chars

            $checksum += ($val * ($i + 1));
        }

        // Checksum
        $checkVal = $checksum % 103;
        // Map checksum value back to pattern key
        // Keys 32..126 map to values 0..94
        // Values 95..106 need distinct keys if I used strict array
        // Checksum can be > 94. 
        // My array keys 32..106 covers values 0..74. 
        // Wait, my array keys correspond to ASCII for 32..126 (values 0..94).
        // But for checksum > 94, I need patterns 95..106.
        // I entered patterns for keys 32..106.
        // Values 0..94 are at keys 32..126.
        // Values 95..106:
        // 95 -> Key? I put patterns at 95..106 in array.
        // Let's recheck array structure.
        // Keys in $this->patterns are somewhat mixed indices.
        // 32..102 are Values 0..70?
        // Let's realign the array to use VALUES (0-106) as keys for safety.

        return $this->generate_safe($text, $filepath, $height, $scale);
    }

    private function generate_safe($text, $filepath, $height, $scale)
    {
        $patterns = $this->get_patterns();

        $seq = [];
        $seq[] = $patterns[104]; // Start B
        $sum = 104;

        for ($i = 0; $i < strlen($text); $i++) {
            $c = ord($text[$i]);
            if ($c < 32 || $c > 126)
                $c = 32;
            $val = $c - 32;
            $seq[] = $patterns[$val];
            $sum += $val * ($i + 1);
        }

        $check = $sum % 103;
        $seq[] = $patterns[$check];
        $seq[] = $patterns[106]; // Stop

        // Calculate total modules
        $total_width = 0;
        foreach ($seq as $p) {
            for ($j = 0; $j < strlen($p); $j++)
                $total_width += (int) $p[$j];
        }

        $img_width = $total_width * $scale;
        $img = imagecreate($img_width + 20, $height); // padding
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);

        imagefill($img, 0, 0, $white);

        $x = 10;
        foreach ($seq as $p) {
            for ($j = 0; $j < strlen($p); $j++) {
                $w = (int) $p[$j] * $scale;
                if ($j % 2 == 0) { // Bar
                    imagefilledrectangle($img, $x, 0, $x + $w - 1, $height, $black);
                }
                // else Space (skip)
                $x += $w;
            }
        }

        imagepng($img, $filepath);
        imagedestroy($img);
        return true;
    }

    private function get_patterns()
    {
        return [
            0 => '212222',
            1 => '222122',
            2 => '222221',
            3 => '121223',
            4 => '121322',
            5 => '131222',
            6 => '122213',
            7 => '122312',
            8 => '132212',
            9 => '221213',
            10 => '221312',
            11 => '231212',
            12 => '112232',
            13 => '122132',
            14 => '122231',
            15 => '113222',
            16 => '123122',
            17 => '123221',
            18 => '223211',
            19 => '221132',
            20 => '221231',
            21 => '213212',
            22 => '223112',
            23 => '312131',
            24 => '311222',
            25 => '321122',
            26 => '321221',
            27 => '312212',
            28 => '322112',
            29 => '322211',
            30 => '212123',
            31 => '212321',
            32 => '232121',
            33 => '111323',
            34 => '131123',
            35 => '131321',
            36 => '112313',
            37 => '132113',
            38 => '132311',
            39 => '211313',
            40 => '231113',
            41 => '231311',
            42 => '112133',
            43 => '112331',
            44 => '132131',
            45 => '113123',
            46 => '113321',
            47 => '133121',
            48 => '313121',
            49 => '211331',
            50 => '231131',
            51 => '213113',
            52 => '213311',
            53 => '213131',
            54 => '311123',
            55 => '311321',
            56 => '331121',
            57 => '312113',
            58 => '312311',
            59 => '332111',
            60 => '314111',
            61 => '221411',
            62 => '431111',
            63 => '111224',
            64 => '111422',
            65 => '121124',
            66 => '121421',
            67 => '141122',
            68 => '141221',
            69 => '112214',
            70 => '112412',
            71 => '122114',
            72 => '122411',
            73 => '142112',
            74 => '142211',
            75 => '241211',
            76 => '221114',
            77 => '413111',
            78 => '241112',
            79 => '134111',
            80 => '111242',
            81 => '121142',
            82 => '121241',
            83 => '114212',
            84 => '124112',
            85 => '124211',
            86 => '411212',
            87 => '421112',
            88 => '421211',
            89 => '212141',
            90 => '214121',
            91 => '412121',
            92 => '111143',
            93 => '111341',
            94 => '131141',
            95 => '114113',
            96 => '114311',
            97 => '411113',
            98 => '411311',
            99 => '113141',
            100 => '114131',
            101 => '311141',
            102 => '411131',
            103 => '211412',
            104 => '211214',
            105 => '211232',
            106 => '2331112'
        ];
    }
}
