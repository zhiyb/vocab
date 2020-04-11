<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST")
    die();

$sid = $_POST["sid"];
if ($sid == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

$stmt = $db->prepare('DELETE FROM `info` WHERE `sid` = ?');
$stmt->bind_param('i', $sid);
if ($stmt->execute() !== TRUE)
    die($stmt->error);
?>
