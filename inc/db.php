<?php
/*
 * bill_list - tab 1- 
 * candidates - tab 2-
 * leg_list  - tab 3-
 * districts - tab 4
 * vote_data - tab 5-
 * links 	 - tab 6-
 * 
 * 
 * 
 */
include_once  $root.'/obj/tables.php';


class bill {
	public $nickname;
	public $stance;
	public $effect;
	public $official;
	public $svid;
	public $hvid;	
	public $desc;
	public $year;
	public $doc;
	public $docname;	
	public $picture;
	public function __construct($d,$index)
	{
		
		$this->nickname =getj($d,'nickname');
		$this->stance =getj($d,'stance');
		if($this->stance == 'pro')
		{
			$this->effect= "Supports animal welfare";
			$this->picture='bill_good.png';
		}
		else 
		{
			$this->picture='bill_bad.gif';
			$this->effect= 	"Harmful to animal welfare";
		}
		$this->official =getj($d,'official');
		$this->svid =getj($d,'svid');
		$this->hvid =getj($d,'hvid');		
		$this->desc =getj($d,'desc');
		$this->year =getj($d,'year');		
		$this->link =getj($d,'link');		
		$this->doc =getj($d,'doc');		
		$this->docname =getj($d,'name');		
	}
	public function get_stance()
	{
		if($this->stance == 'pro')
		{
			return "Supports animal welfare";
		}
		
		return 	"Harmful to animal welfare";
		
	
	}
	public function print_tr()
	{
        if($this->year == 'Year')
            return;
		echo ("<div><h3><a href='/guide/billpage.php?doc=$this->doc'>$this->year - $this->docname - $this->nickname <img style='display:inline;width:40px' src='/img/$this->picture' /></a></h3>	
			
			<div>$this->official </div>
		<div>$this->effect </div>
		<div><a target='_blank' href='http://www.ncleg.net/gascripts/BillLookUp/BillLookUp.pl?Session=$this->year&BillID=$this->doc&submitButton=Go' >Link to $this->doc on NCLEG.NET</a> </div>
		<div>$this->desc </div></div>");
	}
	public function print_page()	
	{
		echo "<H1>$this->year - $this->docname -  $this->nickname</H1>";
		echo "<H2>$this->official </H2>";
		
		
		echo "<p><img style='display:inline;width:60px' src='/img/$this->picture' /> $this->effect </p>";		
		echo "<p><a target='_blank' href='http://www.ncleg.net/gascripts/BillLookUp/BillLookUp.pl?Session=$this->year&BillID=$this->doc&submitButton=Go' >Link to $this->doc on NCLEG.NET</a> </p>";
		echo "<p>$this->desc </p>";
	}	
}

class bill_list extends table_base
{
	
	function create_from_spreadsheet()
	{
		$this->create1('data_v1',1,'bill','doc');
	}	
	public function get_bill($doc)
	{
		return $this->list [$doc];
	}
	public function print_bills()
	{
		foreach ( $this->list as $bill )
		{
			$bill->print_tr();
		}
	}	
}

class vote {
	public $vid;
	public $mkey;
	public $doc;
	public $vote;
	public $score;
	public $grade; /* -1 bad vote, 0 neutral, 1 good */
	public function __construct($d,$index)
	{
		$this->vid=getj($d,'vid');
		$this->mkey=getj($d,'key');		
		$this->doc= getj($d,'doc');
		$this->vote=getj($d,'vote');
		$this->grade=0;
	}
	public function get_score()
	{
		$bill=get_table("bill_list")->get_bill($this->doc);
		
		if($this->vid)
		{
			if(!(($this->vid==$bill->svid)||($this->vid==$bill->hvid)))
				return 0;
		}
		if(($this->vote=='Aye') xor($this->vote=='No'))
		{
			if(($this->vote=='Aye') xor ($bill->stance=='pro'))
				$this->grade=-1;
			else
				$this->grade=1;
			return $this->grade;
		}

		if(($this->vote=='psp') xor($this->vote=='sp'))
		{
			if($bill->stance=='anti')
				$this->grade=-1;
			else
				$this->grade=1;
			return $this->grade * 3;
		}
		return $this->grade;
	}

