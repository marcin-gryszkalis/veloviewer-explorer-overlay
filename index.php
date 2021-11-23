<?
include("lib.php");

$cookie_vv = "vvexp_id";
$cookie_sh = "shexp_id";

$id_vv = "";
if (isset($_COOKIE[$cookie_vv])) { $id_vv = intval($_COOKIE[$cookie_vv]); }
if (isset($_POST[$cookie_vv])) { $id_vv = intval($_POST[$cookie_vv]); }

$id_sh = "";
if (isset($_COOKIE[$cookie_sh])) { $id_sh = $_COOKIE[$cookie_sh]; }
if (isset($_POST[$cookie_sh])) { $id_sh = $_POST[$cookie_sh]; }
$id_sh = preg_replace("/[^a-f0-9]+/g", "", $id_sh);
$id_sh = substr($id_sh, 0, 32);

$cookieopts = array (
    'expires' => time() + 60*60*24*365, // 1 year
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
    );

if ($id_vv != "") { setcookie($cookie_vv, $id_vv, $cookieopts); }
if ($id_sh != "") { setcookie($cookie_sh, $id_sh, $cookieopts); }

$shalias = "";
if (strlen($id_sh) == 32) // SH key
{
    $shalias = sh_key2alias($id_sh);
}

?><!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>VeloViewer Explorer Generic Overlay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="/res/favicon.png">

  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Fira+Sans:300,400,500,700,300italic,400italic,500italic,700italic'>
  <link rel='stylesheet' href='res/basic.css'>
  <link rel='stylesheet' href='res/data-buttons.css'>
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900,200italic,300italic,400italic,600italic,700italic,900italic'>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Source+Code+Pro:300,400,500,600,700,900'>
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'>
  <link rel="stylesheet" href="res/style.css"><!-- based on Tommy Hodgins RFI Style https://codepen.io/tomhodgins/pen/QyvmXX -->
  <link rel="stylesheet" href="res/icons.css">
  <link rel="stylesheet" href="res/lightbox.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<main>
  <h1>VeloViewer / StatHunters - Explorer</h1>
  <h2>Generic Overlay</h2>

<div class="xrow">
  <div class="xcol">
    <h3><a href="https://brouter.de/brouter-web">BRouter Web</a></h3>
    <a href="res/brouter-ex.png" data-lightbox="example-1" data-title="BRouter Web"><img style="height:300px" src="res/brouter-ex.png" alt="BRouter" /></a>
  </div>
  <div class="xcol">
    <h3><a href="https://www.locusmap.app">Locus Map</a></h3>
    <a href="res/locus-ex.png" data-lightbox="example-2" data-title="Locus Map 3"><img style="height:300px" src="res/locus-ex.png" alt="LocusMap"/></a>
  </div>
  <div class="xcol">
    <h3><a href="https://osmand.net">OsmAnd</a></h3>
    <a href="res/osmand-ex.png" data-lightbox="example-3" data-title="OsmAnd"><img style="height:300px" src="res/osmand-ex.png" alt="OsmAnd"/></a>
  </div>
  <div class="xcol">
    <h3><a href="https://nakarte.me">NaKarte.me</a></h3>
    <a href="res/nakarte-ex.png" data-lightbox="example-4" data-title="NaKarte"><img style="height:300px" src="res/nakarte-ex.png" alt="NaKarte"/></a>
  </div>

</div>

<hr>

<?
if ($id_vv != "" && file_exists("cache/$id_vv.php") || $id_sh != "" && file_exists("cache/$shalias.php"))
{
    ?>
<h3>Explorer Stats</h3>
    <?
}

if ($id_vv != "" && file_exists("cache/$id_vv.php"))
{
    ?>
<h4>VeloViewer</h4>
    <?
    $ft = filemtime("cache/$id_vv.php");
    $ago = human_time_diff(time(), $ft);

    include("cache/stats-$id_vv.php");
?>
    <p>Your data was refreshed <?=$ago ?> ago</p>
    <p>Visited Tiles: <?=$stats_tiles ?></p>
    <p>Max Square: <?=$stats_maxsquare ?>x<?=$stats_maxsquare ?></p>
<?
}

if ($id_sh != "" && file_exists("cache/$shalias.php"))
{
    ?>
<h4>StatHunters</h4>
    <?
    $ft = filemtime("cache/$shalias.php");
    $ago = human_time_diff(time(), $ft);

    include("cache/stats-$shalias.php");
?>
    <p>Your data was refreshed <?=$ago ?> ago</p>
    <p>Visited Tiles: <?=$stats_tiles ?></p>
    <p>Max Square: <?=$stats_maxsquare ?>x<?=$stats_maxsquare ?></p>
<?
}
?>

<hr>

