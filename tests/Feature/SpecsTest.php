<?php

namespace marijnvdwerf\palette\Tests\Feature;

use Intervention\Image\ImageManager;
use marijnvdwerf\palette\Palette;
use PHPUnit\Framework\TestCase;

class SpecsTest extends TestCase
{
    private $manager;

    protected function setUp(): void
    {
        $driver = getenv('IMAGE_DRIVER');
        if (!$driver) {
            $driver = 'gd';
        }

        if ($driver !== 'gd') {
            $this->markTestSkipped('These specs only match the GD output');
        }

        $this->manager = new ImageManager(['driver'=>$driver]);
    }

    /**
     * @dataProvider specsProvider
     */
    public function testSpecsImage($title, array $expected)
    {
        $image = $this->manager->make(__DIR__ . '/../../specs/artwork/' . $title . '.png');
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
            $this->assertEquals($value, $actual[$key], $title . ' - ' . $key);
        }
    }

    public static function specsProvider()
    {
        return [
            [
                'Ellie Goulding - Halcyon Days',
                [
                    'vibrant' => '#A21171',
                    'light_vibrant' => '#65C6F1',
                    'dark_vibrant' => '#0D1E4A',
                    'muted' => '#A365A1',
                    'light_muted' => '#E0C6D8',
                    'dark_muted' => '#605A86',
                ],
            ],
            [
                'Fitz & The Tantrums - More Than Just A Dream',
                [
                    'vibrant' => '#B32F7F',
                    'light_vibrant' => '#E5A4CC',
                    'dark_vibrant' => '#101226',
                    'muted' => '#90507B',
                    'light_muted' => '#90A1A6',
                    'dark_muted' => '#1D2537',
                ],
            ],
            [
                'Foals - Holy Fire',
                [
                    'vibrant' => '#D9CC80',
                    'light_vibrant' => '#E9E0A7',
                    'dark_vibrant' => '#665424',
                    'muted' => '#B19F56',
                    'light_muted' => null,
                    'dark_muted' => '#2B2617',
                ],
            ],
            [
                'Foster The People - Supermodel',
                [
                    'vibrant' => '#EAB040',
                    'light_vibrant' => '#EDEAB9',
                    'dark_vibrant' => '#184D66',
                    'muted' => '#61929E',
                    'light_muted' => '#A1C3B1',
                    'dark_muted' => '#543B65',
                ],
            ],
            [
                'Jamie Lidell - Jamie Lidell',
                [
                    'vibrant' => '#D92081',
                    'light_vibrant' => '#DE9EBA',
                    'dark_vibrant' => '#108B50',
                    'muted' => '#60A552',
                    'light_muted' => '#BDC2C4',
                    'dark_muted' => '#292247',
                ],
            ],
            [
                'Janelle Monae - The Electric Lady',
                [
                    'vibrant' => '#CA5749',
                    'light_vibrant' => '#DA5D66',
                    'dark_vibrant' => '#5B2931',
                    'muted' => '#9E5F68',
                    'light_muted' => '#DDB0BD',
                    'dark_muted' => '#2D3E56',
                ],
            ],
            [
                'Kodaline - In A Perfect World',
                [
                    'vibrant' => '#4998B6',
                    'light_vibrant' => null,
                    'dark_vibrant' => '#254D5C',
                    'muted' => '#3D6672',
                    'light_muted' => '#97ACB8',
                    'dark_muted' => '#233C40',
                ],
            ],
            [
                'OneRepublic - Native',
                [
                    'vibrant' => '#972837',
                    'light_vibrant' => null,
                    'dark_vibrant' => '#661838',
                    'muted' => '#976866',
                    'light_muted' => '#A694B5',
                    'dark_muted' => '#6C4045',
                ],
            ],
            [
                'Pharrell Williams - G I R L',
                [
                    'vibrant' => '#E5B531',
                    'light_vibrant' => null,
                    'dark_vibrant' => '#512922',
                    'muted' => null,
                    'light_muted' => null,
                    'dark_muted' => '#1F1010',
                ],
            ],
            [
                'Rhye - Woman',
                [
                    'vibrant' => null,
                    'light_vibrant' => null,
                    'dark_vibrant' => null,
                    'muted' => '#7E7F83',
                    'light_muted' => '#BABCBD',
                    'dark_muted' => '#3F4044',
                ],
            ],
            [
                'The Strokes - Comedown Machine',
                [
                    'vibrant' => '#B22D29',
                    'light_vibrant' => null,
                    'dark_vibrant' => '#66312A',
                    'muted' => null,
                    'light_muted' => null,
                    'dark_muted' => null,

                ],
            ],
            [
                'Yuna - Nocturnal',
                [
                    'vibrant' => '#FAE812',
                    'light_vibrant' => '#D7A7A9',
                    'dark_vibrant' => '#AF1A34',
                    'muted' => '#A36576',
                    'light_muted' => '#DFD2D1',
                    'dark_muted' => '#5D3632',
                ],
            ],
        ];
    }
}
