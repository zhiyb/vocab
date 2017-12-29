<?php
$sid = $_GET['id'];
$unit = $_GET['unit'];
if ($sid == null || $unit == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$stmt = $db->prepare('SELECT * FROM `words` WHERE `sid` = ? AND `unit` = ?');
if ($stmt == false)
    die($db->error);
$stmt->bind_param('is', $sid, $unit);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
?>