<h3>Configure for <a href="https://veloviewer.com/">VeloViewer</a></h3>
<h4>VeloViewer ID</h4>
<p>You can find your VeloViewer ID in URL bar at <a href="https://veloviewer.com/">veloviewer.com</a> - it's the number behind /athlete/.</p>

<form action="index.php" method="post">
<input type="text" name="<?=$cookie_vv ?>" class="textinput" onfocus="if (this.value=='not set') { this.value='' }" value="<?=$id_vv != "" ? $id_vv : "not set" ?>">
<input type="submit" value="Save" data-button>
</form>

<a href="refresh.php" data-button>Refresh Explorer Stats</a>

<p>You need to enable <strong>Share my data with anyone</strong> and <strong>Show my details in the VeloViewer leaderboard</strong> in VeloViewer Options.</p>
<a href="res/vv-options.png" data-lightbox="vv-options" data-title="VV Options"><img style="height:200px" src="res/vv-options.png" alt="VV Options"/></a>

<p>Refreshing may take several seconds or even minutes.</p>

<h4>Overlay tile map URL</h4>
<p>You can use this overlay in any system that supports standard tile servers</p>
<?

if ($id_vv != "")
{
    $url_vv = "https://".$_SERVER['HTTP_HOST']."/".$id_vv."/{z}/{x}/{y}.png";
    ?>
    <p>VeloViewer: <code><?=$url ?></code></p>

    <?
}
else
{
    ?>
    You have to setup your VeloViewer ID first.
    <?
}
?>

<hr>

<h3>Configure for <a href="https://www.statshunters.com/">StatHunters</a></h3>
<h4>StatHunters API key</h4>
<p>You can find your API key in StatHunters Settings: <a href="https://www.statshunters.com/settings">https://www.statshunters.com/settings</a></p>
<a href="res/shsettings.png" data-lightbox="shs" data-title="StatHunters Settings"><img style="height:300px" src="res/shsettings.png" alt="SH" /></a>

<form action="index.php" method="post">
<input type="text" name="<?=$cookie_sh ?>" class="textinput" onfocus="if (this.value=='not set') { this.value='' }" value="<?=$id_sh != "" ? $alias : "not set" ?>">
<input type="submit" value="Save" data-button>
</form>

<a href="refresh.php" data-button>Refresh Explorer Stats</a>

<p>You need to enable <strong>Share my data with anyone</strong> and <strong>Show my details in the VeloViewer leaderboard</strong> in VeloViewer Options.</p>
<a href="res/vv-options.png" data-lightbox="vv-options" data-title="VV Options"><img style="height:200px" src="res/vv-options.png" alt="VV Options"/></a>

<p>Refreshing may take several seconds or even minutes.</p>

<h4>Overlay tile map URL</h4>
<p>You can use this overlay in any system that supports standard tile servers</p>
<?

if ($id_sh != "")
{
    $url_sh = "https://".$_SERVER['HTTP_HOST']."/".$shalias."/{z}/{x}/{y}.png";
    ?>
    <p>StatHunters<code><?=$url ?></code></p>

    <?
}
else
{
    ?>
    You have to setup your StatHunters API key.
    <?
}

if ($url_sh == "" && $url_vv == "")
{   
    $url = "*** setup VeloViewer ID or StatHunters API key first ***";
}
elseif ($url_sh != "" && $url_vv != "")
{
    $url = "<b>$url_vv</b> (VV) or <b>$url_sh</b> (SH)";
}
else
{
    $url = $url_vv.$url_sh;
}

?>

<hr>

<h3>Setup BRouter Web</h3>
  <p>BRouter Planner web interface: <a href="https://brouter.de/brouter-web">https://brouter.de/brouter-web</a></p>

  <h4>Step 1 - choose <strong>Custom Layers</strong></h4>
  <a href="res/brouter1.png" data-lightbox="br1" data-title="BRouter Web setup 1"><img style="height:300px" src="res/brouter1.png" alt="BRouter" /></a>

  <h4>Step 2 - Fill the <strong>Customize Layers form</strong></h4>
  <ol>
    <li>Name of the layer
    <li>Layer URL (with your VeloViewer ID) <code><?=$url ?></code>
    <li>Add overlay
  </ol>
  <a href="res/brouter2.png" data-lightbox="br2" data-title="BRouter Web setup 2"><img style="height:300px" src="res/brouter2.png" alt="BRouter" /></a>

  <h4>Step 3 - Enable the layer</h4>
  <a href="res/brouter3.png" data-lightbox="br3" data-title="BRouter Web setup 3"><img style="height:300px" src="res/brouter3.png" alt="BRouter" /></a>

