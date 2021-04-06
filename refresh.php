<?
$usecache = 1;
print("refreshing...<hr>\n");

# refresh list of visited squares for given id

    function decode_polyline($string)
    {
        $precision = 0;

        $points = array();
        $index = $i = 0;
        $previous = array(0,0);
        while ($i < strlen($string)) 
        {
            $shift = $result = 0x00;
            do 
            {
                $bit = ord(substr($string, $i++)) - 63;
                $result |= ($bit & 0x1f) << $shift;
                $shift += 5;
            } while ($bit >= 0x20);

            $diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
            $number = $previous[$index % 2] + $diff;
            $previous[$index % 2] = $number;
            $index++;
            $points[] = $number * 1 / pow(10, $precision);
        }
        return $points;
    }

function distLatLon($lat1, $lon1, $lat2, $lon2) 
{
    $R = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a =
        sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $d = $R * $c;
    return $d;
}

if (!$_GET['id']) { exit(0); }
$id = intval($_GET['id']);
if ($id == 0) { exit(0); }

if (!$usecache)
{
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://veloviewer.com/athlete/$id/activities",
#  CURLOPT_URL => "https://veloviewer.com/athlete/548887/activities?o=0:1&f=0:1300656660000|1597685377000,1:All,5:0|380508,6:0|5132&c=0,0,5,6,9",
  CURLOPT_RETURNTRANSFER => true,
  # CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    $f = fopen("cache/vv1-$id.php", "w");
    fwrite($f, $response);
    fclose($f);

}
else
{
    $response = file_get_contents("cache/vv1-$id.php");
}


if (preg_match("/Not authenticated/", $response))
{
    print("ERROR: not authenticated (activities)");
    exit(1);    
}

# https://s3.veloviewer.com/athletes2/7/8/8845/3727c41fa319494f52631ac8f34aea33maps.1597759735.js?callback=mapsLoaded
if (!preg_match('/"(https:..s3.veloviewer.com.athletes[^"]+maps.\d+.js)"/', $response, $m))
{
    print("ERROR: can't find explorer url (maps)");
    exit(1);    
}

$urlmap = $m[1];

if (!preg_match('/"(https:..s3.veloviewer.com.athletes[^"]+explorer.\d+.js)"/', $response, $m))
{
    print("ERROR: can't find explorer url (activities)");
    exit(1);    
}

$urlexp = $m[1];

if (!$usecache)
{
curl_setopt_array($curl, array(
  CURLOPT_URL => $urlmap,
  CURLOPT_RETURNTRANSFER => true,
  # CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
  CURLOPT_ACCEPT_ENCODING => ""
));

$response1 = curl_exec($curl);
$err = curl_error($curl);

curl_setopt_array($curl, array(
  CURLOPT_URL => $urlexp,
  CURLOPT_RETURNTRANSFER => true,
  # CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
  CURLOPT_ACCEPT_ENCODING => ""
));

$response2 = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);


$f = fopen("cache/vv2-$id.php", "w");
fwrite($f, $response1);
fclose($f);

$f = fopen("cache/vv3-$id.php", "w");
fwrite($f, $response2);
fclose($f);
}
else
{
    $response1 = file_get_contents("cache/vv2-$id.php");
    $response2 = file_get_contents("cache/vv3-$id.php");
}

$response = preg_replace('/mapsLoaded.(.*)./','$1', $response1);
$j1 = json_decode($response, true);

$response = preg_replace('/explorerLoaded.(.*)./','$1', $response2);
$j2 = json_decode($response, true);

$ii = 0;
$pi = 0;
$x = 0;
$y = 0;

