<?php
$key=getParam('key');
$page_title=$key;

include $header;





include $root.'/obj/person.php';
 
$list=get_table("bill_list");

$key=getParam('key');



$bill=$list->get_bill ($key  );
$bill->print_page ();
$votes=get_table("vote_data");
$votes->print_bill_votes("Primary Sponsors", $bill->key,'psp',0); 
$votes->print_bill_votes( "Sponsors",$bill->key,'sp',0);
if($bill->svid)
{
	$votes->print_bill_votes("Senate Votes For", $bill->key,'Aye',$bill->svid);
	$votes->print_bill_votes("Senate Votes Against", $bill->key,'No',$bill->svid);
		
}
if($bill->hvid)
{
	$votes->print_bill_votes("House Votes For", $bill->key,'Aye',$bill->hvid);
	$votes->print_bill_votes("House Votes Against", $bill->key,'No',$bill->hvid);

}

get_table("exlinks")->print_list(null,$key);

?>



<?php include $footer; ?>