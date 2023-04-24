<?php

namespace marijnvdwerf\palette\Tests\Feature;

use Intervention\Image\ImageManager;
use marijnvdwerf\palette\Palette;
use marijnvdwerf\palette\Swatch;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class LibraryTest extends TestCase
{
    private $manager;

    protected function setUp(): void
    {
        $driver = getenv('IMAGE_DRIVER');
        if (!$driver) {
            $driver = 'gd';
        }

        $this->manager = new ImageManager(['driver' => $driver]);
    }

    /**
     * @dataProvider filesProvider
     */
    public function testConvertsFile(\SplFileInfo $file)
    {
        $image = $this->manager->make($file);
        $palette = Palette::generate($image);

        $this->assertInstanceOf(Palette::class, $palette);

        $funcs = [
            'vibrant' => 'getVibrantSwatch',
            'light_vibrant' => 'getLightVibrantSwatch',
            'dark_vibrant' => 'getDarkVibrantSwatch',
            'muted' => 'getMutedSwatch',
            'light_muted' => 'getLightMutedSwatch',
            'dark_muted' => 'getDarkMutedSwatch',
        ];

        foreach ($funcs as $func) {
            $swatch = $palette->$func();
            if ($swatch !== null) {
                $this->assertInstanceOf(Swatch::class, $swatch);
            }
        }
    }

    public static function filesProvider()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/../../specs') as $file) {
            yield $file->getRelativePathname() => [$file];
        }
    }
}
