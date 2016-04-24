#!/usr/bin/env php
<?php

use Intervention\Image\ImageManager;
use marijnvdwerf\palette\Color\AbstractColor;
use marijnvdwerf\palette\Color\RGBColor;
use marijnvdwerf\palette\Palette;
use marijnvdwerf\palette\Swatch;

require 'vendor/autoload.php';

$colors = [
    '000000' => '16', '00005f' => '17', '000087' => '18', '0000af' => '19', '0000d7' => '20',
    '0000ff' => '21', '005f00' => '22', '005f5f' => '23', '005f87' => '24', '005faf' => '25',
    '005fd7' => '26', '005fff' => '27', '008700' => '28', '00875f' => '29', '008787' => '30',
    '0087af' => '31', '0087d7' => '32', '0087ff' => '33', '00af00' => '34', '00af5f' => '35',
    '00af87' => '36', '00afaf' => '37', '00afd7' => '38', '00afff' => '39', '00d700' => '40',
    '00d75f' => '41', '00d787' => '42', '00d7af' => '43', '00d7d7' => '44', '00d7ff' => '45',
    '00ff00' => '46', '00ff5f' => '47', '00ff87' => '48', '00ffaf' => '49', '00ffd7' => '50',
    '00ffff' => '51', '5f0000' => '52', '5f005f' => '53', '5f0087' => '54', '5f00af' => '55',
    '5f00d7' => '56', '5f00ff' => '57', '5f5f00' => '58', '5f5f5f' => '59', '5f5f87' => '60',
    '5f5faf' => '61', '5f5fd7' => '62', '5f5fff' => '63', '5f8700' => '64', '5f875f' => '65',
    '5f8787' => '66', '5f87af' => '67', '5f87d7' => '68', '5f87ff' => '69', '5faf00' => '70',
    '5faf5f' => '71', '5faf87' => '72', '5fafaf' => '73', '5fafd7' => '74', '5fafff' => '75',
    '5fd700' => '76', '5fd75f' => '77', '5fd787' => '78', '5fd7af' => '79', '5fd7d7' => '80',
    '5fd7ff' => '81', '5fff00' => '82', '5fff5f' => '83', '5fff87' => '84', '5fffaf' => '85',
    '5fffd7' => '86', '5fffff' => '87', '870000' => '88', '87005f' => '89', '870087' => '90',
    '8700af' => '91', '8700d7' => '92', '8700ff' => '93', '875f00' => '94', '875f5f' => '95',
    '875f87' => '96', '875faf' => '97', '875fd7' => '98', '875fff' => '99', '878700' => '100',
    '87875f' => '101', '878787' => '102', '8787af' => '103', '8787d7' => '104', '8787ff' => '105',
    '87af00' => '106', '87af5f' => '107', '87af87' => '108', '87afaf' => '109', '87afd7' => '110',
    '87afff' => '111', '87d700' => '112', '87d75f' => '113', '87d787' => '114', '87d7af' => '115',
    '87d7d7' => '116', '87d7ff' => '117', '87ff00' => '118', '87ff5f' => '119', '87ff87' => '120',
    '87ffaf' => '121', '87ffd7' => '122', '87ffff' => '123', 'af0000' => '124', 'af005f' => '125',
    'af0087' => '126', 'af00af' => '127', 'af00d7' => '128', 'af00ff' => '129', 'af5f00' => '130',
    'af5f5f' => '131', 'af5f87' => '132', 'af5faf' => '133', 'af5fd7' => '134', 'af5fff' => '135',
    'af8700' => '136', 'af875f' => '137', 'af8787' => '138', 'af87af' => '139', 'af87d7' => '140',
    'af87ff' => '141', 'afaf00' => '142', 'afaf5f' => '143', 'afaf87' => '144', 'afafaf' => '145',
    'afafd7' => '146', 'afafff' => '147', 'afd700' => '148', 'afd75f' => '149', 'afd787' => '150',
    'afd7af' => '151', 'afd7d7' => '152', 'afd7ff' => '153', 'afff00' => '154', 'afff5f' => '155',
    'afff87' => '156', 'afffaf' => '157', 'afffd7' => '158', 'afffff' => '159', 'd70000' => '160',
    'd7005f' => '161', 'd70087' => '162', 'd700af' => '163', 'd700d7' => '164', 'd700ff' => '165',
    'd75f00' => '166', 'd75f5f' => '167', 'd75f87' => '168', 'd75faf' => '169', 'd75fd7' => '170',
    'd75fff' => '171', 'd78700' => '172', 'd7875f' => '173', 'd78787' => '174', 'd787af' => '175',
    'd787d7' => '176', 'd787ff' => '177', 'd7af00' => '178', 'd7af5f' => '179', 'd7af87' => '180',
    'd7afaf' => '181', 'd7afd7' => '182', 'd7afff' => '183', 'd7d700' => '184', 'd7d75f' => '185',
    'd7d787' => '186', 'd7d7af' => '187', 'd7d7d7' => '188', 'd7d7ff' => '189', 'd7ff00' => '190',
    'd7ff5f' => '191', 'd7ff87' => '192', 'd7ffaf' => '193', 'd7ffd7' => '194', 'd7ffff' => '195',
    'ff0000' => '196', 'ff005f' => '197', 'ff0087' => '198', 'ff00af' => '199', 'ff00d7' => '200',
    'ff00ff' => '201', 'ff5f00' => '202', 'ff5f5f' => '203', 'ff5f87' => '204', 'ff5faf' => '205',
    'ff5fd7' => '206', 'ff5fff' => '207', 'ff8700' => '208', 'ff875f' => '209', 'ff8787' => '210',
    'ff87af' => '211', 'ff87d7' => '212', 'ff87ff' => '213', 'ffaf00' => '214', 'ffaf5f' => '215',
    'ffaf87' => '216', 'ffafaf' => '217', 'ffafd7' => '218', 'ffafff' => '219', 'ffd700' => '220',
    'ffd75f' => '221', 'ffd787' => '222', 'ffd7af' => '223', 'ffd7d7' => '224', 'ffd7ff' => '225',
    'ffff00' => '226', 'ffff5f' => '227', 'ffff87' => '228', 'ffffaf' => '229', 'ffffd7' => '230',
    'ffffff' => '231', '080808' => '232', '121212' => '233', '1c1c1c' => '234', '262626' => '235',
    '303030' => '236', '3a3a3a' => '237', '444444' => '238', '4e4e4e' => '239', '585858' => '240',
    '626262' => '241', '6c6c6c' => '242', '767676' => '243', '808080' => '244', '8a8a8a' => '245',
    '949494' => '246', '9e9e9e' => '247', 'a8a8a8' => '248', 'b2b2b2' => '249', 'bcbcbc' => '250',
    'c6c6c6' => '251', 'd0d0d0' => '252', 'dadada' => '253', 'e4e4e4' => '254', 'eeeeee' => '255',
];


