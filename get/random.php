<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

$secs = json_decode(file_get_contents("php://input"), true);
if ($secs == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . "vocab");
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

// Enumerate words
function getWords($sid, $unit)
{
    $stmt = $GLOBALS['db']->prepare('SELECT * FROM `words` WHERE `sid` = ? AND `unit` = ?');
    $stmt->bind_param('is', $sid, $unit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$words = array();
foreach ($secs as $sec) {
    foreach ($sec['units'] as $index => $unit) {
        $uwords = getWords($sec['id'], $unit['unit']);
        if ($uwords != null)
            $words = array_merge($words, $uwords);
    }
}

// Randomise words
shuffle($words);

// Output
echo json_encode($words);
?>
