<?php
$doc=getParam('doc');
$page_title=$doc;

include $header;





include $root.'/obj/person.php';
 
$list=get_table("bill_list");

$doc=getParam('doc');



$bill=$list->get_bill ($doc  );
$bill->print_page ();
$votes=get_table("vote_data");
$votes->print_bill_votes("Primary Sponsors", $bill->doc,'psp',0); 
$votes->print_bill_votes( "Sponsors",$bill->doc,'sp',0);
if($bill->svid)
{
	$votes->print_bill_votes("Senate Votes For", $bill->doc,'Aye',$bill->svid);
	$votes->print_bill_votes("Senate Votes Against", $bill->doc,'No',$bill->svid);
		
}
if($bill->hvid)
{
	$votes->print_bill_votes("House Votes For", $bill->doc,'Aye',$bill->hvid);
	$votes->print_bill_votes("House Votes Against", $bill->doc,'No',$bill->hvid);

}

get_table("exlinks")->print_list(null,$doc);

?>



<?php include $footer; ?>