<hr>
<h3>Setup NaKarte.me</h3>
  <p>NaKarte web interface: <a href="https://nakarte.me">https://nakarte.me</a></p>

  <h4>Step 1 - choose <strong>Add custom Layer</strong> (pencil icon)</h4>

  <h4>Step 2 - Fill the <strong>form</strong></h4>
  <ol>
    <li>Layer name
    <li>Tile URL template (with your VeloViewer ID) <code><?=$url ?></code>
    <li>Choose <strong>overlay</strong>
    <li>Add layer
  </ol>
  <a href="res/nakarte1.png" data-lightbox="nk1" data-title="NaKarte setup 1"><img style="height:300px" src="res/nakarte1.png" alt="NaKarte" /></a>

  <h4>Step 3 - Enable the layer</h4>
  <a href="res/nakarte2.png" data-lightbox="nk2" data-title="NaKarte setup 2"><img style="height:300px" src="res/nakarte2.png" alt="NaKarte" /></a>

<hr>
<h3>Setup Locus Map</h3>
  <p>LocusMap Application: <a href="https://www.locusmap.app">https://www.locusmap.app</a></p>

  <h4>Step 1. Install Locus Map</h4>
  Install Locus Map on your smartphone, note - the overlay was tested with LocusMap App version 3 (pro).

  <h4>Step 2. Get providers.xml</h4>
  <p><a href="providers.php">Click here to download your personal providers.xml file to be used in Locus Map</a></p>

  <h4>Step 3. Upload providers.xml to smartphone</h4>
  <p>It should be stored in <code>Internal Storage / Locus / mapsOnline / custom</code> directory.</p>
  <br>
  <a href="res/locus1.png" data-lightbox="lm1" data-title="Locus setup 1"><img style="height:300px" src="res/locus1.png" alt="LocusMap" /></a>

  <h4>Step 4. Verify it's recognized</h4>
  <p>In Map Manager it should be visible under the name of <em>VeloViewer Explorer Personal Overlay</em> in the <em>World</em> category</p>
  <a href="res/locus2.png" data-lightbox="lm2" data-title="Locus setup 2"><img style="height:300px" src="res/locus2.png" alt="LocusMap" /></a>

  <h4>Step 5. Map Overlays</h4>
  <p>Go to <em>More Functions</em> / <em>Map Overlays</em></p>
  <a href="res/locus3.png" data-lightbox="lm3" data-title="Locus setup 3"><img style="height:300px" src="res/locus3.png" alt="LocusMap" /></a>

  <h4>Step 6. Set Overlay</h4>
  <p>Enable Overlay, choose <em>Set</em>, <em>Select Online Map</em> and then select <em>VeloViewer Explorer Overlay</em></p>
  <a href="res/locus4.png" data-lightbox="lm4" data-title="Locus setup 4"><img style="height:300px" src="res/locus4.png" alt="LocusMap" /></a>
  <a href="res/locus5.png" data-lightbox="lm5" data-title="Locus setup 5"><img style="height:300px" src="res/locus5.png" alt="LocusMap" /></a>

  <h4>Step 7. Have fun</h4>
  <p>The overlay should be immediately visible</p>
  <a href="res/locus6.png" data-lightbox="lm6" data-title="Locus setup 6"><img style="height:300px" src="res/locus6.png" alt="LocusMap" /></a>
  <a href="res/locus7.png" data-lightbox="lm7" data-title="Locus setup 7"><img style="height:300px" src="res/locus7.png" alt="LocusMap" /></a>
  <a href="res/locus8.png" data-lightbox="lm8" data-title="Locus setup 8"><img style="height:300px" src="res/locus8.png" alt="LocusMap" /></a>
  <a href="res/locus9.png" data-lightbox="lm9" data-title="Locus setup 9"><img style="height:300px" src="res/locus9.png" alt="LocusMap" /></a>

  <h4>Step 8. Cache</h4>
  <p>You can clear Locus Map cache of Explorer Overlay in case you uploaded new tracks to Strava, updated VeloViewer and refreshed data here (the <em>Refresh Explorer stats</em> button)</p>
  <a href="res/locus0.png" data-lightbox="lm0" data-title="Locus setup 0"><img style="height:300px" src="res/locus0.png" alt="LocusMap" /></a>

