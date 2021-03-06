<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST")
    die();

$id = $_POST["id"];
if ($id == null)
    die();

require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

$stmt = $db->prepare('DELETE FROM `words` WHERE id = ?');
$stmt->bind_param('i', $id);
if ($stmt->execute() !== TRUE)
    die($stmt->error);
?>
