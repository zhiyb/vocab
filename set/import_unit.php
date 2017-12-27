<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die();

$id = $json['id'];
$unit = $json['unit'];
$words = $json['payload'];
if ($id == '' || $words == '')
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

$ret['unit'] = $unit;
$ret['cnt'] = 0;
$stmt = $db->prepare('REPLACE INTO `w_' . $id . '` VALUES (0, ?, ?, ?)');
foreach ($words as $e) {
    $word = $e['word'];
    unset($e['word']);
    $info = json_encode($e);
    $stmt->bind_param('sss', $unit, $word, $info);
    if ($stmt->execute() === true)
        $ret['cnt']++;
}
echo json_encode($ret);
?>
