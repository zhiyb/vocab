<?php
$uid = $_GET['uid'];
if ($uid == null)
    die('UID required');

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

// User stats
$stmt = $db->prepare('SELECT `sid`, `unit`, CAST(SUM(1) AS UNSIGNED) AS `total`, CAST(SUM(`new`) AS UNSIGNED) AS `new`,
    CAST(SUM(`weight` >= 1) AS UNSIGNED) AS `pass`, CAST(SUM(`weight` <= -1) AS UNSIGNED) AS `fail` FROM (
        SELECT `sid`, `unit`, (`weight` IS NULL) AS `new`, `weight` FROM (
            SELECT `user`.`id`, `sid`, `unit`, `weight`, `time` FROM `words`
            LEFT JOIN (SELECT * FROM `user` WHERE `uid` = UNHEX(?)) AS `user` ON `words`.`id` = `user`.`id`
        ) AS `res`
    ) AS `res` GROUP BY `sid`, `unit` ORDER BY `sid`, LOWER(`unit`)');
if ($stmt == false)
    die($db->error);
$stmt->bind_param('s', $uid);
if ($stmt->execute() !== true)
    die($stmt->error);
$stats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
echo json_encode($stats);
?>