	public function print_vote_tr()
	{
		$vote=$this->vote;
		$doc=$this->doc;
		$bill=get_table("bill_list")->get_bill($doc);
		if($this->vid)
		{
			if(!(($this->vid==$bill->svid)||($this->vid==$bill->hvid)))
				return;
		}
		//echo "<img style='width:50px' src='/img/x.jpg'/>";

		echo "<tr>";
		//echo "<td>$vid</td>";

		$picture=0;
		$class="";
		$title="Voted ".($vote=='Aye'? "for" :"against")." bill that ". $bill->effect;
		if(($vote=='Aye') xor($vote=='No'))
		{
			if(($vote=='Aye') xor ($bill->stance=='pro'))
			{
				$class='votebad';
				$picture='bill_bad.gif';
			}
			else
			{
				$class='votegood';
				$picture='bill_good.png';
			}
		}

		if(($vote=='psp') xor($vote=='sp'))
		{
			$title='Bill ' . $bill->effect;
			if($bill->stance=='anti')
			{
				$class='votebad';
				$picture='bill_bad.gif';
			}
			else
			{
				$class='votegood';
				$picture='bill_good.png';
			}
		}
		if($vote=='psp')
			$vote='Primary Sponsor';
		if($vote=='sp')
			$vote='Sponsor';
		if($vote=='N/V')
			$vote='Did Not Vote';
		echo "<td><div class='$class'>$vote</div>";
		if($picture)
			echo "<img style='width:60px' title='$title' src='/img/$picture'/>";

		echo "</td>";
		echo "<td><div><a href='/guide/billpage.php?doc=$doc'>$doc - $bill->official</a></div>";
		echo "<div class='$class'>$title</div>";
		if($bill->desc)
			echo "<div>$bill->desc</div>";

		echo "</td></tr>";
	}
}

class vote_data extends table_base
{

	function create_from_spreadsheet()
	{
		$this->create1('data_v1',3,'vote');
	}	

	public function print_bill_votes($title,$doc,$vote,$vid) {

		$legs=array(); //local
		//echo "<table class='votes'>";
		$comma=0;
		$uid=$doc.$vote.$vid;
		$display='block';

		
		foreach ( $this->list as $v )
		{
			if($vid && ($vid!=$v->vid))
				continue;
			if(($doc == $v->doc)&&
					($vote==$v->vote)
			)
			{
				
				$leg=get_table("leg_list")->get_leg_by_key($v->mkey);
				if($leg)
					$legs[]=$leg;
			}
		}
		$count=count($legs);
		if($count>6)
			$display='none';
		echo("<h5>$title:<a onclick='togglehide(\"$uid\")'>($count)</a></h5><div id='$uid' style='display:$display'>");



		foreach ( $legs as $leg )
		{

			if($comma)
				echo (", ");
			echo "<a href='/guide/legpage.php?id=$leg->key'>$leg->name</a>";
			$comma=1;
		}
		echo("</div>");
	}
	public function get_votes($legid,&$count) {
		$score=0;
		$count=0;
        
		foreach ( $this->list as $v )
		{
			if($legid == $v->mkey)
			{

				$score+=$v->get_score();
				$count++;

			}
		}
		return $score;
	}
	public function print_list_votes($legid,$sponsors) {
		foreach ( $this->list as $v )
		{
			if($v->vid xor $sponsors)
				if($legid == $v->mkey)
				{
					$v->print_vote_tr();
				}
		}
	}
}
class election
{
	//columns
	public $key;
	public $year;
	public $type;
	public $district;
	public $chamber;
	public $party;
	public $nameonballot;
	public $endorsements;
	function printbio()
	{
		echo("<table><tr><td/><td/></tr>");


		echo ('</table>');
	}

