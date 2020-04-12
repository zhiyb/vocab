<?php
$uid = $_GET['uid'];

if ($uid == null) {
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

$stmt = $db->prepare("SELECT * FROM `session` WHERE `uid` = UNHEX(?)");
$stmt->bind_param('s', $uid);
if ($stmt->execute() !== true) {
    http_response_code(500);
    die($stmt->error);
}
$res = $stmt->get_result()->fetch_assoc();
// json_encode does not support BINARY uid field
unset($res['uid']);
echo json_encode($res);
?>
