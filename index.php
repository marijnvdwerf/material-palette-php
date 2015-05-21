<?php

use Intervention\Image\ImageManager;
use marijnvdwerf\palette\Color\AbstractColor;
use marijnvdwerf\palette\Color\RGBColor;
use marijnvdwerf\palette\Palette;
use marijnvdwerf\palette\Swatch;

require 'vendor/autoload.php';

?>
    <style type="text/css">
        body {
            font-family: Roboto, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .swatch {
            padding: 15px;
            list-style-type: none;
            font-weight: 500;
            font-size: 14px;
        }

        .swatch__color {
            float: right;
        }

        .album {
            width: 320px;
            float: left;
            margin-left: 40px;
            padding-bottom: 40px;
        }


    </style>

<?php


$basePath = './specs/artwork';
$files = scandir($basePath);

$manager = new ImageManager(array('driver' => 'imagick'));
foreach ($files as $filename) {
    if ($filename[0] === '.') {
        continue;
    }

    $image = $manager->make($basePath . '/' . $filename);

    $palette = Palette::generate($image);


    echo '<div class="album">';
    echo "<img src='data:image/png;base64," . base64_encode($image->resize(320, 0)->encode('png')) . "' />";
    echo '<h1>' . $filename . '</h1>';
    printPalette($palette);
    //printSwatches($swatches);
    echo '</div>';
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

    $textColors = [
        new RGBColor(1, 1, 1, 0.87),
        new RGBColor(1, 1, 1),
        new RGBColor(0, 0, 0, 0.87),
        new RGBColor(0, 0, 0)
    ];

    echo '<ul>';
    foreach ($v1s as $v1) {
        foreach ($v2s as $v2) {
            /** @var Swatch $swatch */
            $swatch = call_user_func([$palette, 'get' . $v2 . $v1 . 'Swatch']);

            $swatchName = implode(' ', array_filter([$v2, $v1]));
            if ($swatch !== null) {
                $textColor = null;
                foreach ($textColors as $foregroundColor) {
                    if (AbstractColor::calculateContrast($swatch->getColor(), $foregroundColor) >= 3) {
                        $textColor = $foregroundColor;
                        break;
                    }
                }
                echo sprintf('<li class="swatch" style="background-color: %1$s; color: %3$s"><span class="swatch__name">%2$s</span> <span class="swatch__color">%1$s</span></li>', $swatch->getColor(), $swatchName, $textColor);
            } else {
                echo sprintf('<li class="swatch swatch--empty">%s</li>', $swatchName);
            }

        }
    }
    echo '</ul>';
}

