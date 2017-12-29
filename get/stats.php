<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die();

$id = $json['id'];
if ($id == '')
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$stmt = $db->prepare('SELECT * FROM `user` WHERE id = ?');
$stmt->bind_param('i', $id);
if ($stmt->execute() !== true)
    die();

echo json_encode($stmt->get_result()->fetch_assoc());
?>
