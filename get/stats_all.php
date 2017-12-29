<?php
require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$stmt = $db->prepare('SELECT `sid`, `unit`, SUM(`pass`) AS `pass`, SUM(`fail`) AS `fail` FROM ( SELECT `sid`, `unit`, (`type` = 1) AS `pass`, (`type` = -1) AS `fail` FROM ( SELECT `sid`, `unit`, LEAST( GREATEST( CAST(`yes` AS SIGNED) - CAST(`no` AS SIGNED), -1 ), 1 ) AS `type` FROM ( SELECT `user`.`id`, `sid`, `unit`, `yes`, `skip`, `no`, `time` FROM `words` RIGHT JOIN `user` ON `words`.`id` = `user`.`id` ) AS `res` HAVING (`type` <> 0) ) AS `res` ) AS `res` GROUP BY `sid`, `unit`');
if ($stmt->execute() !== true)
    die($stmt->error);

echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
?>
