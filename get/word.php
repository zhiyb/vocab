<?php
$sid = $_GET['sid'];
$id = $_GET['id'];
if ($sid == null || $id == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$stmt = $db->prepare('SELECT * FROM `words` WHERE `id` = ? AND `sid` = ?');
if ($stmt == false)
    die($db->error);
$stmt->bind_param('ii', $id, $sid);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_assoc());
?>
