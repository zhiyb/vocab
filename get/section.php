<?php
$sid = $_GET["sid"];
if ($sid == NULL)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

$stmt = $db->prepare('SELECT * FROM info WHERE sid = ?');
$stmt->bind_param('i', $sid);
if ($stmt->execute() !== true)
    die($stmt->error);
$res = $stmt->get_result()->fetch_assoc();
echo json_encode($res);
?>
