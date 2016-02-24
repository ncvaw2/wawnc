<?php


$person = get_table("table_person")->getobj($key);

$has_links = get_table("exlinks")->has_links($key, null);
$has_votes = get_table("vote_data")->check_for_voting_record($key);
$has_survey_2014 = get_table("survey_data")->check($key);
$has_survey_2016 = get_table("table_survey")->check($key);


$page_title = $person->titlename;
if($person->photo_url_local)
    $fb_image = $fb_domain . $person->photo_url_local;
$gender_him = 'him';
$gender_his = 'his';
$votecolor = "#c00000";
$call_to_action = "";
$fb_share=false;
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

if($has_survey_2016)
{
    $fb_share=true;
    $fb_description = "Responses to Animal Welfare Survey ";

}
else
{
    $fb_share=true;
    $fb_description = "Receives a grade of \"" . $person->grade . "\"  on animal welfare issues. ";
    if ($person->gradecomment) {
        $fb_description .= $person->gradecomment;
    } else {
        $fb_description .= "Grade based on voting record.";
    }
}


include $header;
add_init_js ( "tabinit();" );
//add_init_js("tabselect('tab_survey16');");

?>

<div class="text_wrap"><?php $person->printPage(); ?></div>
<h2 style="color:<?php echo($votecolor); ?>"><?php echo($call_to_action);
    $shareurl = urlencode($fb_domain . '/bio/' . $key);    ?>

</h2>


<div id='tablist'>
		<span>
		<?php
        if ($has_links)
            echo("<a class='tab' id='tab_news_top' onclick=\"tabclick('tab_news')\">In the News</a>");

        if ($has_votes)
            echo("<a class='tab'id='tab_votes_top'  onclick=\"tabclick('tab_votes')\">Voting Record</a>");
        if ($has_survey_2014)
            echo("<a class='tab'  id='tab_survey14_top' onclick=\"tabclick('tab_survey14')\">Survey 2014</a>");
        if ($has_survey_2016)
            echo("<a class='tab'  id='tab_survey16_top' onclick=\"tabclick('tab_survey16')\">Survey 2016</a>");
        ?>
        </span>
    <?php
    if ($has_votes) {
        echo("
			<div class='tabbody' id='tab_votes'>
				<H3>Bills Sponsored</H3>
				<table class='votes'>");

        $person->print_list_sponsorship();
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
        $person->print_list_votes();
        echo("</table></div>");
    }

    if ($has_survey_2014) {
        echo("<div class='tabbody' style='display: none' id='tab_survey14'>");
        get_table("survey_data")->printresp($key);
        echo("</div>");
    }
    if ($has_survey_2016) {
        echo("<div class='tabbody' style='display: none' id='tab_survey16'>");
        get_table("table_survey")->printresp($key);
        echo("</div>");
    }
    if ($has_links) {
        echo("<div class='tabbody' style=\"display: none\" id='tab_news'>");
        get_table("exlinks")->print_list($key, null);
        echo("</div>");
    }

    ?>
</div>


