<?php
$id = $_GET['id'];
$wid = $_GET['wid'];
if ($id == null || $wid == null)
    die();

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
    die($db->error);
$stmt = $db->prepare('SELECT id, unit, word, info FROM `w_' . $id . '` WHERE id = ?');
if ($stmt == false)
    die($db->error);
$stmt->bind_param('i', $wid);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0]);
?>