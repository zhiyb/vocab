<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die('POST only');

$uid = $_GET['uid'];
if ($uid == null)
    die('UID required');

$secs = json_decode(file_get_contents("php://input"), true);
if ($secs == null)
    die('POST data not found');

require 'algorithm.php';
require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

// Create temporary table for section & unit pairs
if ($db->query('CREATE TEMPORARY TABLE IF NOT EXISTS `sel` (
        `sid` INT UNSIGNED NOT NULL,
        `unit` VARCHAR(32) NOT NULL,
        PRIMARY KEY (`sid`, `unit`)
    ) CHARACTER SET = utf8 COLLATE utf8_bin') !== true)
    die($db->error);

// Insert section & unit pairs
$stmt = $GLOBALS['db']->prepare('INSERT INTO `sel` (`sid`, `unit`) VALUES (?, ?)');
$words = array();
foreach ($secs as $sec) {
    foreach ($sec['units'] as $unit) {
        $stmt->bind_param('is', $sec['sid'], $unit['unit']);
        $stmt->execute();
    }
}

// Enumerate words
$stmt = $db->prepare('SELECT `words`.`id`, `sid`, `unit`, `word`, `info`, '
    . $algo .  ' AS `weight` FROM (SELECT * FROM `user` WHERE `uid` = UNHEX(?)) AS `user` RIGHT JOIN (
        SELECT `id`, `words`.`sid`, `words`.`unit`, `word`, `info` FROM `words`
        RIGHT JOIN `sel` ON `words`.`sid` = `sel`.`sid` AND `words`.`unit` = `sel`.`unit`
    ) AS `words` ON `user`.`id` = `words`.`id` ORDER BY `weight`, RAND()');
if ($stmt == false)
    die($db->error);
$stmt->bind_param('s', $uid);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
?>
