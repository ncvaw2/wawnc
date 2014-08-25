<?php

$chamber=getParam("ch");




include $header;
include $root.'/inc/db.php';

$distobj=get_table('districts')->print_list();


?>	
	
	


<?php include $footer; ?>
				
				