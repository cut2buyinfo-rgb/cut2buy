<?php
// File: /includes/ImageHasher.php
// A simple, self-contained perceptual hash implementation. No dependencies.

class ImageHasher
{
    private int $width;
    private int $height;

    public function __construct(int $width = 8, int $height = 8)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function hash(string $path): ?string
    {
        try {
            if (!file_exists($path)) {
                return null;
            }
            
            $image = @imagecreatefromstring(file_get_contents($path));
            if ($image === false) {
                return null;
            }

            $resized = imagecreatetruecolor($this->width + 1, $this->height);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $this->width + 1, $this->height, imagesx($image), imagesy($image));
            imagefilter($resized, IMG_FILTER_GRAYSCALE);
            
            $bits = '';
            for ($y = 0; $y < $this->height; $y++) {
                for ($x = 0; $x < $this->width; $x++) {
                    $left = imagecolorat($resized, $x, $y) & 0xFF;
                    $right = imagecolorat($resized, $x + 1, $y) & 0xFF;
                    $bits .= ($left < $right) ? '1' : '0';
                }
            }
            
            imagedestroy($image);
            imagedestroy($resized);

            return self::binaryToHex($bits);
        } catch (\Exception $e) {
            error_log("ImageHasher Error: " . $e->getMessage());
            return null;
        }
    }

    public static function distance(string $hash1, string $hash2): int
    {
        $bits1 = self::hexToBinary($hash1);
        $bits2 = self::hexToBinary($hash2);

        if (strlen($bits1) !== strlen($bits2)) {
            return 64; // Max distance if lengths differ
        }
        
        $distance = 0;
        for ($i = 0; $i < strlen($bits1); $i++) {
            if ($bits1[$i] !== $bits2[$i]) {
                $distance++;
            }
        }
        return $distance;
    }

    private static function binaryToHex(string $binary): string
    {
        return gmp_strval(gmp_init($binary, 2), 16);
    }
    
    private static function hexToBinary(string $hex): string
    {
        return str_pad(gmp_strval(gmp_init($hex, 16), 2), 64, '0', STR_PAD_LEFT);
    }
}