<?php
include_once  $root.'/obj/person.php';
include_once  $root.'/inc/db.php';
$key=getParam( "key");
$person=null;
include $header;



if ($key)
{
	$person=get_table("table_person")->getobj($key);
	$has_links= get_table("exlinks")->has_links($key,null);
	if($has_links)
	{
		add_init_js("tabselect('tab_news');");
	}
	
	
	
	?>
	
	<div class="text_wrap"><?php $person->printPage();?>

	
	
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
				<?php 
				//$leg->print_list_sponsorship(); 
				?>
			</table>
	
			<H3>Voting Record</H3>
			<table class='votes'>
				<thead><tr><th>Vote</th><th>Bill</th></tr></thead>
					<?php 
					//$leg->print_list_votes(); 
					?>
			</table>
		</div>
		<div class='tabbody' style="display: none" id='tab_survey'><?php $leg->print_survey();?></div>
		<div class='tabbody' style="display: none" id='tab_news'><?php
		get_table("exlinks")->print_list($legid,null);
		
			?></div>
	</div>
	</div>
		
	
	
	
	
	
	
	
	
	
	
	
	
	
<?php	
}
else
{
	get_table("table_person")->printtable();
}
 include $footer; ?>