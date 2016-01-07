<?php
$person = get_table ( "table_person" )->getobj ( $key );
$has_links = get_table ( "exlinks" )->has_links ( $key, null );

$vote=get_table("vote_data")->get_vote($key,'15HB405');

$page_title=$person->fullname;
$fb_image=$fb_domain. $person->photo_url_local;
$gender_him='him';
$gender_his='his';
$votecolor="#c00000";
$call_to_action="";
/*
if($person->gender=='f')
{
	$gender_him='her';
	$gender_his='her';
}
if($vote == 'Aye')
{
	$page_title="Tell ".$person->fullname." to change ". $gender_his . " vote!";
	$call_to_action=$person->fullname ." voted for the HB405 Ag-Gag Bill. Call ". $person->phone ." and email ".
	 $person->email . " and tell ".$gender_him ." to change ".$gender_his ." vote, and uphold the Governers veto!";
}
if($vote == 'No')
{
	$votecolor="#008000";
	$page_title="Tell ".$person->fullname." to keep fighting!";
	$call_to_action=$person->fullname ." voted against the HB405 Ag-Gag Bill. Call ". $person->phone ." and email ".
	$person->email . " to thank ".$gender_him .", and to tell " . $gender_him . " to keep fighting to prevent an override of the Governers veto.";
}<a href='https://www.facebook.com/sharer/sharer.php?u=<?php echo($shareurl);?>' target='_blank'><img style='display:inline;width:80px;' src='/img/fb-share-button.png'/></a>
*/
$fb_description="Receives a grade of \"" . $person->grade ."\"  on animal welfare issues. ";

if( $person->gradecomment)
{
	$fb_description .= $person->gradecomment;
}
else
{
	$fb_description .="Grade based on voting record, responsiveness to inquiries, and feedback from constituents";
}
	
include $header;    
//add_init_js ( "tabinit();" );
add_init_js ( "tabselect('tab_votes');" );
	
?>

<div class="text_wrap" ><?php $person->printPage();?></div>
<h2 style="color:<?php  echo($votecolor);?>"><?php echo($call_to_action);
$shareurl=urlencode($fb_domain.'/bio/'.$key);

?>  

</h2>


	<div id='tablist'>
		<span>
		<?php 
		if ($has_links)
				echo ("<a class='tab' id='tab_news_top' onclick=\"tabselect('tab_news')\">In the News</a>"); 		

		if ($person->office)
				echo ("<a class='tab'id='tab_votes_top'  onclick=\"tabselect('tab_votes')\">Voting Record</a>"); 		
				
	if (get_table ( "survey_data" )->check ( $key ))
		echo ("<a class='tab'  id='tab_survey_top' onclick=\"tabselect('tab_survey')\">Survey Responses</a>");
			 ?>

		</span>
		<?php
		if ($person->office)
		{
			echo("
			<div class='tabbody' id='tab_votes'>
				<H3>Bills Sponsored</H3>
				<table class='votes'>");
				
					$person->office->print_list_sponsorship();
				echo("	
				</table>

				<H3>Voting Record</H3>
				<table class='votes'>
					<thead>
						<tr>
							<th>Vote</th>
							<th>Bill</th>
						</tr>
					</thead>");
						$person->office->print_list_votes();
				echo("
				</table>
			</div>");
			}?>
		<div class='tabbody' style="display: none" id='tab_survey'>
			<?php 
				get_table("survey_data")->printresp($key);
				?>
		</div>
		<?php
		if ($has_links)
		{
		echo("<div class='tabbody' style=\"display: none\" id='tab_news'>");
				get_table ( "exlinks" )->print_list ( $key, null ); 	
		echo("</div>");
		}

		?>
	</div>


