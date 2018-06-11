<?php
if ($_SERVER["REQUEST_METHOD"] != "POST")
    die();

require '../dbconf.php';
echo md5($salt . trim(file_get_contents("php://input")));
?>
