<?php
$id = $_GET["id"];
if ($id == null)
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
$res = $db->query('SELECT DISTINCT unit FROM `w_' . $id . '`')->fetch_all(MYSQLI_NUM);
echo json_encode($res);
?>
