<?php
# refresh list of visited squares for given id

$cookie = "vvexp_id";

$id = -1;
if (isset($_COOKIE[$cookie])) { $id = intval($_COOKIE[$cookie]); }
# allow overwrite in request
if (isset($_POST[$cookie])) { $id = intval($_POST[$cookie]); }
if (isset($_GET[$cookie])) { $id = intval($_GET[$cookie]); }

if ($id <= -1)
{
    header("Location: index.php");
    exit(0);
}

$debug = false;
if (isset($_GET['dbg_activity']))
{
    $dbg_activity = $_GET['dbg_activity'];
    $debug = true;
    print "<pre>\n";
}

# force using cache:
$usecache = 0;
if (isset($_REQUEST['usecache']))
{
    $usecache = 1;
}


$output = "";

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

function dist_lat_lon($lat1, $lon1, $lat2, $lon2)
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

if (!$usecache)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://veloviewer.com/athlete/$id/activities",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("cache-control: no-cache"),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    file_put_contents("cache/vv1-$id.html", $response);
}
else
{
    $response = file_get_contents("cache/vv1-$id.html");
}

if (preg_match("/Not authenticated/", $response))
{
    print("ERROR: not authenticated (activities)");
    exit(1);
}

if (preg_match("/This athlete's activities are not available/", $response))
{
    print("ERROR: Activities are not available -- You need to enable <b>Share my data with anyone</b> and <b>Show my details in the VeloViewer leaderboard</b> in VeloViewer Options.");
    exit(1);
}


if (!preg_match('/"(https:..s3.veloviewer.com.athletes[^"]+maps.\d+.js)"/', $response, $m))
{
    print("ERROR: can't find explorer url (maps)");
    exit(1);
}
$urlmap = $m[1];

if (preg_match('/"(https:..s3.veloviewer.com.athletes[^"]+explorer.\d+.js)"/', $response, $m))
{
    $urlexp = $m[1];
}
else
{
//    print("ERROR: can't find explorer url (activities)");
//    exit(1);

    $urlexp = '';
}

if (!$usecache)
{
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlmap,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("cache-control: no-cache"),
        CURLOPT_ACCEPT_ENCODING => ""
    ));

    $response1 = curl_exec($curl);
    $err = curl_error($curl);
    file_put_contents("cache/vv2-$id.html", $response1);

    if ($urlexp)
    {
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlexp,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("cache-control: no-cache"),
            CURLOPT_ACCEPT_ENCODING => ""
        ));

        $response2 = curl_exec($curl);
        $err = curl_error($curl);
        file_put_contents("cache/vv3-$id.html", $response2);
    }

    curl_close($curl);

}
else
{
    $response1 = file_get_contents("cache/vv2-$id.html");
    $response2 = file_get_contents("cache/vv3-$id.html");
}

$response = preg_replace('/mapsLoaded.(.*)./', '$1', $response1);
$j1 = json_decode($response, true);

if ($urlexp)
{
    $response = preg_replace('/explorerLoaded.(.*)./', '$1', $response2);
    $j2 = json_decode($response, true);

    # parse explorer paths (so called "definite" set)
    foreach ($j2 as $n => $v)
    {
        $definite_check[$n] = 1; # to skip it during full path check

        if ($debug && isset($dbg_activity))
        {
            if ($n != $dbg_activity)
            {
                continue;
            }
        }

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
        }
    }
}

# parse map paths
$zoom = 14;
foreach ($j1 as $n => $v)
{
    if (array_key_exists($n, $definite_check))
    {
        continue;
    }

    $v = $v['m'];
    $v = preg_replace('/},{/', '}{', $v); # https://github.com/marcin-gryszkalis/veloviewer-explorer-overlay/issues/1

    if ($debug && isset($dbg_activity))
    {
        if ($n != $dbg_activity)
        {
            continue;
        }

        print_r($v);
    }

    $prevxtile = 0;
    $prevytile = 0;

    $parr = decode_polyline($v);

    $xstack = array();
    $ystack = array();

    $i = 0;
    $x = 0;
    $y = 0;
    foreach ($parr as $p)
    {
        if ($i%2 == 0)
        {
            $x = $p/100000;
            array_push($xstack, $x);
        }
        else
        {
            $y = $p/100000;
            array_push($ystack, $y);

            if ($debug)
            {
                print "A ($x,$y)\n";
            }
        }
        $i++;
    }

    while (!empty($xstack))
    {
        $x = array_shift($xstack);
        $y = array_shift($ystack);

        if ($debug)
        {
            print "($x,$y)\n";
        }

        $xtile = floor((($y + 180) / 360) * pow(2, $zoom));
        $ytile = floor((1 - log(tan(deg2rad($x)) + 1 / cos(deg2rad($x))) / pi()) /2 * pow(2, $zoom));

        $tiles["$xtile:$ytile"] = 1;

        if ($prevxtile == 0 || $prevytile == 0 || (abs($prevxtile - $xtile) + abs($prevytile - $ytile)) <= 1) # first tile or adjacent tile
        {
            $prevx = $x;
            $prevy = $y;
            $prevxtile = $xtile;
            $prevytile = $ytile;

            continue;
        }

        array_unshift($xstack, $x);
        array_unshift($ystack, $y);

        if ($debug)
        {
            print "U1 ($x,$y)\n";
        }


        $x = $prevx + ($x - $prevx) / 2; # half way between prevx and x
        $y = $prevy + ($y - $prevy) / 2;

        array_unshift($xstack, $x);
        array_unshift($ystack, $y);
        if ($debug)
        {
            print "U2 ($x,$y)\n";
        }

   }
}

if ($debug)
{
    print_r($tiles);
    $id="9999999999"; # debug id 10x9
}

file_put_contents("cache/$id.php", '<?php $exp = '.var_export($tiles, true).";\n");

$output .= '$stats_tiles = '.count($tiles).";\n";

# calculate max square
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

$output .= '$stats_maxsquare = '.$max.";\n";

foreach (array_keys($maxes) as $m)
{
    if ($maxes[$m] == $max)
    {
        # print "max square: $max x $max -- $m<br>\n";

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

file_put_contents("cache/maxsq-top-$id.php", '<?php $maxsq_top = '.var_export($maxsq_top, true).";\n");
file_put_contents("cache/maxsq-left-$id.php", '<?php $maxsq_left = '.var_export($maxsq_left, true).";\n");
file_put_contents("cache/stats-$id.php", "<?php\n$output\n");

header("Location: index.php");
