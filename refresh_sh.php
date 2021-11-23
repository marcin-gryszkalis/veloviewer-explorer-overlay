<?php
# refresh list of visited squares for given id

include("lib.php");

$cookie = "shexp_id";

$id = "";
if (isset($_COOKIE[$cookie])) { $id = $_COOKIE[$cookie]; }
# allow overwrite in request
if (isset($_POST[$cookie])) { $id = $_POST[$cookie]; }
if (isset($_GET[$cookie])) { $id = $_GET[$cookie]; }
$id = preg_replace("/[^a-f0-9]+/", "", $id);
$id = substr($id, 0, 32);

if (! preg_match("/^[0-9a-f]{32}$/", $id))
{
    header("Location: index.php");
    exit(0);
}


$aid = sh_key2alias($id);

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

if (!$usecache)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://statshunters.com/api/$id/tiles",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("cache-control: no-cache"),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    file_put_contents("cache/sh1-$id.html", $response);
}
else
{
    $response = file_get_contents("cache/sh1-$id.html");
}

if (preg_match("/Not authenticated/", $response)) // need other
{
    print("ERROR: not authenticated (activities)");
    exit(1);
}


$j1 = json_decode($response, true);
$tiles1 = $j1['tiles'];
foreach ($tiles1 as $e)
{
    $x = $e['x'];
    $y = $e['y'];
    $tiles["$x:$y"] = 1;
}

if ($debug)
{
    print_r($tiles);
    $id="9999999999"; # debug id 10x9
}

file_put_contents("cache/$aid.php", '<?php $exp = '.var_export($tiles, true).";\n");

$output .= '$stats_tiles = '.count($tiles).";\n";

# calculate max square
# it's already there in json:
#   "square" : {
#      "x1" : 9058,
#      "x2" : 9096,
#      "y1" : 5414,
#      "y2" : 5452
#   },

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

file_put_contents("cache/maxsq-top-$aid.php", '<?php $maxsq_top = '.var_export($maxsq_top, true).";\n");
file_put_contents("cache/maxsq-left-$aid.php", '<?php $maxsq_left = '.var_export($maxsq_left, true).";\n");
file_put_contents("cache/stats-$aid.php", "<?php\n$output\n");

header("Location: index.php");