<hr>
<h3>Setup OsmAnd</h3>
  <p>OsmAnd Application: <a href="https://osmand.net">https://osmand.net</a></p>

  <h4>Step 1. Install OsmAnd</h4>
  Install OsmAnd on your smartphone. Tested with OsmAnd+ 3.9.10 (Android) and 3.9.7 (iOS)

  <h4>Step 2. Enable <em>Online Maps plugin</em></h4>
  <a href="res/osmand01.png" data-lightbox="os1" data-title="OsmAnd setup 1"><img style="height:300px" src="res/osmand01.png" alt="OsmAnd" /></a>

  <h4>Step 3. Add new Map Source</h4>
  <p>In <em>Configure Map</em> menu theres <em>Map Source</em> - select it and then choose <em>Add</em></p>
  <br>
  <a href="res/osmand02.png" data-lightbox="br2" data-title="OsmAnd setup 2"><img style="height:300px" src="res/osmand02.png" alt="OsmAnd" /></a>
  <a href="res/osmand03.png" data-lightbox="br3" data-title="OsmAnd setup 3"><img style="height:300px" src="res/osmand03.png" alt="OsmAnd" /></a>

  <h4>Step 4. Setup Overlay - fill the form</h4>
  <ol>
    <li>Name of the source
    <li>Layer URL (with your VeloViewer ID) <code><?=$url ?></code>
    <li>Save
  </ol>
  <p><strong>Important: <em>iOS OsmAnd</em></strong> required different format of URL template ({z}/{x}/{y} replaced with {0}/{1}/{2}) but they fixed it in recent versions.</p>
  <a href="res/osmand04.png" data-lightbox="br4" data-title="OsmAnd setup 4"><img style="height:300px" src="res/osmand04.png" alt="OsmAnd" /></a>

  <h4>Step 5. Enable Overlay</h4>
  <p>Go to <em>Configure Map</em> / <em>Overlay Map</em></p>
  <a href="res/osmand05.png" data-lightbox="br5" data-title="OsmAnd setup 5"><img style="height:300px" src="res/osmand05.png" alt="OsmAnd" /></a>

  <h4>Step 6. Choose Overlay</h4>
  <p>Choose <em>VeloViewer Explorer</em> you added on Step 4. You can set <em>transparency</em> to the max, because Overlay is already served as transparent (you will be able to make it even less visible with <em>tranparency seekbar</em> (if you choose to enable it).</p>
  <a href="res/osmand06.png" data-lightbox="br6" data-title="OsmAnd setup 6"><img style="height:300px" src="res/osmand06.png" alt="OsmAnd" /></a>
  <a href="res/osmand07.png" data-lightbox="br7" data-title="OsmAnd setup 7"><img style="height:300px" src="res/osmand07.png" alt="OsmAnd" /></a>

  <h4>Step 7. Have fun</h4>
  <p>The overlay should be immediately visible</p>
  <a href="res/osmand08.png" data-lightbox="br8" data-title="OsmAnd setup 8"><img style="height:300px" src="res/osmand08.png" alt="OsmAnd" /></a>
  <a href="res/osmand09.png" data-lightbox="br9" data-title="OsmAnd setup 9"><img style="height:300px" src="res/osmand09.png" alt="OsmAnd" /></a>
  <a href="res/osmand10.png" data-lightbox="br10" data-title="OsmAnd setup 10"><img style="height:300px" src="res/osmand10.png" alt="OsmAnd" /></a>
  <p>iOS Version</p>
  <a href="res/osmand11.png" data-lightbox="br11" data-title="OsmAnd setup 11"><img style="height:300px" src="res/osmand11.png" alt="OsmAnd" /></a>

<hr>
<h3>Problems?</h3>
  <p>If you have any problems please report an issue at <a href="https://github.com/marcin-gryszkalis/veloviewer-explorer-overlay/issues">https://github.com/marcin-gryszkalis/veloviewer-explorer-overlay/issues</a> or just write to <span class="material-icons md-18 md-dark">email</span> <a href="mailto:mg@fork.pl">mg@fork.pl</a></p>

<!--
  <blockquote>
    <p><q>This is a blockquote</q></p>
    <footer>First Last</footer>
  </blockquote>
  <pre>&lt;script>alert('I LOVE ALERTS!')&lt;/script></pre>
  <p><img class=float-left style=width:100px src=//>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
  <p><img class=float-right style=width:100px src=//staticresource.com/user.png>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
  <p><img class=float-none src=//staticresource.com/user.png></p>
  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

-->
<hr>
<h3>Credits</h3>
  <p>This page uses:</p>
  <ul>
    <li>LightBox2 - <a href="https://github.com/lokesh/lightbox2">https://github.com/lokesh/lightbox2</a>
    <li>Tommy Hodgins RFI theme - <a href="https://codepen.io/tomhodgins/pen/QyvmXX">https://codepen.io/tomhodgins/pen/QyvmXX</a>
  </ul>

<footer>
  <span class="material-icons md-18 md-dark">source</span>
  <a href="https://github.com/marcin-gryszkalis/veloviewer-explorer-overlay">Github</a> |
  <span class="material-icons md-18 md-dark">email</span>
  <a href="mailto:mg@fork.pl">mg@fork.pl</a>
</footer>

</main>

<script src='res/EQCSS.min.js'></script>
<script src="res/script.js"></script>
<script src="res/lightbox.min.js"></script>

<a href="https://github.com/marcin-gryszkalis/veloviewer-explorer-overlay" class="github-corner" aria-label="View source on GitHub"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a>

</body>
</html>
