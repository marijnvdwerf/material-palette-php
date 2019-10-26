<?php

namespace marijnvdwerf\palette\Tests;

use Intervention\Image\ImageManager;
use marijnvdwerf\palette\Palette;
use PHPUnit\Framework\TestCase;

class PaletteTest extends TestCase
{
    public function testGenerateEmptySwatches()
    {
        $expected = [
            'vibrant' => '#DD4B4A',
            'light_vibrant' => null,
            'dark_vibrant' => '#701615',
            'muted' => '#92554D',
            'light_muted' => '#AFAFB0',
            'dark_muted' => '#543333',
        ];

        $manager = new ImageManager();
        $image = $manager->make(__DIR__.'/data/soroush-karimi-Mx5kwvzeGC0-unsplash.jpg');
        $palette = Palette::generate($image);

        $funcs = [
            'vibrant' => 'getVibrantSwatch',
            'light_vibrant' => 'getLightVibrantSwatch',
            'dark_vibrant' => 'getDarkVibrantSwatch',
            'muted' => 'getMutedSwatch',
            'light_muted' => 'getLightMutedSwatch',
            'dark_muted' => 'getDarkMutedSwatch',
        ];

        $actual = [];
        foreach ($funcs as $field => $func) {
            $swatch = $palette->$func();
            if ($swatch !== null) {
                $actual[$field] = $swatch->getColor()->asRGBColor()->asHex();
            } else {
                $actual[$field] = null;
            }
        }

        foreach ($expected as $key => $value) {
            $this->assertEquals(
                $value,
                $actual[$key],
                'Generate empty swatches (dark vibrant) - ' . $key
            );
        }

    }
}
