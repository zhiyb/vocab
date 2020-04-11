<?php
$sid = $_GET["sid"];
if ($sid == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

$stmt = $db->prepare('SELECT DISTINCT `unit` FROM `words` WHERE `sid` = ? ORDER BY LOWER(`unit`)');
if ($stmt == false)
    die($db->error);
$stmt->bind_param('i', $sid);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_NUM));
?>
