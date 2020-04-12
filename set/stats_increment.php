<?php
$uid = $_GET['uid'];
$id = $_GET['id'];
$field = $_GET['field'];

if ($uid == null || $id == null) {
    http_response_code(400);
    die();
}

if ($field != 'yes' && $field != 'skip' && $field != 'no') {
    http_response_code(400);
    die();
}

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error) {
    http_response_code(500);
    die("Connection failed: " . $db->connect_error . "\n");
}
$db->set_charset('utf8mb4');

// Create user session if not exists
$stmt = $db->prepare("INSERT IGNORE INTO `session` (`uid`) VALUES (UNHEX(?))");
$stmt->bind_param('s', $uid);
if ($stmt->execute() !== true) {
    http_response_code(500);
    die($stmt->error);
}

$stmt = $db->prepare("INSERT INTO `user` (`uid`, `id`, `$field`) VALUES (UNHEX(?), ?, 1) ON DUPLICATE KEY UPDATE `$field` = `$field` + 1");
$stmt->bind_param('si', $uid, $id);
if ($stmt->execute() !== true) {
    http_response_code(500);
    die($stmt->error);
}

$stmt = $db->prepare('SELECT * FROM `user` WHERE `uid` = UNHEX(?) AND `id` = ?');
$stmt->bind_param('si', $uid, $id);
if ($stmt->execute() !== true) {
    http_response_code(500);
    die($stmt->error);
}

$res = $stmt->get_result()->fetch_assoc();
// json_encode does not support BINARY uid field
unset($res['uid']);
echo json_encode($res);
?>