	public function get_local_page_url() {	
		$url="/v2/bio.php?key=$this->key";
		$leg=get_table("leg_list")->get_leg_by_key($this->key);
		if($leg)
		{
			$url="/guide/legpage.php?id=$this->key";
			
		}
		return $url;
	}
}
class table_election  extends table_base
{
	function get_columns()
	{
		return ['key','year','type','district','chamber','party','party','nameonballot','endorsements'];
	}	
	function create_from_spreadsheet()
	{
		$this->create('data_v2','oi0q51k','election','key');
	}
	
	public function print_list() {
		global $g_debug;
		$biolist = get_table ( "table_person" );
		
		echo "<div class='tbl_leglist' >";
		foreach ( $this->list as $d )
		{
			$key=$d->key;
			$bio=	$biolist->getobj ( $key );
			if($bio)
				$bio->print_list_row ();
			else
			{
				if($g_debug)
					echo "<H1>Could not find $key</H1>";
			}
				
		}
		echo '</div>';
	}	
	
	function printtable()
	{
		$column="key";
		foreach ($this->list as $row )
		{
			$val=$row->$column;
			echo($val);
		}
	}
	public function sort() {
	
		$sort=getParam("sort");
		if($sort=='grade')
		{
			uasort($this->list, 'sort_func_grade');
		}
		
			else
		if($sort=='dist')
		{
		uasort($this->list, 'sort_func_dist');
		}
		
		else
			ksort ( $this->list );
	
	}
	public function getlist($ch,$num)
	{
		$set=array();
		foreach ( $this->list as $c )
		{
			if(
			($c->district==$num)&&
			($c->chamber==$ch))
			{
				$set [] =$c;
			}
		}
		return $set;
	}	
	public function print_people($ch,$num)
	{
	
		$set=$this->getlist($ch,$num);
		foreach ( $set as $x )
		{
			$leg=get_table("leg_list")->get_leg_by_key($x->key);
			if($leg)
			{
				$leg->print_list_row();
				
			}
			else
			{
				$person = get_table ( "table_person" )->getobj ( $x->key );
				$person->print_list_row();	
			}		
			
			
		}
	}
	public function print_endorse($ch,$num)
	{
	
		$set=$this->getlist($ch,$num);
		foreach ( $set as $x )
		{
			$leg=get_table("leg_list")->get_leg_by_key($x->key);
			if($leg)
			{
				$leg->print_short_bio();
				
			}
			else
			{
				$person = get_table ( "table_person" )->getobj ( $x->key );
				$person->print_short_bio();	
			}		
			
			
		}
	}	
	public function get_endorcements($ch,$num,$elect)
	{
		$link_string="";
		$set=$this->getlist($ch,$num,$elect);
		$legs=get_table("leg_list");
		foreach ( $set as $x )
		{
			$grade="";
			$leg=$legs->get_leg_by_key($x->key);
			if($leg)
			{
				$grade="<span style='font-weight:bold;color:" .$leg->grade_color . "'>" . $leg->grade . "</span>";
	
				$link_string.="<div>$x->party: <a href='/guide/legpage.php?id=$x->key'>$x->nameonballot $grade</a></div>";
	
			}
			else
				$link_string.="<div>$x->party: <a href='/v2/bio.php?key=$x->key'>$x->nameonballot $grade</a></div>";
	
				
		}
		if(count($set)==1)
		{
			$link_string.="<div>(uncontested)</div>";
				
		}
		return $link_string;
	}	
	public function get_candate_links($ch,$num,$elect)
		{
			$link_string="";
			$set=$this->getlist($ch,$num,$elect);
			$legs=get_table("leg_list");
			foreach ( $set as $x )
			{
				$grade="";
				$endorse="";
				$leg=$legs->get_leg_by_key($x->key);
				if($x->endorsements=='Y')
				{
					$endorse="<img class='endorsesmall' src='img/endorse_small.png'>";
					
				}
				if($leg)
				{
					$grade="<span style='font-weight:bold;color:" .$leg->grade_color . "'>" . $leg->grade . $endorse . "</span>";
				
					$link_string.="<div>$x->party: <a href='/guide/legpage.php?id=$x->key'>$x->nameonballot $grade</a></div>";
				
				}
				else
					$link_string.="<div>$x->party: <a href='/v2/bio.php?key=$x->key'>$x->nameonballot $grade </a>$endorse</div>";

			
			}
			if(count($set)==1)
			{
				$link_string.="<div>(uncontested)</div>";
			
			}
			return $link_string;
		}	

}
function get_grade(&$score,&$grade,&$color)
{
	$grades = [
	 [ -6,"F-","#F00"],
	 [ -3,"F","#F00"],
	 [ -2,"D-","#C04"],	 
	 [ -1,"D","#808"],
	 [ 0,"C","#00F"],
	 [ 1,"C+","#02E"],	 
	 [ 2,"B","#088"],	 	
	 [ 3,"B+","#0c8"],	  
	 [ 4,"A-","#0c0"],
	 [ 5,"A","#0c0"],	 
 	 [ 6,"A+","#0c0"],	 
	];	
	if(!$grade)
	{
		$grade="A+";
		$color="#0c0";
		foreach ( $grades as $g )
		{
			if($score <= $g[0])
			{
				$grade=$g[1];
				$color=$g[2];
				return;
			}
		}
		return;
	}
	foreach ( $grades as $g )
	{
		if($grade == $g[1])
		{
			$score=$g[0];
			$color=$g[2];
			return;
		}
	}	
}


class legislator{

