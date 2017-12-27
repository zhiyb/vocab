<?php
require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");

$stmt = $db->prepare('SELECT * FROM info WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0];
echo json_encode($res);
?>
