<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die();

$id = $json['id'];
$sid = $json['sid'];
if ($id == '' || $sid == '')
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

function getSID($sid) {
    $stmt = $GLOBALS['db']->prepare('SELECT id FROM `info` WHERE id = ?');
    $stmt->bind_param('i', $sid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()["id"];
}

$sid = getSID($sid);
if ($sid == null)
    die();

$stmt = $db->prepare('SELECT * FROM `user` WHERE id = ? AND sid = ?');
$stmt->bind_param('ii', $id, $sid);
if ($stmt->execute() !== true)
    die();

echo json_encode($stmt->get_result()->fetch_assoc());
?>
