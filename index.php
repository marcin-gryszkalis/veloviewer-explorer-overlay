<?
$cookie = "vvexp_id";

$id = -1;
if ($_COOKIE[$cookie]) { $id = $_COOKIE[$cookie] }
if ($_REQUEST[$cookie]) { $id = $_REQUEST[$cookie]; }

$cookieopts = array (
                'expires' => time() + 60*60*24*365, // 1 year
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
                );

if ($id != -1) { setcookie($cookie, $id, $cookieopts); }

?><!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>VeloViewer Explorer Generic Overlay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Fira+Sans:300,400,500,700,300italic,400italic,500italic,700italic'>
  <link rel='stylesheet' href='res/basic.css'>
  <link rel='stylesheet' href='res/data-buttons.css'>
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900,200italic,300italic,400italic,600italic,700italic,900italic'>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Source+Code+Pro:300,400,500,600,700,900'>
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'>
  <link rel="stylesheet" href="res/style.css"><!-- based on Tommy Hodgins RFI Style https://codepen.io/tomhodgins/pen/QyvmXX -->
  <link rel="stylesheet" href="res/icons.css">

</head>
<body>
<main>
  <h1>VeloViewer Explorer</h1>
  <h2>Generic Overlay</h2>
  <h5 style=text-align:center>...</h5>

<h1>BRouter</h1>
<a href="https://brouter.de/brouter-web"></a>
<h1>LocusMap</h1>
<a href="https://www.locusmap.app"></a>

<img src="res/brouter-ex.png">
<img src="res/locus6.png">
  <p><strong>Lorem ipsum dolor sit amet,</strong> consectetur adipisicing elit, <em>sed do eiusmod tempor incididunt</em> ut labore et dolore magna aliqua. <u>Ut enim ad minim veniam</u>, quis nostrud exercitation ullamco <a href=#>laboris nisi ut aliquip</a> ex ea commodo consequat. Duis aute irure dolor in reprehenderit in <code>voluptate</code> velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

  <blockquote>
    <p><q>This is a blockquote</q></p>
    <footer>First Last</footer>
  </blockquote>

  <h2>List Test</h2>
  <ul>
    <li>list item
    <li>list item with <a href=#>a link</a>
    <li>list item
  </ul>
  <ol>
    <li>list item
    <li>list item with <a href=#>a link</a>
    <li>list item
  </ol>
  <h4>Button Test</h4>
  <a href=# data-button>default button</a>
  <a href=# data-button="blue">.blue button</a>
  <a href=# data-button="green">.green button</a>
  <a href=# data-button="red">.red button</a>
  <a href=# data-button="grey">.grey button</a>
  <a href=# data-button="outline">.outline button</a>
  <br>
  <a href=# data-button disabled>default button</a>
  <a href=# data-button="blue" class="disabled">.blue button</a>
  <a href=# data-button="green" hidden>.green button</a>
  <a href=# data-button="red" disabled>.red button</a>
  <a href=# data-button="grey" class="disabled">.grey button</a>
  <a href=# data-button="outline" hidden>.outline button</a>
  <pre>&lt;script>alert('I LOVE ALERTS!')&lt;/script></pre>
  <h1>This is an &lt;H1&gt; Headline</h1>
  <hr>
  <h2>This is an &lt;H2&gt; Headline</h2>
  <h3>This is an &lt;H3&gt; Headline</h3>
  <h4>This is an &lt;H4&gt; Headline</h4>
  <h5>This is an &lt;H5&gt; Headline</h5>
  <h6>This is an &lt;H6&gt; Headline</h6>
  <p><img class=float-left style=width:100px src=//>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
  <p><img class=float-right style=width:100px src=//staticresource.com/user.png>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
  <p><img class=float-none src=//staticresource.com/user.png></p>
  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
  <footer>
    <span class="material-icons md-18 md-dark">source</span>
    <a href="https://github.com/marcin-gryszkalis/veloviewer-explorer-overlay">Github</a> |
    <span class="material-icons md-18 md-dark">email</span>
    <a href="mailto:mg@fork.pl">mg@fork.pl</a>
  </footer>

</main>

<script src='res/EQCSS.min.js'></script>
<script src="res/script.js"></script>

</body>
</html>
