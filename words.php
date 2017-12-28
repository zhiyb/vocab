<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Section and unit selection</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link rel="stylesheet" href="css/src/theme.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/0.8.3/jquery.csv.min.js" integrity="sha256-xKWJpqP3ZjhipWOyzFuNmG2Zkp1cW4nhUREGBztcSXs=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/4.0.0/autosize.min.js" integrity="sha256-F7Bbc+3hGv34D+obsHHsSm3ZKRBudWR7e2H0fS0beok=" crossorigin="anonymous"></script>
<script src="js/global.min.js"></script>
</head>

<body>
<?php
// Connect database
require 'dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');
?>

<div class="container theme-showcase" role="main">

<div id="sections">
<ul class="list-group">
<li class="list-group-item list-group-item-primary">Section list</li>
<?php
// Enumerate sections and units
$secs = $GLOBALS['db']->query("SELECT id, name FROM info ORDER BY LOWER(name)")->fetch_all(MYSQLI_ASSOC);
foreach ($secs as $index => $sec) {
    $units = $GLOBALS['db']->query('SELECT DISTINCT unit FROM `w_' . $sec['id'] . '` ORDER BY LOWER(unit)')->fetch_all(MYSQLI_ASSOC);
    echo '<li class="list-group-item" id="' . $sec['id'] . '"><script>document.write(disp("' . $sec['name'] . '"));</script>';
    echo '<div data-toggle="buttons">';
    foreach ($units as $unit) {
        echo '<label class="btn btn-primary"><input id="' . $unit['unit'] . '" type="checkbox" autocomplete="off"><script>document.write(disp("' . $unit['unit'] . '"));</script></label>&nbsp;';
    }
    echo '</div></li>';
    $secs[$index]['units'] = $units;
}
?>
</ul><p><p>
<button id="submit" class="btn btn-success btn-lg btn-block">Start!</button>
</div>

<div id="words" style="display:none"><ul class="list-group"></ul></div>
<span id="log"></span>

<script src="js/src/cards.js"></script>
</div>

</body>
</html>
