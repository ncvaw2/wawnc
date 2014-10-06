<?php
$page_title="Candidate";


include $root.'/inc/db.php';
$key=getParam( "key");
$candidate=null;
if ($key)
{
	$candidate=get_table("candidates")->get_candiate($key);
    $page_title=$candidate->displayname;
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