	public $name;
	public $first;
	public $last;
	public $id;
	public $key;
	public $grade;
	public $score;
	public $phone;
	public $email;
	public $comment;
	public $grade_color;
	public $uid;
	public $party;
	public $chamberId;
	public $chamber;
	public $county;
    public $url;
	public $district;
	
	public function init() {
		
		
	
	}
	public function __construct($d,$index) {

		$this->party = getj($d,'party');
		$this->first = getj($d,'first');
		$this->phone = getj($d,'phone');
		$this->last = getj($d,'last');
		$this->key = getj($d,'key');
		$this->comment=getj($d,"comment");
		$this->email=getj($d,"email");
		$this->grade=getj($d,"grade");
		$this->note=getj($d,"note");
		
		$this->name = $this->first.' '.$this->last;
		$this->uid =getj($d,'uid');
		$this->id = getj($d,'id');
		$this->county = getj($d,'county');
		$this->chamberId = getj($d,'chamber');
        $this->district=getj($d,'district');
        $this->title=getj($d,'title') ;
		if($this->chamberId=='H')
			$this->chamber='House';
		else
			$this->chamber='Senate';
			
		$this->create_grade();
	}
	public function get_url() {
		return ("http://www.ncleg.net/gascripts/members/viewMember.pl?sChamber=$this->chamber&nUserID=$this->uid");
	}
	public function print_survey() {
		get_table("survey_data")->printresp($this->key);
	}
	public function print_list_votes()
	{
		get_table("vote_data")->print_list_votes($this->key,0);

	}
	public function print_list_sponsorship()
	{

		get_table("vote_data")->print_list_votes($this->key,1);

	}
	public function create_grade()
	{
		$count=0;
		$color="#000";

		$score=get_table("vote_data")->get_votes($this->key,$count);

		if( ($count==0) && (!$this->grade))
		{
			$this->grade='Not Graded';
			$this->comment='Has not been in office long enough to assign grade';
		}
		else
			get_grade($score,$this->grade,$color);


		$this->grade_color=$color;
		$this->score=$score;
			
	}

