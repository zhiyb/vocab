<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die();

$id = $json['id'];
$field = $json['field'];
if ($id == '')
    die();
if ($field != 'yes' && $field != 'skip' && $field != 'no')
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$stmt = $db->prepare("INSERT INTO `user` (`id`, `$field`) VALUES (?, 1) ON DUPLICATE KEY UPDATE `$field` = `$field` + 1");
$stmt->bind_param('i', $id);
if ($stmt->execute() !== true)
    die();

$stmt = $db->prepare('SELECT * FROM `user` WHERE id = ?');
$stmt->bind_param('i', $id);
if ($stmt->execute() !== true)
    die();

echo json_encode($stmt->get_result()->fetch_assoc());
?>
