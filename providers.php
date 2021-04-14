<?php
$cookie = "vvexp_id";

$id = -1;
if (isset($_COOKIE[$cookie])) { $id = intval($_COOKIE[$cookie]); }

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
