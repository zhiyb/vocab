<?php
$uid = $_GET['uid'];
$id = $_GET['id'];
$skip = $_GET['skip'];
$weight = $_GET['weight'];

if ($uid == null || $id == null || !is_numeric($weight)) {
    http_response_code(400);
    die();
}

$skip = filter_var($skip, FILTER_VALIDATE_BOOLEAN);
$weight = (float)$weight;

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

if ($skip) {
    $stmt = $db->prepare("INSERT INTO `user` (`uid`, `id`, `weight`, `skip`) VALUES (UNHEX(?), ?, ?, -1) ON DUPLICATE KEY UPDATE
        `weight` = VALUES(`weight`), `skip` = CASE WHEN `skip` < 0 THEN `skip` - 1 ELSE `skip` + 1 END");
    $stmt->bind_param('sid', $uid, $id, $weight);
} else {
    $stmt = $db->prepare("INSERT INTO `user` (`uid`, `id`, `weight`, `skip`) VALUES (UNHEX(?), ?, ?, ABS(`skip`)) ON DUPLICATE KEY UPDATE
        `weight` = VALUES(`weight`), `skip` = ABS(`skip`)");
    $stmt->bind_param('sid', $uid, $id, $weight);
}
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
