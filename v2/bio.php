<?php
include $header;
include $root.'/obj/person.php';



$key=getParam( "key");
$person=null;
if ($key)
{
	$person=get_table("table_person")->getobj($key);
	$person->printPage();
}
else
{
	get_table("table_person")->printtable();
	
	

}



 include $footer; ?>