	public function print_table_row($label, $val,$color=null) {
		$style="";
		if($color)
			$style="style='color:$color;font-weight:bold'";
			
		echo "<tr><td class='leg_label'>$label: </td><td class='leg_val' $style>$val</td></tr>";
	}
	public function print_short_bio() {
		global $isPhone;
		$candidate=get_table('table_election')->getobj($this->key);
		$data_key=$this->key;


		echo "<span class='short_bio' data-name='$data_key'><div class='leg_thumb' >";
		echo "<a title='Click for voting record'  href='/guide/legpage.php?id=$this->key'>";


		echo "<img src='http://www.ncleg.net/$this->chamber/pictures/$this->uid.jpg'/></a>
		<h4>$this->title $this->name</h4>
		<h5>$this->party</h5>
		";
		
		/*
		 $district=
		*/
		

        if(get_table("survey_data")->check($this->key))
		{
            echo("<h5><a style='color:green;font-weight:bold;' href='/guide/legpage.php?id=$this->key'>Survey Responses</a></h5>");
        }
     


			$grade_link="<a title='Click for voting record'  style='font-weight:bold;color:" .$this->grade_color
			."' href='/guide/legpage.php?id=$this->key'>$this->grade</a>";
			echo ( "Grade: $grade_link" );
				
	




        /*
		$url=$this->get_url();
		
		echo ("<a target='_blank' href='$url'>Link to page on NCGA website</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href='/guide/legpage.php?id=$this->key'>Link to voting record</a>");
		*/
		echo "</div></span>";
	}

	public function print_list_row($class='leg_bio') {
		global $isPhone;
		$candidate=get_table('table_election')->getobj($this->key);
		$data_key=$this->key;


		echo "<div class='leg_bio' data-name='$data_key'><hr/><div class='leg_thumb' >";
		echo "<a title='Click for voting record'  href='/guide/legpage.php?id=$this->key'>";


		echo "<img src='http://www.ncleg.net/$this->chamber/pictures/$this->uid.jpg'/></a>";
		
		echo "</div><div class='leg_info' ><a title='Click for voting record' href='/guide/legpage.php?id=$this->key'><h2>$this->title $this->name</h2></a><table>";

		/*
		 $district=
		*/
		
		$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		$this->print_table_row ( 'District', "<a title='Show district race'  href=$district_url>$this->chamber #$this->district</a>" );
		$running="";
		if(!$candidate)
		{
			"Not running for re-election.";

				
		}
		$running.="<div><a href=$district_url>Click here for 2014 district race</a></div>";
		$this->print_table_row ( '2014 Election', $running );
		$this->print_table_row ( 'Party', $this->party );
		$this->print_table_row ( 'Counties', $this->county );

		$email_link="<a  href='mailto:'" .$this->email . ">" . $this->email . "</a>";
		$this->print_table_row ( 'Email', $email_link );
		$this->print_table_row ( 'Phone', $this->phone );
        $responded="No";
        if(get_table("survey_data")->check($this->key))
            $responded="<a style='color:green;font-weight:bold;' href='/guide/legpage.php?id=$this->key'>Yes</a>";
        
     
        $this->print_table_row ( 'Responded to survey', $responded );


			$grade_link="<a title='Click for voting record'  style='font-weight:bold;color:" .$this->grade_color
			."' href='/guide/legpage.php?id=$this->key'>$this->grade</a>";
			$this->print_table_row ( 'Grade',$grade_link );
				
	

		if(!$this->comment)
		{
				
			$this->comment="Voting record, responsiveness to inquiries, and feedback from constituents";
			$this->print_table_row ( 'Ranking Created By', $this->comment );
		}
		else
			$this->print_table_row ( 'Reason For Grade', $this->comment );

		if($this->note)
		{
			$this->print_table_row ( 'Note', $this->note );
		}       



		echo '</table>';
        /*
		$url=$this->get_url();
		
		echo ("<a target='_blank' href='$url'>Link to page on NCGA website</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href='/guide/legpage.php?id=$this->key'>Link to voting record</a>");
		*/
		echo "</div></div><div style='clear:both'></div>";
	}
}



function sort_func_grade($a, $b) {
	if ($a->score == $b->score) {
		return 0;
	}
	return ($a->score < $b->score) ? 1 : -1;
}
function sort_func_dist($a, $b) {
	if ($a->district == $b->district) {
		return 0;
	}
	return ($a->district < $b->district) ? -1 : 1;
}

class leg_list extends table_base{
    function create_from_spreadsheet()
	{
		$this->create1('data_v1',2,'legislator','key');
	}
	public function print_list($chamber) {
		echo "<div class='tbl_leglist' >";
		foreach ( $this->list as $d )
        {
            if($chamber)
                if($chamber != $d->chamberId)
                    continue;
			$d->print_list_row ();
		}
		echo '</div>';
	}
   
