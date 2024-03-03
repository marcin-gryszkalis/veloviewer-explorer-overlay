<?

$lat = $_REQUEST['lat'];
$lon = $_REQUEST['lon'];
$side = $_REQUEST['side'];

$zoom = 14;
$n = pow(2, $zoom);

// Lon./lat. to tile numbers
$xtile = floor((($lon + 180) / 360) * pow(2, $zoom));
$ytile = floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) /2 * pow(2, $zoom));

// Tile numbers to lon./lat.
$lon1 = $xtile / $n * 360.0 - 180.0;
$lat1 = rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n))));

$xtile += $side;
$ytile += $side;

// Tile numbers to lon./lat.
$lon2 = $xtile / $n * 360.0 - 180.0;
$lat2 = rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n))));

$fn = "square-$side.gpx";
header("Content-Type: application/gpx+xml");
header("Content-Disposition: attachment; filename=\"$fn\"");

?><?='<?xml version="1.0" encoding="UTF-8"?>'?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1" creator="MyGPSFiles" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <trk>
    <name>Square <?="$lat,$lon ($side x $side)" ?></name>
    <desc></desc>
    <trkseg>
      <trkpt lat="<?=$lat1 ?>" lon="<?=$lon1 ?>"/>
      <trkpt lat="<?=$lat2 ?>" lon="<?=$lon1 ?>"/>
      <trkpt lat="<?=$lat2 ?>" lon="<?=$lon2 ?>"/>
      <trkpt lat="<?=$lat1 ?>" lon="<?=$lon2 ?>"/>
      <trkpt lat="<?=$lat1 ?>" lon="<?=$lon1 ?>"/>
    </trkseg>
  </trk>
</gpx>
