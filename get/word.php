<?php
$sid = $_GET['sid'];
$id = $_GET['id'];
if ($sid == null || $id == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

function getSID($sid) {
    $stmt = $GLOBALS['db']->prepare('SELECT id FROM info WHERE id = ?');
    $stmt->bind_param('i', $sid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['id'];
}

$sid = getSID($sid);
if ($sid == null)
    die($db->error);
$stmt = $db->prepare('SELECT CAST(' . $sid . ' AS UNSIGNED) AS sid, id, unit, word, info FROM `w_' . $sid . '` WHERE id = ?');
if ($stmt == false)
    die($db->error);
$stmt->bind_param('i', $id);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_assoc());
?>
