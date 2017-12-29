<?php
$_algo = "(CAST(`yes` AS SIGNED) - CAST(`no` AS SIGNED) * 2 - CAST(`skip` AS SIGNED))";
$algo = "(IF(`yes` + `no` = 0, CAST(`skip` AS SIGNED) * -32, $_algo))";
$stat = "(IF(`yes` + `no` = 0, 0, $_algo))";
?>
