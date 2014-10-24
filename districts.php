<?php

$chamber=getParam("ch");




include $header;
include $root.'/obj/districts.php';

$distobj=get_table('districts')->print_list();


?>	
	
	


<?php include $footer; ?>
				
				