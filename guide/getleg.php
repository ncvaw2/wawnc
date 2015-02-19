<?php
// called via AJAX!! Not standalone
include $root.'/obj/person.php';
 


$chamber=0;
$district=0;
if (array_key_exists ( "dist", $_GET ))
	$district= $_GET ["dist"];
if (array_key_exists ( "ch", $_GET ))
	$chamber= $_GET ["ch"];
get_table('table_office')->get_leg_by_district($chamber,$district)->print_list_row();

?>



