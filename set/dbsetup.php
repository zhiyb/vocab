<?php
require '../../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");

function createDatabase($dbname) {
    $stat = $GLOBALS['db']->query("CREATE DATABASE IF NOT EXISTS " . $dbname . " CHARACTER SET = utf8 COLLATE utf8_bin");
    if ($stat === TRUE)
        echo "Database " . $dbname . " created\n";
    else
        echo "Error creating database " . $dbname . ": " . $GLOBALS['db']->error . "\n";
    return $stat;
}

if (createDatabase($dbprefix . "vocab") !== TRUE)
    die();

$db->select_db($dbprefix . "vocab");
if ($db->query("CREATE TABLE IF NOT EXISTS info (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name TINYTEXT NOT NULL,
    style TEXT, weight TEXT)
    CHARACTER SET = utf8 COLLATE utf8_bin") !== TRUE)
    die("Error creating table vocab->info: " . $db->error . "\n");
?>
