<?php

namespace marijnvdwerf\palette;


use Intervention\Image\AbstractDriver;
use Intervention\Image\Gd\Driver as GDDriver;
use Intervention\Image\Image;
use Intervention\Image\Imagick\Driver as ImagickDriver;

class PaletteGenerator
{
    /** @var PaletteGenerator */
    private static $instance;

    /** @var ColorCutQuantizer */
    private $quantizer;

    /**
     * @param AbstractDriver $driver
     * @return HistogramGenerator
     * @throws \Exception
     */
    private function getHistogramGeneratorForDriver(AbstractDriver $driver)
    {
        if ($driver instanceof GDDriver) {
            return new GDHistogramGenerator();
        } elseif ($driver instanceof ImagickDriver) {
            return new ImagickHistogramGenerator();
        }

        throw new \Exception(sprintf('Unknown Driver (%s) is not supported.', get_class($driver)));
    }

    private function getQuantizer()
    {
        if ($this->quantizer === null) {
            $this->quantizer = new ColorCutQuantizer();
        }

        return $this->quantizer;
    }

    public function generate(Image $image)
    {
        $swatches = $this->getHistogramGeneratorForDriver($image->getDriver())->generate($image);
        $swatches = $this->getQuantizer()->quantize($swatches, 16);

        return new Palette($swatches);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new PaletteGenerator();
        }

        return self::$instance;
    }
}
