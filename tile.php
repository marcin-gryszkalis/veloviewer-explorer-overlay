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

if ($z > 14)
{
    $dz = $z - 14;
    $x = intval($x / (2**$dz));
    $y = intval($y / (2**$dz));
}

include("cache/$id.php");
error_log("$x:$y = ".array_key_exists("$x:$y", $exp));

if (array_key_exists("$x:$y", $exp))
{
    readfile("red256x256.png");
}
else
{
    readfile("empty256x256.png");
}