function getClosestColor(AbstractColor $color)
{
    global $colors;

    $referenceColor = $color->asRGBColor();
    $referenceRed = $referenceColor->getRedComponent() * 255;
    $referenceGreen = $referenceColor->getGreenComponent() * 255;
    $referenceBlue = $referenceColor->getBlueComponent() * 255;

    $closestDistance = PHP_INT_MAX;
    $closestId = -1;
    foreach ($colors as $hex => $id) {
        $hexRed = hexdec(substr($hex, 0, 2));
        $hexGreen = hexdec(substr($hex, 2, 2));
        $hexBlue = hexdec(substr($hex, 4, 2));

        $distance = pow(abs($hexRed - $referenceRed), 2) + pow(abs($hexGreen - $referenceGreen), 2) + pow(abs($hexBlue - $referenceBlue), 2);

        if ($distance < $closestDistance) {
            $closestDistance = $distance;
            $closestId = $id;
        }
    }

    return $closestId;
}

if (count($argv) !== 2) {
    echo 'Usage: `php cli.php [image]`' . PHP_EOL;
    exit(1);
}

$file = $argv[1];
if (!file_exists($file)) {
    echo 'File couldn\'t be found' . PHP_EOL;
    exit(2);
}

$manager = new ImageManager(array('driver' => 'imagick'));
$image = $manager->make($file);

$palette = Palette::generate($image);

$v1s = ['Vibrant', 'Muted'];
$v2s = ['', 'Light', 'Dark'];

$maxWidth = 0;
foreach ($v1s as $v1) {
    foreach ($v2s as $v2) {
        $maxWidth = max(strlen(implode(' ', array_filter([$v2, $v1]))), $maxWidth);
    }
}

foreach ($v1s as $v1) {
    foreach ($v2s as $v2) {
        /** @var Swatch $swatch */
        $swatch = call_user_func([$palette, 'get' . $v2 . $v1 . 'Swatch']);
        $swatchName = implode(' ', array_filter([$v2, $v1]));

        if ($swatch == null) {
            echo $swatchName . PHP_EOL;
            continue;
        }

        $swatchHex = $swatch->getColor()->asRGBColor()->asHex();
        $colorCodeXterm = "\033[48;5;" . getClosestColor($swatch->getColor()) . "m";
        $foregroundColour = $foregroundColour = "\033[38;5;0m";
        if (AbstractColor::calculateContrast($swatch->getColor(), new RGBColor(1, 1, 1)) >= 3) {
            $foregroundColour = "\033[38;5;15m";
        }

        echo $colorCodeXterm . $foregroundColour . str_pad($swatchName, $maxWidth + 2, ' ', STR_PAD_RIGHT) . $swatchHex . "\033[0m" . PHP_EOL;
    }
}
