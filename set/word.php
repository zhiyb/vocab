<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die();

$sid = $json['sid'];
$id = $json['id'];
$unit = $json['unit'];
$word = $json['word'];
$info = $json['info'];
if ($sid == '' || $word == '')
    die();
if ($unit == '')
    $unit = '(default)';

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

$ret['unit'] = $unit;
$ret['cnt'] = 0;
$stmt = $db->prepare('INSERT INTO `words` (`id`, `sid`, `unit`, `word`, `info`) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `sid` = ?, `unit` = ?, `word` = ?, `info` = ?');
$stmt->bind_param('iisssisss', $id, $sid, $unit, $word, $info, $sid, $unit, $word, $info);
if ($stmt->execute() === true)
    $ret['cnt']++;
echo json_encode($ret);
?>
