<?php
include '../common/access_data.php';

include '../config.php';

$dbstring=$pwdconfiguration['web_inventory_dbstring'];

$maintenance_states=array('Working','Broken','Problems','Notice');

?>