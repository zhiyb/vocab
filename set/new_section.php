<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST")
    die();

$name = $_POST["name"];
if ($name == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . 'vocab');
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

function getSID($name) {
    $stmt = $GLOBALS['db']->prepare('SELECT `id` FROM `info` WHERE `name` = ?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['id'];
}

// Check for existance
if (getSID($name) !== null) {
    // Entry exists
    die("Section $name exists");
} else {
    // Add entry
    $stmt = $db->prepare('INSERT INTO `info` (`name`) VALUES (?)');
    $stmt->bind_param('s', $name);
    if ($stmt->execute() !== TRUE)
        die($stmt->error);
}
?>
