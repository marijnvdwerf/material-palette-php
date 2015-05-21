<?php

use Intervention\Image\ImageManager;
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
        }

        .swatch__color {
            float: right;
        }

        .album {
            width: 320px;
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
    echo "<img src='data:image/png;base64," . base64_encode($image->encode('png')) . "' />";
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

    echo '<ul>';
    foreach ($v1s as $v1) {
        foreach ($v2s as $v2) {
            /** @var Swatch $swatch */
            $swatch = call_user_func([$palette, 'get' . $v2 . $v1 . 'Swatch']);

            $swatchName = implode(' ', array_filter([$v2, $v1]));
            if ($swatch !== null) {
                echo sprintf('<li class="swatch" style="background-color: %1$s"><span class="swatch__name">%2$s</span> <span class="swatch__color">%1$s</span></li>', $swatch->getColor(), $swatchName);
            } else {
                echo sprintf('<li class="swatch swatch--empty">%s</li>', $swatchName);
            }

        }
    }
    echo '</ul>';
}

