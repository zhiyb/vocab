<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die();

$sid = $json['sid'];
$name = $json['name'];
$style = $json['style'];
$weight = $json['weight'];
if ($sid == null || $name == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$stmt = $db->prepare('UPDATE `info` SET `name` = ?, `style` = ?, `weight` = ? WHERE `sid` = ?');
$stmt->bind_param('sssi', $name, $style, $weight, $sid);
if ($stmt->execute() === true)
    echo "OK";
?>
