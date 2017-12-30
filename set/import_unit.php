<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$json = json_decode(file_get_contents("php://input"), true);
if ($json == null)
    die();

$sid = $json['sid'];
$unit = $json['unit'];
$words = $json['payload'];
if ($sid == '' || $words == '')
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
$stmt = $db->prepare('INSERT INTO `words` (`sid`, `unit`, `word`, `info`) VALUES (?, ?, ?, ?)');
foreach ($words as $e) {
    $word = $e['word'];
    unset($e['word']);
    $info = json_encode($e);
    $stmt->bind_param('isss', $sid, $unit, $word, $info);
    if ($stmt->execute() === true)
        $ret['cnt']++;
}
echo json_encode($ret);
?>
