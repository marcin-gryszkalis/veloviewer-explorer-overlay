<?php
include("lib.php");

$mode = "vv";
if (isset($_GET['mode']))
{
    $mode = $_GET['mode'];
}

$id = -1;
if ($mode == "vv")
{
    $cookie = "vvexp_id";
    if (isset($_COOKIE[$cookie])) { $id = intval($_COOKIE[$cookie]); }
}
else
{
    $cookie = "shexp_id";
    if (isset($_COOKIE[$cookie])) { $id = sh_key2alias($_COOKIE[$cookie]); }
}

if ($id > -1)
{
    $tmpl = file_get_contents("vvexplorer.providers.xml");
    $tmpl = preg_replace("/###ID###/", $id, $tmpl);
    $tmpl = preg_replace("/###DOMAIN###/", $_SERVER['HTTP_HOST'], $tmpl);
    header("Content-Type: text/xml");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"vvexplorer.$id.providers.xml\"");
    print($tmpl);
}
else
{
    header("Location: index.php");
}