foreach ($j1 as $n => $v) 
{
    $prevxtile = 0;
    $prevytile = 0;


#$g = fopen("cache/mg.txt", "r");
#$v = fread($g, 10000000);
#$v = chop($v);
# print $v;
#    if ($n != 301059797) { continue; }

# print("$pi: tile count: ".count($tiles)."<br>");

    $v = $v['m'];
    $parr = decode_polyline($v);
    $i = 0;
    foreach ($parr as $p)
    {
        if ($i%2 == 0)
        {
            $x = $p;
        }
        else
        {
            $y = $p;

            $x /= 100000;
            $y /= 100000;
            $zoom = 14;
            $xtile = floor((($y + 180) / 360) * pow(2, $zoom));
            $ytile = floor((1 - log(tan(deg2rad($x)) + 1 / cos(deg2rad($x))) / pi()) /2 * pow(2, $zoom));


                    if (!array_key_exists("$xtile:$ytile", $tiles))
                    {
                        print("new: ($xtile:$ytile)<br>");
                        $tiles["$xtile:$ytile"] = 1;
                    }

#            fwrite($f, '"'."$p:");
#            fwrite($f, $p.'" => 1,'."\n");

            if ($prevxtile > 0 && $prevytile > 0 && (abs($prevxtile - $xtile) + abs($prevytile - $ytile)) > 1)
            {
                $dx = distLatLon($prevx,$y,$x,$y); # meters
                $dy = distLatLon($x,$prevy,$x,$y); 

                $dd = max($dx,$dy);

                $ddiv = $dd / 10; # every x meters on longer axis

                $sx = ($x - $prevx) / $ddiv;    
                $sy = ($y - $prevy) / $ddiv;    
               
    print("$n ($prevxtile,$prevytile) - ($xtile,$ytile), dist: $dd (sx=$sx, sy=$sy)<br>");

                $xx = $prevx;
                $yy = $prevy;
                $xycnt = 0;
                while (1)
                {
                    if ($n == 1682805185)
                    {
                        print("check: $xx,$yy<br>");
                    }

                    $xx += $sx;
                    $yy += $sy;
                    $xycnt++;
                    if ($xycnt > $ddiv)
                    {
                        break;
                    }

                    $xtile = floor((($yy + 180) / 360) * pow(2, $zoom));
                    $ytile = floor((1 - log(tan(deg2rad($xx)) + 1 / cos(deg2rad($xx))) / pi()) /2 * pow(2, $zoom));

                    if (!array_key_exists("$xtile:$ytile", $tiles))
                    {
                        print("   add: ($xtile:$ytile)<br>");
                        $tiles["$xtile:$ytile"] = 1;
                    }
                }
            }

            $prevx = $x;
            $prevy = $y;

            $prevxtile = $xtile;
            $prevytile = $ytile;
                    
        }
        $i++;
        $ii++;
    }
    $pi++;
}


$ii = 0;
$pi = 0;
foreach ($j2 as $n => $v) 
{
    $v = chop($v);
    $parr = decode_polyline($v);
    $i = 0;
    foreach ($parr as $p) 
    {   
        if ($i%2 == 0)
        {   
            $t = "$p:";
        }   
        else
        {   
            $t .= "$p";            
            $tiles[$t] = 1;
        }   
        $i++;
        $ii++;
    }   
    $pi++;
}


$f = fopen("cache/$id.php", "w");
fwrite($f, '<?php $exp = ['."\n");
foreach (array_keys($tiles) as $e)
{
    fwrite($f, '"'.$e.'" => 1,'."\n");
}
fwrite($f, '];'."\n");
fclose($f);

print("tile count: ".count($tiles)."<hr>");
print("<pre>");
# print_r($tiles);

# calculate max square
# include("cache/$id.php");
$max = 0;

foreach (array_keys($tiles) as $e)
{
    $a = explode(":", $e);
    $x = $a[0];
    $y = $a[1];

    $s = 1;
    $broken = false;
    foreach (range(1, 1000) as $s) # 1000x1000 square should be enough for everyone ;)
    {
        foreach (range(0, $s) as $ix)
        {
            foreach (range(0, $s) as $iy)
            {
                if (!array_key_exists(($x+$ix).":".($y+$iy), $tiles))
                {
                    $broken = true;
                    break 2;
                }
            }
        }

        if ($broken) # max is for previous $s, but we count from 0 
        {
            if ($s >= $max)
            {
                $maxes[$e] = $s;
                if ($s > $max)
                {
                    $max = $s;
                }
            }

            break;
        }
    }

}

foreach (array_keys($maxes) as $m)
{
    if ($maxes[$m] == $max)
    {
        print "max square: $max x $max -- $m<br>\n";

        $a = explode(":", $m);
        $x = $a[0];
        $y = $a[1];

        foreach (range(0, $max-1) as $i)
        {
            $maxsq_top[($x+$i).":".$y] = 1;
            $maxsq_left[$x.":".($y+$i)] = 1;
            $maxsq_top[($x+$i).":".($y+$max)] = 1;
            $maxsq_left[($x+$max).":".($y+$i)] = 1;
        }
    }
}

$f = fopen("cache/maxsq-top-$id.php", "w");
fwrite($f, '<?php $maxsq_top = '."\n");
fwrite($f, var_export($maxsq_top, true));
fwrite($f, ";\n");
fclose($f);

$f = fopen("cache/maxsq-left-$id.php", "w");
fwrite($f, '<?php $maxsq_left = '."\n");
fwrite($f, var_export($maxsq_left, true));
fwrite($f, ";\n");
fclose($f);


