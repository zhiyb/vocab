<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die('POST only');

$uid = $_GET['uid'];
if ($uid == null)
    die('UID required');

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die('POST data not found');

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

$stmt = $db->prepare("INSERT INTO `user` (`uid`, `id`, `$field`) VALUES (UNHEX(?), ?, 1) ON DUPLICATE KEY UPDATE `$field` = `$field` + 1");
$stmt->bind_param('si', $uid, $id);
if ($stmt->execute() !== true)
    die($stmt->error);

$stmt = $db->prepare('SELECT * FROM `user` WHERE `uid` = ? AND `id` = ?');
$stmt->bind_param('si', $uid, $id);
if ($stmt->execute() !== true)
    die($stmt->error);

echo json_encode($stmt->get_result()->fetch_assoc());
?>
