<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST")
    die();

$name = $_POST["name"];
if ($name == null)
    die();

require '../../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbprefix . 'vocab');
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->query('SET CHARACTER SET utf8');

function getID($name) {
    $stmt = $GLOBALS['db']->prepare('SELECT id FROM info WHERE name = ?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()["id"];
}

// Check for existance
if (getID($name) !== null) {
    // Entry exists
    die("Error: Name " . $name . " exists");
} else {
    // Add entry
    $stmt = $db->prepare('INSERT INTO info (name) VALUES (?)');
    $stmt->bind_param('s', $name);
    if ($stmt->execute() !== TRUE)
        die("Error: " . $stmt->error);
    $id = getID($name);
    if ($id == null)
        die($db->error);
}

// Create related tables
if ($db->query('CREATE TABLE IF NOT EXISTS `w_' . $id . '` (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unit TINYTEXT NOT NULL, word TEXT NOT NULL, info TEXT)
    CHARACTER SET = utf8 COLLATE utf8_bin') !== true)
    die($db->error);
?>
