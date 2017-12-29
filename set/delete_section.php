<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST")
    die();

$sid = $_POST["id"];
if ($sid == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . 'vocab');
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$stmt = $db->prepare('DELETE FROM `info` WHERE `id` = ?');
$stmt->bind_param('i', $sid);
if ($stmt->execute() !== TRUE)
    die($stmt->error);

$stmt = $db->prepare('DELETE FROM `words` WHERE `sid` = ?');
$stmt->bind_param('i', $sid);
if ($stmt->execute() !== TRUE)
    die($stmt->error);
?>
