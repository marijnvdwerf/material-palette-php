<?php

use Intervention\Image\ImageManager;
use marijnvdwerf\palette\Color\AbstractColor;
use marijnvdwerf\palette\Color\RGBColor;
use marijnvdwerf\palette\Palette;
use marijnvdwerf\palette\Swatch;
use Symfony\Component\ErrorHandler\Debug;

require 'vendor/autoload.php';

$containerWidth = 728;
$gutter = 4;
$columns = 4;

$albumWidth = ($containerWidth - $gutter * ($columns - 1)) / $columns;

set_time_limit(0);

Debug::enable();

?>
    <style type="text/css">
        body {
            font-family: Roboto, sans-serif;
        }

        .container {
            width: <?= $containerWidth + $gutter; ?>px;
            margin: 0 auto;
            overflow: auto;
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
            width: <?= $albumWidth; ?>px;
            float: left;
            margin-left: <?= $gutter; ?>px;
            padding-bottom: <?= $gutter; ?>px;
        }

        .album:nth-child(<?=$columns;?>n + 1) {
            clear: left;
        }

        .album__cover {
            display: block;
            width: 100%;
        }

        .album__header {
            position: relative;
        }

        .album__overlay {
            background: -webkit-linear-gradient(top, rgba(0, 0, 0, 0), rgba(0, 0, 0, .4));
            background: linear-gradient(top, rgba(0, 0, 0, 0), rgba(0, 0, 0, .4));
            padding: 11px 16px 15px;
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;

            color: rgb(255, 255, 255);
            text-shadow: 0 1px 2px rgb(0, 0, 0);
        }

        .album__title {
            font-size: 16px;
            line-height: 24px;
            font-weight: 500;
        }

        .album__artist {
            font-size: 14px;
            line-height: 18px;
            font-weight: 400;
        }

    </style>

<?php


$albumPath = './specs/artwork';
$albums = [];

foreach (scandir($albumPath) as $filename) {
    if ($filename[0] === '.') {
        continue;
    }

    preg_match('/^(.*?) - (.*?)\.png/', $filename, $matches);

    $albums[] = [
        'artist' => $matches[1],
        'title' => $matches[2],
        'artwork' => $albumPath . '/' . $filename
    ];
}

echo '<div class="container">';
$imagickBench = new Ubench();
$gdBench = new Ubench();

$imagickBench->start();
printImages($albums, new ImageManager(array('driver' => 'imagick')));
$imagickBench->end();

$gdBench->start();
printImages($albums, new ImageManager(array('driver' => 'gd')));
$gdBench->end();
echo '</div>';

dump('Imagick: script execution time: ' . $imagickBench->getTime() . "\n" . 'GD: script execution time: ' . $gdBench->getTime());

function printImages($albums, ImageManager $manager)
{
    global $albumWidth;

    foreach ($albums as $album) {
        $image = $manager->make($album['artwork']);

        $palette = Palette::generate($image);

        echo '<div class="album">';
        echo '<div class="album__header">';
        echo '<img class="album__cover" src="data:image/png;base64,' . base64_encode($image->resize($albumWidth, $albumWidth)->encode('png')) . '" />';
        echo '<div class="album__overlay">';
        echo '<h2 class="album__title">' . $album['title'] . '</h2>';
        echo '<h1 class="album__artist">' . $album['artist'] . '</h1>';
        echo '</div>';
        echo '</div>';
        printPalette($palette);
        //printSwatches($swatches);
        echo '</div>';

        $image->destroy();
    }
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
                echo sprintf('<li class="swatch" style="background-color: %1$s; color: %3$s">
<span class="swatch__name">%2$s</span>
<span class="swatch__color">%4$s</span>
</li>', $swatch->getColor(), $swatchName, $textColor, $swatch->getColor()->asRGBColor()->toHex());
            } else {
                echo sprintf('<li class="swatch swatch--empty">%s</li>', $swatchName);
            }

        }
    }
    echo '</ul>';
}

