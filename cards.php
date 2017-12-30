<?php
require 'get/algorithm.php';
require 'dbconf.php';

// Connect database
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Section and unit selection</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link rel="stylesheet" href="css/theme.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/0.8.3/jquery.csv.min.js" integrity="sha256-xKWJpqP3ZjhipWOyzFuNmG2Zkp1cW4nhUREGBztcSXs=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/4.0.0/autosize.min.js" integrity="sha256-F7Bbc+3hGv34D+obsHHsSm3ZKRBudWR7e2H0fS0beok=" crossorigin="anonymous"></script>
<script src="js/global.min.js"></script>
</head>

<body>
<div class="container theme-showcase" role="main">

<div id="sections">
<ul class="list-group">
<li class="list-group-item list-group-item-primary">
<div class="row justify-content-between">
<div class="col-auto">Section list</div>
<div class="col-auto">
<button type="button" id="clean" class="btn btn-sm btn-danger"><span class="fa fa-eraser" /></button>
<a class="btn btn-sm btn-secondary" href="./" target="_blank"><span class="fa fa-list-ul" /></a></button>
</div>
</div>
</li>
<?php
// User stats
$stmt = $db->prepare('SELECT `sid`, `unit`,
    SUM(`sum` > 0) AS `pass`, SUM(`sum` < 0) AS `fail` FROM (
        SELECT `sid`, `unit`, ' . $stat . ' AS `sum` FROM (
            SELECT `user`.`id`, `sid`, `unit`, `yes`, `skip`, `no`, `time` FROM `words`
            RIGHT JOIN `user` ON `words`.`id` = `user`.`id`
        ) AS `res`
    ) AS `res` GROUP BY `sid`, `unit` ORDER BY `sid`, LOWER(`unit`)');
if ($stmt->execute() !== true)
    die($stmt->error);
$stats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Enumerate sections and units
$secs = $GLOBALS['db']->query("SELECT * FROM `info` ORDER BY LOWER(`name`)")->fetch_all(MYSQLI_ASSOC);
foreach ($secs as $index => $sec) {
    $units = $GLOBALS['db']->query('SELECT `unit`, COUNT(*) AS `cnt` FROM `words` WHERE `sid` = ' . $sec['sid'] . ' GROUP BY `unit` ORDER BY LOWER(`unit`)')->fetch_all(MYSQLI_ASSOC);
    echo '<li class="list-group-item" sid="' . $sec['sid'] . '"><script>document.write(disp("' . $sec['name'] . '"));</script>';
    echo '<div class="row justify-content-start align-items-end">';
    foreach ($units as $unit) {
        $pass = 0.0;
        $fail = 0.0;
        foreach ($stats as $i => $stat)
            if ($stat['sid'] == $sec['sid'] && $stat['unit'] == $unit['unit']) {
                $pass = (float)$stat['pass'] * 100 / (float)$unit['cnt'];
                $fail = (float)$stat['fail'] * 100 / (float)$unit['cnt'];
                unset($stats[$i]);
                break;
            }
        echo '<div class="col-auto"><div data-toggle="buttons" class="progress-btn"><div class="progress"><div class="progress-bar bg-success" role="progressbar" style="width: ' . $pass . '%"></div><div class="progress-bar bg-danger" role="progressbar" style="width: ' . $fail . '%"></div></div><label class="btn btn-sm btn-outline-warning btn-static text-dark"><script>document.write(disp("' . $unit['unit'] . '"));</script><input unit="' . $unit['unit'] . '" type="checkbox" autocomplete="off"></label></div></div>';
    }
    echo '</div></li>';
    $secs[$index]['units'] = $units;
}
?>
</ul><p><p>
<script>
var sections = <?php
$sections = [];
foreach ($secs as $sec)
    $sections[$sec['sid']] = $sec;
echo json_encode($sections);
?>;
</script>
<button id="submit" class="btn btn-primary btn-lg btn-block">Start!</button>
</div>

<div id="card" style="display:none">
<div class="progress" style="height: 24px;"><div class="progress-bar progress-bar-striped progress-bar-animated bg-info text-dark" role="progressbar" style="width: 100%">1536/2048</div></div><p>
<ul class="list-group word_list"></ul><p><p>
<div class="row" id="buttons">
<div class="col-sm"><button class="btn btn-primary btn-lg btn-block">Show</button></div>
<div class="col-sm"><button class="btn btn-success btn-lg btn-block">Yes</button></div>
<div class="col-sm"><button class="btn btn-warning btn-lg btn-block">Skip</button></div>
<div class="col-sm"><button class="btn btn-danger btn-lg btn-block">No</button></div>
<div class="col-sm"><button class="btn btn-secondary btn-lg btn-block">Back</button></div>
</div>
</div>
<div id="words" style="display:none"><ul class="list-group"></ul></div>

<!-- Word copying modal dialog -->
<div id="word_copy" class="modal fade" role="dialog">
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title"></h4>
      <button type="button" class="close" data-dismiss="modal"><span class="fa fa-times"></span></button>
    </div>
    <div class="modal-body">
      <div class="copy_field">With annotations:
        <textarea class="form-control" type="text" placeholder="With annotation" rows=1></textarea><p></div>
      <div class="copy_field">Annotations:
        <textarea class="form-control" type="text" placeholder="Annotations" rows=1></textarea><p></div>
      <div class="copy_field">Word:
        <textarea class="form-control" type="text" placeholder="Word" rows=1></textarea><p></div>
      <div class="copy_field">HTML:
        <textarea class="form-control" type="text" placeholder="HTML" rows=1></textarea><p></div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
</div>

</div>
<script src="js/copy.min.js"></script>
<script src="js/cards.min.js"></script>
<script src="js/words.min.js"></script>
<script src="js/actions.min.js"></script>
</body>
</html>
