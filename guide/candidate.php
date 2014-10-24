<?php
$page_title="Candidate";


include $root.'/obj/dist.php';
$key=getParam( "key");
$candidate=null;
if ($key)
{
	$candidate=get_table('table_election')->get_candiate($key);
    $page_title=$candidate->nameonballot;
}


include $header;
echo("<div class='text_wrap'>");


if($candidate)
{
	$candidate->print_list_row();
	//$candidate->print_survey();	
	
}
else
	echo ("<h2>Candidate $key not found</h2>");
echo("</div>");

 include $footer; ?>