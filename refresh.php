<?
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


if (!$_GET['id']) { exit(0); }
$id = intval($_GET['id']);
if ($id == 0) { exit(0); }

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://veloviewer.com/api/getExplorerTiles.php?id=$id&vvext=d0a48adf6ee24ebf36542d1ffe57e223",
  CURLOPT_RETURNTRANSFER => true,
  # CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if (preg_match("/Not authenticated/", $response))
{
    print("ERROR: not authenticated");
    exit(1);    
}

$f = fopen("cache/vv-$id.php", "w");
fwrite($f, $response);
fclose($f);

$response = preg_replace('/.*"t":\s*"([^"]+).*/','$1', $response);

$parr = decode_polyline($response);
$f = fopen("cache/$id.php", "w");
fwrite($f, '<?php $exp = ['."\n");
$i = 0;
foreach ($parr as $p)
{
    if ($i%2 == 0)
    {
        fwrite($f, '"'."$p:");
    }
    else
    {
        fwrite($f, $p.'" => 1,'."\n");
    }
    $i++;
}

fwrite($f, '];'."\n");
fclose($f);

$i /= 2;
print "visited: $i<br>\n";


# calculate max square
include("cache/$id.php");
$max = 0;

foreach (array_keys($exp) as $e)
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
                if (!array_key_exists(($x+$ix).":".($y+$iy), $exp))
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


