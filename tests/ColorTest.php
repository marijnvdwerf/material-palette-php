<?php

namespace marijnvdwerf\palette\Tests;

use marijnvdwerf\palette\Color\HSLColor;
use marijnvdwerf\palette\Color\RGBColor;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    const ALLOWED_OFFSET_HUE = 0.005;
    const ALLOWED_OFFSET_SATURATION = 0.005;
    const ALLOWED_OFFSET_LIGHTNESS = 0.005;

    /**
     * @url https://github.com/marijnvdwerf/material-palette-php/issues/5
     */
    public function testColorConversion()
    {
        $color = new RGBColor(0.8, 0.5, 0.2);
        $this->assertSame('rgb(204, 128, 51)', (string)$color);

        $hsl = $color->asHSLColor();
        $this->assertEqualsWithDelta(30 / 360, $hsl->getHue(), self::ALLOWED_OFFSET_HUE, 'Hue not within offset');
        $this->assertEqualsWithDelta(0.6, $hsl->getSaturation(), self::ALLOWED_OFFSET_SATURATION,
            'Saturation not within offset');
        $this->assertEqualsWithDelta(0.5, $hsl->getLightness(), self::ALLOWED_OFFSET_LIGHTNESS,
            'Lightness not within offset');
    }

    public function rgbHslDataprovider()
    {
        return [
            [RGBColor::fromHex(0x000000), new HSLColor(0, 0, 0)],
            [RGBColor::fromHex(0xFFFFFF), new HSLColor(0, 0, 1)],
            [RGBColor::fromHex(0x0000FF), new HSLColor(240 / 360, 1, 0.5)],
            [RGBColor::fromHex(0x00FF00), new HSLColor(120 / 360, 1, 0.5)],
            [RGBColor::fromHex(0xFF0000), new HSLColor(0, 1, 0.5)],
            [RGBColor::fromHex(0x00FFFF), new HSLColor(180 / 360, 1, 0.5)],
            [RGBColor::fromHex(0x2196F3), new HSLColor(207 / 360, 0.9, 0.54)],
            [RGBColor::fromHex(0xD1C4E9), new HSLColor(261 / 360, 0.46, 0.84)],
            [RGBColor::fromHex(0x311B92), new HSLColor(251.09 / 360, 0.687, 0.339)],
        ];
    }

    /**
     * @dataProvider rgbHslDataprovider
     */
    public function testHSLConversion(RGBColor $color, HSLColor $expected)
    {
        $actual = $color->asHSLColor();

        $this->assertEqualsWithDelta($expected->getHue(), $actual->getHue(), self::ALLOWED_OFFSET_HUE,
            'Hue not within offset');
        $this->assertEqualsWithDelta($expected->getSaturation(), $actual->getSaturation(),
            self::ALLOWED_OFFSET_SATURATION, 'Saturation not within offset');
        $this->assertEqualsWithDelta($expected->getLightness(), $actual->getLightness(), self::ALLOWED_OFFSET_LIGHTNESS,
            'Lightness not within offset');
    }
}