	public function get_leg_by_key($key) {
        if(array_key_exists ($key,$this->list))
            return $this->list[$key];
        return null;
	}
	
	public function get_leg_by_district($chamber,$district) {
		foreach ( $this->list as $leg ) {
			if (($district == $leg->district)
                &&($chamber==$leg->chamberId))
				return $leg;
		}
		return 0;
	}
	public function sort() {
		
		$sort=getParam("sort");
		if($sort=='grade')
		{
			uasort($this->list, 'sort_func_grade');
		}
		else
		if($sort=='dist')
		{
			uasort($this->list, 'sort_func_dist');
		}	
		else
			ksort ( $this->list );
	
	}	
	
}




function file_get_contents_curl($url)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	
	

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}
class exlink {
	public $key;
	public $candidates;
	public $doc;
	public $date;	
	public $title;	
	public $note;
	public $link;
	public $image;
	public $text;	
	public $candidate_list;
	
	public function __construct($d,$index) {
		$this->key =getj($d,'key');
		$this->link =getj($d,'link');
		$this->doc =getj($d,'doc');
		$this->title =getj($d,'title');
		$this->date =getj($d,'date');
		$this->image =getj($d,'image');
		$this->text =getj($d,'text');
		$this->candidates =getj($d,'candidates');
		$this->candidate_list=array();
		if($this->candidates)
			$this->candidate_list=str_getcsv($this->candidates);
		
		//if($this->link)	$this->fetch();
	}
	
	public function print_panel()	
	{
		global $g_debug;
		echo("<div style='background-color:#eee;padding:10px 10px 0 10px '><div style='background-color:white;margin: 10px 10px 0 10px ;padding:10px'>");
		
		if($g_debug)
		{
			echo("<h4>$this->key</h4>");
		}
		echo("<h4>$this->date</h4>");
		echo ("<a target='_blank' href='$this->link'><img  style='max-width:300px;max-height:200px;' src='$this->image'/></a>");
		echo ("<a  target='_blank' href='$this->link'><h4>$this->title</h4></a>");
		echo ("<p>$this->text</p><div>Links: ");	
		$canlist=	get_table('table_election');
		$comma=false;
		foreach($this->candidate_list as $key )
		{
			
			$candidate=$canlist->getobj($key);
			if($candidate)
			{
				$url=$candidate->get_local_page_url();
				if($comma)
					echo(",");
				echo("<a href='$url'>$candidate->nameonballot</a>");
				
				$comma=true;
			}
			
		}
		echo("</div></div></div>");
		
		
		
	}
	public function fetch()
	{

		try {
		//parsing begins here:
			$html = file_get_contents_curl($this->link);
			
			$doc = new DOMDocument();
			@$doc->loadHTML($html);
			
			//get and display what you need:
			if(!$this->title)
			{
				$nodes = $doc->getElementsByTagName('title');
				$this->title = $nodes->item(0)->nodeValue;
			}
			
			$keywords="";
			$description="";
			$image="";
			
			$metas = $doc->getElementsByTagName('meta');
			
			for ($i = 0; $i < $metas->length; $i++)
			{
				$meta = $metas->item($i);
				if($description=="")
					if($meta->getAttribute('name') == 'description')
						$description = $meta->getAttribute('content');
				if($description=="")					
					if($meta->getAttribute('property') == 'og:description')
						$description = $meta->getAttribute('content');			
				if($image=="")
					if($meta->getAttribute('property') == 'og:image')
						$image = $meta->getAttribute('content');
			}
			if(!$this->image)
			{
				$this->image=$image;
			}		
			if(!$this->text)
			{
				$this->text=$description;
			}
	
		}
		catch (Exception $e) {
			$this->title=$e->getMessage();
  
			}
		
	
	}	

	
	
}

