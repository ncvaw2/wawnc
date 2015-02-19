<?php
$person = get_table ( "table_person" )->getobj ( $key );
$has_links = get_table ( "exlinks" )->has_links ( $key, null );

    
add_init_js ( "tabinit();" );
   
	
?>

<div class="text_wrap" ><?php $person->printPage();?></div>
	<div id='tablist'>
		<span>
		<?php 
		if ($has_links)
				echo ("<a class='tab' id='tab_news_top' onclick=\"tabselect('tab_news')\">In the News</a>"); 		

		if ($person->office)
				echo ("<a class='tab'id='tab_votes_top'  onclick=\"tabselect('tab_votes')\">Voting Record</a>"); 		
				?>
		 
		<a class='tab' id='tab_survey_top' onclick="tabselect('tab_survey')">Survey Responses</a>

		</span>
		<?php
		if ($person->office)
		{
			echo("
			<div class='tabbody' id='tab_votes'>
				<H3>Bills Sponsored</H3>
				<table class='votes'>
					<thead>
						<tr>
							<th>Vote</th>
							<th>Bill</th>
						</tr>
					</thead>");
				
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


