<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die('POST only');

$uid = $_GET['uid'];
if ($uid == null)
    die('UID required');

$secs = json_decode(file_get_contents("php://input"), true);
if ($secs == null)
    die('POST data not found');

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

// Create temporary table for section & unit pairs
if ($db->query('CREATE TEMPORARY TABLE IF NOT EXISTS `sel` (
        `sid` INT UNSIGNED NOT NULL,
        `unit` VARCHAR(32) NOT NULL,
        PRIMARY KEY (`sid`, `unit`)
    ) CHARACTER SET = utf8mb4 COLLATE utf8mb4_unicode_ci') !== true)
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
$stmt = $db->prepare('SELECT `words`.`id` AS `id` FROM
    (SELECT * FROM `user` WHERE `uid` = UNHEX(?)) AS `user` RIGHT JOIN (
        SELECT `id`, `words`.`sid`, `words`.`unit` FROM `words`
        RIGHT JOIN `sel` ON `words`.`sid` = `sel`.`sid` AND `words`.`unit` = `sel`.`unit`
    ) AS `words` ON `user`.`id` = `words`.`id` ORDER BY LEAST(`skip`, 0), `weight`, RAND()');
$stmt->bind_param('s', $uid);
$stmt->execute();

$ids = [];
foreach ($stmt->get_result()->fetch_all(MYSQLI_NUM) as $entry)
    array_push($ids, $entry[0]);
echo json_encode($ids);
?>
