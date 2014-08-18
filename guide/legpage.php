<?php

include $root.'/inc/db.php';
$legid=getParam( "id");

if (!$legid)
	echo '<h2>No id selected</h2>';
$leg=getobj("leg_list")->get_leg_by_key($legid);

if($leg)
	$page_title=$leg->name;

include $header;
$has_links= getobj("exlinks")->has_links($legid,null); 
if($has_links)
{
	add_init_js("tabselect('tab_news');");
}

?>
<div class="text_wrap"><?php $leg->print_list_row();?>



<div id='tablist'>
	<span>
	<?php if($has_links) 
		echo("<a class='tab' onclick=\"tabselect('tab_news')\">In the News</a>");	
    ?>
	<a class='tab' onclick="tabselect('tab_votes')">Voting Record</a>
	
	<a class='tab' onclick="tabselect('tab_survey')">Survey Responses</a>

	</span>
	<div class='tabbody' id='tab_votes'>
		<H3>Bills Sponsored</H3>
		<table class='votes'>
			<thead><tr><th>Vote</th><th>Bill</th></tr></thead>
			<?php $leg->print_list_sponsorship(); ?>
		</table>

		<H3>Voting Record</H3>
		<table class='votes'>
			<thead><tr><th>Vote</th><th>Bill</th></tr></thead>
				<?php $leg->print_list_votes(); ?>
		</table>
	</div>
	<div class='tabbody' style="display: none" id='tab_survey'><?php $leg->print_survey();?></div>
	<div class='tabbody' style="display: none" id='tab_news'><?php
	getobj("exlinks")->print_list($legid,null);
	
		?></div>
</div>
</div>


<?php include $footer; ?>