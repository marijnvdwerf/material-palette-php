<?php

use marijnvdwerf\palette\ImagickHistogramGenerator;
use marijnvdwerf\palette\Palette;
use marijnvdwerf\palette\Swatch;

require 'vendor/autoload.php';

$histogramGenerator = new ImagickHistogramGenerator();
$colorQuantizer = new \marijnvdwerf\palette\ColorCutQuantizer();


$files = scandir('./specs/artwork');
foreach ($files as $file) {
    if ($file[0] === '.') {
        continue;
    }

    $image = new Imagick('./specs/artwork/' . $file);

    $image->sampleImage(100, 100);

    $image->setFormat('png');

    $swatches = $histogramGenerator->generate($image);

    $swatches = $colorQuantizer->quantize($swatches, 16);

    //$palette = Palette::generate($swatches);

    echo '<h1>' . $file . '</h1>';
    echo "<img src='data:image/png;base64," . base64_encode($image->getImageBlob()) . "' />";
    printSwatches($swatches);
}


/**
 * @param $swatches Swatch[]
 */
function printSwatches($swatches)
{

    echo '<ul>';
    foreach ($swatches as $swatch) {
        echo sprintf('<li class="swatch" style="background-color: %1$s">%1$s</li>', $swatch->getColor()->asHex());
    }
    echo '</ul>';
}


function printPalette(Palette $palette)
{
    $v1s = ['Vibrant', 'Muted'];
    $v2s = ['', 'Light', 'Dark'];

    echo '<ul>';
    foreach ($v1s as $v1) {
        foreach ($v2s as $v2) {
            /** @var Swatch $swatch */
            $swatch = call_user_func([$palette, 'get' . $v2 . $v1 . 'Swatch']);

            $swatchName = implode(' ', array_filter([$v2, $v1]));
            if ($swatch !== null) {
                echo sprintf('<li class="swatch" style="background-color: %s">%s</li>', $swatch->getColor(), $swatchName);
            } else {
                echo sprintf('<li class="swatch swatch--empty">%s</li>', $swatchName);
            }

        }
    }
    echo '</ul>';
}
