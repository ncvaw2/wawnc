<?php
include $root . '/obj/person.php';
$legid = getParam ( "id" );

if (! $legid)
	echo '<h2>No id selected</h2>';
$leg = get_table ( "table_office" )->get_leg_by_key ( $legid );

if ($leg)
	$page_title = $leg->name;

include $header;
$has_links = get_table ( "exlinks" )->has_links ( $legid, null );

add_init_js ( "tabinit();" );
$ncleg_url = $leg->get_ncleg_link ();
?>
<div class="text_wrap"><?php $leg->print_list_row();?>

</div>

<div id='tablist'>
	<span>
	<?php
	
if ($has_links)
		echo ("<a class='tab' id='tab_news_top' onclick=\"tabselect('tab_news')\">In the News</a>");
	?>
    
    
    
	<a class='tab' id='tab_votes_top' onclick="tabselect('tab_votes')">Voting
			Record</a>
	<?php
	if (get_table ( "survey_data" )->check ( $legid ))
		echo ("<a class='tab'  id='tab_survey_top' onclick=\"tabselect('tab_survey')\">Survey Responses</a>");
	
	echo ("<a class='tab'  target='_blank' href='$ncleg_url'>Link to page on NCGA website</a>");
	
	?>

	</span>
	<div class='tabbody' id='tab_votes'>
		<H3>Bills Sponsored</H3>
		<table class='votes'>
			<thead>
				<tr>
					<th>Vote</th>
					<th>Bill</th>
				</tr>
			</thead>
			<?php $leg->print_list_sponsorship(); ?>
		</table>

		<H3>Voting Record</H3>
		<table class='votes'>
			<thead>
				<tr>
					<th>Vote</th>
					<th>Bill</th>
				</tr>
			</thead>
				<?php $leg->print_list_votes(); ?>
		</table>
	</div>
	<div class='tabbody' style="display: none" id='tab_survey'><?php $leg->print_survey();?></div>
	<div class='tabbody' style="display: none" id='tab_news'><?php
	get_table ( "exlinks" )->print_list ( $legid,null);
	
		?></div>
</div>



<?php include $footer; ?>