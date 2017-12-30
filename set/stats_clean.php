<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$secs = json_decode(file_get_contents("php://input"), true);
if ($secs == null)
    die();

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
if ($db->query('DELETE `user` FROM `user` INNER JOIN (
        SELECT `id` FROM `words` RIGHT JOIN `sel`
        ON `words`.`sid` = `sel`.`sid` AND `words`.`unit` = `sel`.`unit`
    ) AS `del` ON `user`.`id` = `del`.`id`') !== true)
    die($db->error);
?>
