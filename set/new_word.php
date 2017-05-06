<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die();

$id = $json['id'];
$unit = $json['unit'];
$word = $json['word'];
$info = $json['info'];
if ($id == '' || $word == '')
    die();
if ($unit == '')
    $unit = '(default)';

require '../../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

function getID($id) {
    $stmt = $GLOBALS['db']->prepare('SELECT id FROM info WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()["id"];
}

$id = getID($id);
if ($id == null)
    die();

$stmt = $db->prepare('INSERT INTO `w_' . $id . '` VALUES (NULL, ?, ?, ?)');
$stmt->bind_param('sss', $unit, $word, $info);
if ($stmt->execute() === true)
    echo "$unit";
?>
