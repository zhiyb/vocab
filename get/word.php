<?php
$id = $_GET['id'];
if ($id == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

$stmt = $db->prepare('SELECT * FROM `words` WHERE `id` = ?');
if ($stmt == false)
    die($db->error);
$stmt->bind_param('i', $id);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_assoc());
?>
