<?php
require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$stmt = $db->prepare('SELECT `sid`, `unit`, SUM(`sum` > 0) AS `pass`, SUM(`sum` < 0) AS `fail` FROM ( SELECT `sid`, `unit`, ( CAST(`yes` AS SIGNED) - CAST(`no` AS SIGNED) ) AS `sum` FROM ( SELECT `user`.`id`, `sid`, `unit`, `yes`, `skip`, `no`, `time` FROM `words` RIGHT JOIN `user` ON `words`.`id` = `user`.`id` ) AS `res` ) AS `res` GROUP BY `sid`, `unit` ORDER BY `sid`, LOWER(`unit`)');
if ($stmt->execute() !== true)
    die($stmt->error);

echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
?>
