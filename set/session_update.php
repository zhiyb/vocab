<?php
$uid = $_GET['uid'];
$index = $_GET['index'];

if ($uid == null) {
    http_response_code(400);
    die();
}

$data = null;
$post = $_SERVER["REQUEST_METHOD"] == "POST";
if ($post)
    $data = file_get_contents("php://input");

if ($index == null && !$post) {
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

if ($index != null) {
    $stmt = $db->prepare("INSERT INTO `session` (`uid`, `index`) VALUES (UNHEX(?), ?) ON DUPLICATE KEY UPDATE `index` = VALUES(`index`)");
    $stmt->bind_param('si', $uid, $index);
    if ($stmt->execute() !== true) {
        http_response_code(500);
        die($stmt->error);
    }
}

if ($post) {
    $stmt = $db->prepare("INSERT INTO `session` (`uid`, `data`) VALUES (UNHEX(?), ?) ON DUPLICATE KEY UPDATE `data` = VALUES(`data`)");
    $stmt->bind_param('ss', $uid, $data);
    if ($stmt->execute() !== true) {
        http_response_code(500);
        die($stmt->error);
    }
}
?>
