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
function getSID($sid)
{
    $stmt = $GLOBALS['db']->prepare('SELECT id FROM info WHERE id = ?');
    $stmt->bind_param('i', $sid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['id'];
}

function getWords($sid, $unit)
{
    $stmt = $GLOBALS['db']->prepare('SELECT CAST(' . $sid . ' AS UNSIGNED) AS sid, id, unit, word, info FROM `w_' . $sid . '` WHERE unit = ?');
    $stmt->bind_param('s', $unit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$words = array();
foreach ($secs as $sec) {
    $sid = getSID($sec['id']);
    if ($sid == null)
        continue;
    foreach ($sec['units'] as $index => $unit) {
        $uwords = getWords($sid, $unit['unit']);
        if ($uwords != null)
            $words = array_merge($words, $uwords);
    }
}

// Randomise words
shuffle($words);

// Output
echo json_encode($words);
?>
