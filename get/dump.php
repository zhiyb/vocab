<?php
require '../../dbconf.php';
passthru('mysqldump -u ' . $dbuser . ' -p"' . $dbpw . '" -h ' . $dbhost . ' ' . $dbprefix . 'vocab');
?>
