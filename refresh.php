<?

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

    $id = $_GET['id'];

$id = 548887;

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
print "visited: $i";

exit();


