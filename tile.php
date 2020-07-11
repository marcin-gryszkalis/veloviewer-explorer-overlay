<?php

$x = intval($_GET['x']);
$y = intval($_GET['y']);
$z = intval($_GET['z']);
$id = intval($_GET['id']);

header('Content-type: image/png');

error_log("id($id) $x:$y @ $z");
# TODO: support for zoom<14 (buid image)
if ($z < 14 || $id == 0 || !file_exists("cache/$id.php"))
{
    readfile("empty256x256.png");
    exit();
}


include("cache/$id.php");

$f_t = $f_l = true;

$ox = $x; # original
$oy = $y;

if ($z > 14)
{
    $dz = $z - 14;
    $x = intval($x / (2**$dz));
    $y = intval($y / (2**$dz)); 

    $f_l = ($x == ($ox / (2**$dz)));
    $f_t = ($y == ($oy / (2**$dz)));
}

$png = imagecreatefrompng("empty256x256.png");
imagesavealpha($png, true);
$c_bg = imagecolorallocatealpha($png, 255, 0, 0, 100);
$c_frame = imagecolorallocatealpha($png, 255, 0, 0, 50);

# error_log("$x:$y = ".array_key_exists("$x:$y", $exp));

if (array_key_exists("$x:$y", $exp))
{
    imagefilledrectangle($png, 0, 0, 255, 255, $c_bg);
}

if ($f_l) imageline($png, 0, 0, 0, 255, $c_frame);
if ($f_t) imageline($png, 0, 0, 255, 0, $c_frame);

imagepng($png);

