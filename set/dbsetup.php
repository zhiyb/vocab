<?php
require '../dbconf.php';
$db = new mysqli($dbhost, $dbuser, $dbpw);
if ($db->connect_error)
    die("Connection failed: " . $db->connect_error . "\n");
$db->set_charset('utf8mb4');

function createDatabase($dbname) {
    $stat = $GLOBALS['db']->query("CREATE DATABASE IF NOT EXISTS " . $dbname . " CHARACTER SET = utf8mb4 COLLATE utf8mb4_unicode_ci");
    if ($stat === TRUE)
        echo "Database " . $dbname . " created\n";
    else
        echo "Error creating database " . $dbname . ": " . $GLOBALS['db']->error . "\n";
    return $stat;
}

if (createDatabase($dbname) !== TRUE)
    die();

$db->select_db($dbname);
if ($db->query("CREATE TABLE IF NOT EXISTS info (
    sid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name TINYTEXT NOT NULL,
    style TEXT, weight TEXT)
    CHARACTER SET = utf8mb4 COLLATE utf8mb4_unicode_ci") !== TRUE)
    die("Error creating table " . $dbname . "->info: " . $db->error . "\n");

if ($db->query("CREATE TABLE IF NOT EXISTS `words` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    `sid` INT UNSIGNED NOT NULL,
    `unit` VARCHAR(32) NOT NULL,
    `word` TEXT NOT NULL,
    `info` TEXT,
    PRIMARY KEY (`id`),
    UNIQUE (`sid`, `id`),
    FOREIGN KEY (`sid`) REFERENCES `info`(`sid`) ON DELETE CASCADE ON UPDATE CASCADE
    ) CHARACTER SET = utf8mb4 COLLATE utf8mb4_unicode_ci") !== TRUE)
    die("Error creating table " . $dbname . "->info: " . $db->error . "\n");

if ($db->query("CREATE TABLE IF NOT EXISTS `session` (
    `uid` BINARY(16) NOT NULL,
    `index` INT UNSIGNED NOT NULL DEFAULT 0,
    `data` TEXT NULL,
    `time` TIMESTAMP,
    PRIMARY KEY (`uid`)
    ) CHARACTER SET = utf8mb4 COLLATE utf8mb4_unicode_ci") !== TRUE)
    die("Error creating table " . $dbname . "->user: " . $db->error . "\n");

if ($db->query("CREATE TABLE IF NOT EXISTS `user` (
    `uid` BINARY(16) NOT NULL,
    `id` INT UNSIGNED NOT NULL,
    `yes` INT UNSIGNED NOT NULL DEFAULT 0,
    `skip` INT UNSIGNED NOT NULL DEFAULT 0,
    `no` INT UNSIGNED NOT NULL DEFAULT 0,
    `time` TIMESTAMP,
    PRIMARY KEY (`uid`, `id`),
    FOREIGN KEY (`uid`) REFERENCES `session`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`id`) REFERENCES `words`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) CHARACTER SET = utf8mb4 COLLATE utf8mb4_unicode_ci") !== TRUE)
    die("Error creating table " . $dbname . "->user: " . $db->error . "\n");
?>
