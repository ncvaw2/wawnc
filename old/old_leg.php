<?php
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
		$leg=get_table("table_office")->get_leg_by_key($this->key);
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
			$leg=get_table("table_office")->get_leg_by_key($x->key);
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
			$leg=get_table("table_office")->get_leg_by_key($x->key);
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
		$legs=get_table("table_office");
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
		$legs=get_table("table_office");
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


class legislator_OBSOLETE{

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
		return ("http://www.ncga.state.nc.us/gascripts/members/viewMember.pl?sChamber=$this->chamber&nUserID=$this->uid");
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


		echo "<img src='http://www.ncga.state.nc.us/$this->chamber/pictures/$this->uid.jpg'/></a>
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
	global $g_electionMode;
	$candidate=get_table('table_election')->getobj($this->key);
	$data_key=$this->key;


	echo "<div class='leg_bio' data-name='$data_key'><hr/><div class='leg_thumb' >";
	echo "<a title='Click for voting record'  href='/guide/legpage.php?id=$this->key'>";


	echo "<img src='http://www.ncga.state.nc.us/$this->chamber/pictures/$this->uid.jpg'/></a>";

	echo "</div><div class='leg_info' ><a title='Click for voting record' href='/guide/legpage.php?id=$this->key'><h2>$this->title $this->name</h2></a>";
	if($candidate)
	{
	if($candidate->endorsements=='Y')
	{
	echo("<img class='endorse' style='width:40px'  src='/img/endorse_small.png'><span style='color:blue'>NCVAW Endorsed</h4>");
			}
		}

		echo "<table>";

		/*
		$district=
		*/

		$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		$this->print_table_row ( 'District', "<a title='Show district race'  href=$district_url>$this->chamber #$this->district</a>" );
		$running="";

		$running.="<div><a href=$district_url>Click here for 2014 district race</a></div>";
		if(	$g_electionMode	)
		$this->print_table_row ( '2016 Election', $running );
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


class leg_list_OBSOLETE extends table_base{
	
	function get_columns()
	{
		return ['key','chamber','uid','district','party','email','offaddr','offaddr2','offzip','offphone','name'];
	}	
	
    function create_from_spreadsheet()
	{
		$this->create('data_v2',2,'legislator','key');
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

?>
