<?php
require '../dbconf.php';
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
    sid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name TINYTEXT NOT NULL,
    style TEXT, weight TEXT)
    CHARACTER SET = utf8 COLLATE utf8_bin") !== TRUE)
    die("Error creating table vocab->info: " . $db->error . "\n");

if ($db->query("CREATE TABLE IF NOT EXISTS `words` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    `sid` INT UNSIGNED NOT NULL,
    `unit` TINYTEXT NOT NULL,
    `word` TEXT NOT NULL,
    `info` TEXT,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`sid`) REFERENCES `info`(`sid`) ON DELETE CASCADE ON UPDATE CASCADE
    ) CHARACTER SET = utf8 COLLATE utf8_bin") !== TRUE)
    die("Error creating table vocab->info: " . $db->error . "\n");

if ($db->query("CREATE TABLE IF NOT EXISTS `user` (
    `id` INT UNSIGNED NOT NULL,
    `yes` INT UNSIGNED NOT NULL DEFAULT 0,
    `skip` INT UNSIGNED NOT NULL DEFAULT 0,
    `no` INT UNSIGNED NOT NULL DEFAULT 0,
    `time` TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id`) REFERENCES `words`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) CHARACTER SET = utf8 COLLATE utf8_bin") !== TRUE)
    die("Error creating table vocab->user: " . $db->error . "\n");
?>
