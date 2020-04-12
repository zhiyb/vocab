<?php
$id = $_GET['id'];
$uid = $_GET['uid'];
if ($id == null || $uid == null) {
    http_response_code(400);
    die();
}

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

$stmt = $db->prepare('SELECT *, `words`.`id` AS `id` FROM (SELECT * FROM `words` WHERE `id` = ?) AS `words`
    LEFT JOIN `user` ON `user`.`uid` = UNHEX(?) AND `user`.`id` = ? AND `user`.`id` = `words`.`id`');
if ($stmt == false) {
    http_response_code(500);
    die($db->error);
}
$stmt->bind_param('isi', $id, $uid, $id);
if ($stmt->execute() !== true) {
    http_response_code(500);
    die($stmt->error);
}
$res = $stmt->get_result()->fetch_assoc();
// json_encode does not support BINARY uid field
unset($res['uid']);
echo json_encode($res);
?>
