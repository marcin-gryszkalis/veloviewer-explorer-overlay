<?php

$x = intval($_GET['x']);
$y = intval($_GET['y']);
$z = intval($_GET['z']);
$id = intval($_GET['id']);

header('Content-type: image/png');

error_log("id($id) $x:$y @ $z");
if ($z < 7 || $id == 0 || !file_exists("cache/$id.php")) # z7 is lowest level that makes sense to display
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
 
if ($z >= 14)
{
    # error_log("$x:$y = ".array_key_exists("$x:$y", $exp));
    
    if (array_key_exists("$x:$y", $exp))
    {
        imagefilledrectangle($png, 0, 0, 255, 255, $c_bg);
    }
    
    if ($f_l) imageline($png, 0, 0, 0, 255, $c_frame);
    if ($f_t) imageline($png, 0, 0, 255, 0, $c_frame);
    
}
elseif ($z < 14) # lower limit is checked before
{
    $dz = 14 - $z;
    $zm = 2 ** $dz; # zoom multiplier
    $r = 256 / $zm; # small square size

    $jx = 0;
    foreach (range($x*$zm, $x*$zm + $zm - 1) as $ix)
    {
        $jy = 0;
        foreach (range($y*$zm, $y*$zm + $zm - 1) as $iy)
        {
#            error_log("z:$z $ix:$iy = ".array_key_exists("$ix:$iy", $exp));
            if (array_key_exists("$ix:$iy", $exp))
            {
                imagefilledrectangle($png, 
                    $r * $jx, $r * $jy, 
                    $r * $jx + $r - 1, $r * $jy + $r - 1, 
                    $c_bg);
            }
            $jy++;      
        }
        $jx++;
    }

    if ($z >= 10) # no frames for lower zoom levels
    {
        foreach (range(0, $zm - 1) as $i)
        {
            imageline($png, $i * $r, 0, $i * $r, 255, $c_frame);
            imageline($png, 0, $i * $r, 255, $i * $r, $c_frame);
        }
    }
 
}

imagepng($png);
