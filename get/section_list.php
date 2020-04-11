<?php
require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

$res = $db->query("SELECT sid, name FROM info ORDER BY LOWER(name)")->fetch_all(MYSQLI_ASSOC);
echo json_encode($res);
?>
