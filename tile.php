<?php

# default config
$cfg['ms'] = 1;
$cfg['cl'] = 1;
$cfg['cv'] = 'ff0000';
$cfg['cc'] = '0000ff';
$cfg['cg'] = 'ff0000';
$cfg['cm'] = '0000c8';
$cfg['tv'] = 115;
$cfg['tc'] = 115;
$cfg['tg'] = 50;
$cfg['tm'] = 50;

$cfgs = $_GET['cfg'] ?? '';
$cfga = explode("/", $cfgs);
foreach ($cfga as $c)
{
    $ca = explode("-", $c);
    $cfg[$ca[0]] = $ca[1] ?? $cfg[$ca[0]] ?? '';
}

$zoom = 14; # default, 17 for squadrathinos
if (array_key_exists("zz", $cfg))
{
    $z = intval($cfg['zz']);
    if ($z >= 14 && $z <= 17)
    {
        $zoom = $z; 
    }      
}

$x = intval($_GET['x']);
$y = intval($_GET['y']);
$z = intval($_GET['z']);
$id = intval($_GET['id']);
header('Content-type: image/png');

$anon = !file_exists("cache/$id.php");

error_log("anon($anon) id($id) $x:$y @ $z zoom($zoom) cfg[$cfgs]");

if ($z < $zoom - 7) # lowest level that makes sense to display
{
    readfile("empty256x256.png");
    exit();
}

if (!$anon)
{
    include("cache/$id.php");
    include("cache/maxsq-top-$id.php");
    include("cache/maxsq-left-$id.php");
}

$f_t = $f_l = true;

$ox = $x; # original
$oy = $y;

if ($z > $zoom)
{
    $dz = $z - $zoom;
    $x = intval($x / (2**$dz));
    $y = intval($y / (2**$dz)); 

    $f_l = ($x == ($ox / (2**$dz)));
    $f_t = ($y == ($oy / (2**$dz)));
}

function colorfromhexa($img, $hc, $alpha)
{
    return 
    imagecolorallocatealpha($img, 
        hexdec(substr($hc, 0, 2)), 
        hexdec(substr($hc, 2, 2)), 
        hexdec(substr($hc, 4, 2)), 
        $alpha);
}

$png = imagecreatefrompng("empty256x256.png");
imagesavealpha($png, true);
$c_bg = colorfromhexa($png, $cfg['cv'], $cfg['tv']);
$c_bg_cluster = colorfromhexa($png, $cfg['cc'], $cfg['tc']);
$c_frame = colorfromhexa($png, $cfg['cg'], $cfg['tg']);
$c_frame_maxsq = colorfromhexa($png, $cfg['cm'], $cfg['tm']);
 
function in_cluster($x, $y, $exp)
{
    return 
        array_key_exists(($x-1).":".($y), $exp) &&
        array_key_exists(($x+1).":".($y), $exp) &&
        array_key_exists(($x).":".($y-1), $exp) &&
        array_key_exists(($x).":".($y+1), $exp);
}

if ($z >= $zoom)
{
    # error_log("$x:$y = ".array_key_exists("$x:$y", $exp));
    
    if (!$anon && array_key_exists("$x:$y", $exp))
    {
        imagefilledrectangle($png, 0, 0, 255, 255, $cfg['cl'] == 1 && in_cluster($x, $y, $exp) ? $c_bg_cluster : $c_bg);
    }
    
    if ($f_l) 
    {
        if (!$anon && $cfg['ms'] && array_key_exists("$x:$y", $maxsq_left))
        {
            imageline($png, 0, 0, 0, 255, $c_frame_maxsq);
            imageline($png, 1, 0, 1, 255, $c_frame_maxsq);
        }    
        else
        {
            imageline($png, 0, 0, 0, 255, $c_frame);
        }   
    }

    if ($f_t) 
    {
        if (!$anon && $cfg['ms'] && array_key_exists("$x:$y", $maxsq_top))
        {
            imageline($png, 0, 0, 255, 0, $c_frame_maxsq);
            imageline($png, 0, 1, 255, 1, $c_frame_maxsq);
        }
        else
        {
            imageline($png, 0, 0, 255, 0, $c_frame);
        }
    }

}
elseif ($z < $zoom) # lower limit is checked before
{
    $dz = $zoom - $z;
    $zm = 2 ** $dz; # zoom multiplier
    $r = 256 / $zm; # small square size

    if (!$anon)
    {
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
                        in_cluster($ix, $iy, $exp) ? $c_bg_cluster : $c_bg);
                }
                $jy++;      
            }
            $jx++;
        }
    }

    if ($z >= $zoom-4) # no frames for lower zoom levels
    {
        foreach (range(0, $zm - 1) as $i)
        {
            imageline($png, $i * $r, 0, $i * $r, 255, $c_frame);
            imageline($png, 0, $i * $r, 255, $i * $r, $c_frame);
        }
    }

    # max square requires checking all squares again 
    if (!$anon)
    {
        $jx = 0;
        foreach (range($x*$zm, $x*$zm + $zm - 1) as $ix)
        {
            $jy = 0;
            foreach (range($y*$zm, $y*$zm + $zm - 1) as $iy)
            {
                if (array_key_exists("$ix:$iy", $maxsq_left))
                {
                   imageline($png, $r * $jx, $r * $jy, $r * $jx, $r * $jy + $r - 1, $c_frame_maxsq);
                   imageline($png, $r * $jx + 1, $r * $jy, $r * $jx + 1, $r * $jy + $r - 1, $c_frame_maxsq);
                }
    
                if (array_key_exists("$ix:$iy", $maxsq_top))
                {
                    imageline($png, $r * $jx, $r * $jy, $r * $jx + $r - 1, $r * $jy, $c_frame_maxsq);
                    imageline($png, $r * $jx, $r * $jy + 1, $r * $jx + $r - 1, $r * $jy + 1, $c_frame_maxsq);
                }
                $jy++;      
            }
            $jx++;
        }
    }

}

imagepng($png);
