<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST")
    die();

$id = $_POST["id"];
$wid = $_POST["wid"];
if ($id == null || $wid == null)
    die();

require '../../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . 'vocab');
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

$stmt = $db->prepare('DELETE FROM `w_' . $id . '` WHERE id = ?');
$stmt->bind_param('i', $wid);
$stmt->execute();
?>