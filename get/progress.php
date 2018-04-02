<?php
require 'algorithm.php';
require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

// User stats
$stmt = $db->prepare('SELECT `sid`, `unit`, CAST(SUM(1) AS UNSIGNED) AS `total`, CAST(SUM(`new`) AS UNSIGNED) AS `new`,
    CAST(SUM(`weight` > 0) AS UNSIGNED) AS `pass`, CAST(SUM(`weight` < 0) AS UNSIGNED) AS `fail` FROM (
    SELECT `sid`, `unit`, (`yes` IS NULL) AS `new`,
        COALESCE(' .$algo . ', 0) AS `weight` FROM (
            SELECT `user`.`id`, `sid`, `unit`, `yes`, `skip`, `no`, `time` FROM `words`
            LEFT JOIN `user` ON `words`.`id` = `user`.`id`
        ) AS `res`
    ) AS `res` GROUP BY `sid`, `unit` ORDER BY `sid`, LOWER(`unit`)');
if ($stmt->execute() !== true)
    die($stmt->error);
$stats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
echo json_encode($stats);
?>