class exlinks extends table_base
{
	function create_from_spreadsheet()
	{
		$this->create1('data_v1',6,'exlink');
	}
	public function has_links($legid,$billid)
	{
		foreach ( $this->list as $row )
		{
			if($legid)
			{
				if(in_array($legid,$row->candidate_list))
					return true;;
			}
			if($billid)
			{
				if($row->doc ==$billid)
					return true;
	
			}
			
		}
	}	
	public function process()
	{
		global $root;
		$filename=$root."/data2/links2.csv";
		$vars=get_member_vars('exlink');
		
		$fp = fopen($filename, 'w');
		fputcsv($fp,$vars);
		fclose($fp);
		
		foreach ( $this->list as $row )
		{
			if(($row->text)&&($row->image)&&($row->title))
				continue;			
			$row->fetch();
			$array= (array)$row;
			$fp = fopen($filename, 'a');
			fputcsv($fp,$array);
			fclose($fp);
			
			
		}
	}	
	public function print_list($legid,$billid)
	{
		foreach ( $this->list as $row )
		{
			if($legid)
			{
				if(in_array($legid,$row->candidate_list)==false)
					continue;
			}
			if($billid)
			{
				if($row->doc !=$billid)
					continue;

			}			
			$row->print_panel();
		}
	}	

}

class survey_question 
{
	public $q;
    public function __construct($d,$index) {
        $this->q = getj($d,'question');
	}
}
class survey_questions extends table_base
{
 	function create_from_spreadsheet()
	{
		$this->create1('data_v1',7,'survey_question');
	}     
	public function getquestion($num)
	{
		return  $this->list[$num]->q;
	}
}
class survey_resp 
{
	public $fistname;
	public $lastname;
	public $comments;
	public $answers;
	public $key;
    public function __construct($d,$index) {
            $this->answers=array();
		    $this->key = getj($d,'key');
		    $this->firstname = getj($d,'firstname');
		    $this->lastname = getj($d,'lastname');
		    $this->comments = getj($d,'comments');
            $this->answers[0]=getj($d,'a1');
            $this->answers[1]=getj($d,'a2');
            $this->answers[2]=getj($d,'a3');
            $this->answers[3]=getj($d,'a4');
            $this->answers[4]=getj($d,'a5');
        
            }
	public function printresp()
	{
		for ($x=0; $x<5; $x++)
		{
			$qnum=$x+1;
			$q=get_table("survey_questions")->getquestion($x);
			if(!$q)
				$q="could not get question";
			$a=$this->answers[$x];
			if(!$a)
				$a="[blank]";			
			echo("<div style='margin-top:30px' class='section_head'>Question #$qnum</div>");
			echo("<div>$q</div>");
			echo("<div style='margin-top:10px' class='section_head'>Answer:</div>");
			echo("<div>$a</div>");
		}
        

	}	
}

class survey_data extends table_base
{
 	function create_from_spreadsheet()
	{
		$this->create1('survey1',1,'survey_resp','key');
	}   
	public function check($key)
	{
        return (array_key_exists ($key,$this->list));
 
	}	
	
	public function printresp($key)
	{
 		echo("<div  style='max-width:800px'><h3>Responses to animal welfare survey:</h3>");    
       $row=null;
        if(array_key_exists ($key,$this->list))
            $row= $this->list[$key];
		if(!$row)
        {
            echo("<div>Did not respond to our survey.</div>");    
        }
        else
       		$row->printresp();
        echo("</div>");  
	}

	public function printlist()
	{
		ksort ( $this->list );
		global $g_debug;
		foreach ( $this->list as $row )
		{
			$key=$row->key;
				
			$bio=get_table ( "table_person" )->getobj ( $key );
			if($bio)
			{
				
				$bio->print_list_row();
			}
			else {
				
				if($g_debug)
				{
					echo("<H1>$key NOT FOUND</H1>");
				}
				
			}
		}
	}
}



?>

