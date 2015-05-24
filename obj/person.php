<?php
include_once  $root.'/inc/db.php';



function get_party($id)
{
	$parties=array(
	
			'DEM'=>'Democratic',
			'REP'=>'Republican',
			'IND' => 'Independent',
			'LIB' => 'Libertarian',
			'UNA' => 'Unaffiliated',
	);
	
	$p=$parties[$id];
	if(!$p)
		return $id;

	return $p;
}

function get_chamber($id)
{
	if($id=='H') return 'House';
	if($id=='S') return 'Senate';
	return 'None';
}
function print_table_row($label, $val,$color=null) {
	$style="";
	if($color)
		$style="style='color:$color;font-weight:bold'";
			
	echo "<tr><td class='leg_label'>$label: </td><td class='leg_val' $style>$val</td></tr>";
}	
$grade_chart = [
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
	 [ 9999,"A+","#0c0"],
];
function get_grade_color($grade,&$font)
{
	global $grade_chart;
	
	foreach ( $grade_chart as $g )
	{
		if($grade == $g[1])
		{
			$font='bold';
			return $g[2];
		}
	}	
	return "#000";
}
function get_grade($score)
{
	global $grade_chart;
	$grade="A+";
	foreach ( $grade_chart as $g )
	{
		if($score <= $g[0])
			return $g[1];
	}
	return "Ungraded";
}
function get_score_from_grade($grade)
{
	global $grade_chart;
	
	foreach ( $grade_chart as $g )
	{
		if($grade == $g[1])
		{
			return $g[0];
		}
	}	
	return 0;
}



class person
{
	//columns
	public $key;
	public $grade;
	public $score;
	public $gradecomment;
	public $party;
	public $fullname;
	public $photo;
	public $phone;
	public $email;
	public $addr;
	public $city;
	public $state;
	public $zip;

	public $website;
	public $facebook;
	//lookup
	public $candidate;
	public $office;
	public $election;

	public function get_photo_url()
	{
		global $root;
		
		$dir='/img/people/';
		if (!file_exists($root .$dir)) {
			mkdir($root .$dir, 0777, true);
		}
		$path=$dir.$this->key . '.jpg';
		$filename=$root . $path;
		
		if( file_exists ( $filename ))
			return $path;
		if($this->photo)
		{
			$result=file_put_contents($filename, file_get_contents($this->photo));
			
			if($result==='FALSE')
				return null;
		}
		return $path;
		
	
	}
	public function get_local_page_url() {
		$url="/v2/bio.php?key=$this->key";
		return $url;
	}	
	public function pdata_init()
	{
		global $g_debug;
		$this->office	=get_table("table_office")->getobj($this->key);
		$this->candidate= null; //get_table("table_election")->getobj($this->key);

		if($this->office)
		{
			
			if($this->office->chamber=='H')
			{
				$this->fullname="Representative ".$this->fullname;
				$chamber='House';
			}
			else 
			{
				$chamber='Senate';
				
				$this->fullname="Senator ".$this->fullname;
				
			}
			$uid=$this->office->uid;
			
			if(!$this->photo)
					$this->photo="http://www.ncga.state.nc.us/$chamber/pictures/$uid.jpg";
			
			$this->phone=$this->office->offphone;
			$this->website=NULL;
				
			$this->email=$this->office->email;
		}
		if($this->candidate)
		{
			$this->party=get_party($this->candidate->party);			
			$this->district=$this->candidate->district;			
		}
		else
			$this->election="Not running";
		
		
		$this->photo=$this->get_photo_url();
		// *** GRADE ****
		if($this->grade)
		{
			$this->score=get_score_from_grade($this->grade);
		}
		else
		{
			$count=0;
			$this->score=get_table("vote_data")->get_votes($this->key,$count);
			if( $count==0)
			{
				$this->grade='Not Yet Graded';
				$this->gradecomment='Has not been in office long enough to assign grade';
			}
			else
				$this->grade=get_grade($this->score);
		}		


		
	}
	public function print_table_row($label, $val,$color=null) {
		$style="";
		if($color)
			$style="style='color:$color;font-weight:bold'";
			
		echo "<tr><td class='leg_label'>$label: </td><td class='leg_val' $style>$val</td></tr>";
	}	
	public function printPage() {
		$this->print_list_row();
	}

	
	public function print_list_row($class='leg_bio') {
		global $root;
		

	
		$data_key=$this->key;
	
		echo ("<div class='$class' data-name='$data_key'><hr>");
		//thumbnail
	
		if($this->photo)
		{
	
			echo ("<div class='leg_thumb' ><a href='/v2/bio.php?key=$this->key'>");
			echo ("<img src='$this->photo'/></a></div>");
		}
		else {
			echo ("<div class='leg_thumb' ><img src='/img/unknown.png'/></div>");
	
		}
	
		echo ("<div class='leg_info' ><a href='/v2/bio.php?key=$this->key'><h2>$this->fullname</h2></a>");

		if($this->candidate)
		{
			if($this->candidate->endorsements=='Y')
			{
				echo("<img class='endorse' style='width:40px' src='/img/endorse_small.png'><span style='color:blue'>NCVAW Endorsed</h4>");

			}
		}



		echo("<table><tr><td/><td/></tr>");
//GRADES
		
		if($this->grade)
		{
			$f='normal';
			$c=get_grade_color($this->grade,$f);
			$grade_link="<a title='Click for profile'  style='font-weight:$f;color:$c' href='/guide/legpage.php?id=$this->key'>$this->grade</a>";
			$this->print_table_row ( 'Grade (2013)',$grade_link );
			if($this->gradecomment)
				$this->print_table_row ( 'Reason For Grade', $this->gradecomment );
			else
			{
			
				$this->gradecomment="Voting record, responsiveness to inquiries, and feedback from constituents";
				$this->print_table_row ( 'Ranking Created By', $this->gradecomment );
			}
				
		}		
		
//OFFICE		
		if($this->office)
		{
			$dist=$this->office->district;
			$ncleg_url=$this->office->get_ncleg_link();
			$chamberId=$this->office->chamber;
			$chamber=get_chamber($chamberId);
			$district_url="'/district.php?dist=". $dist . "&ch=" . $chamberId . "'";
			print_table_row ( 'District', "<a href=$district_url>$chamber # $dist</a>" );
			
			
				
		
		}
		else {
			
		}	

		print_table_row ( 'Party', get_party($this->party ));
        $responded="No";
        if(get_table("survey_data")->check($this->key))
            $responded="<a style='color:green;font-weight:bold;' href='/v2/bio.php?key=$this->key'>Yes</a>";
        
     
        print_table_row ( 'Responded to survey', $responded );	


	
		if($this->website)
		{
			$link="<a href='".$this->website."' target='_blank'>".$this->website."</a>";
			$this->print_table_row ( 'Website', $link );
	
		}
		if($this->facebook)
		{
			$link="<a href='".$this->facebook."' target='_blank'>Facebook Page</a>";
			$this->print_table_row ( 'Facebook', $link );
		
		}	
		if($this->email)
		$this->print_table_row ( 'Email',"<a  href='mailto:" .$this->email . "'>" . $this->email . "</a>" );
		if($this->phone)
		$this->print_table_row ( 'Phone', $this->phone );
		if($this->office)
			print_table_row ( 'NCGA website', "<a  target='_blank' href='" . $ncleg_url . "'>Click here for NCGA page</a>" );
		
		echo ('</table>');
			
		echo ("</div></div><div style='clear:both'></div>");
	}	
	

}

class table_person  extends table_base
{
	function get_columns()
	{
		return ['key','fullname','party','grade','gradecomment','addr','city','state','zip','phone','email','photo','facebook','website'];
	}	
	function create_from_spreadsheet()
	{
		$this->create('data_v2','oc8fqax','person','key');
	}
	function create_offline()
	{
		$me=new person();
		$me->key='anthony.corriveau';
		$this->list [] = new $objname ( $row ,$index);
		
	}	

	function printtable()
	{
		$column="key";
		foreach ($this->list as $row )
		{
			$row->print_list_row();
		}
	}
	public function print_list() {
		echo "<div class='tbl_leglist' >";
		foreach ( $this->list as $d )
		{
			$d->print_list_row ();
		}
		echo '</div>';
	}	
	public function sort() {
	
		$sort=getParam("sort");
		if($sort=='grade')
		{
			uasort($this->list, 'sort_func_grade');
		}
		/*
		else
		if($sort=='dist')
		{
			uasort($this->list, 'sort_func_dist');
		}
		*/
		else
			ksort ( $this->list );
	
	}	
}
class office
{
	//columns
	public $key;
	public $chamber;
	public $uid;
	public $party;
	public $district;		
	public $offphone;		
	public $email;
	public $offaddr;
	public $offaddr2;
	public $offzip;
	public $name;
	
	public function get_ncleg_link() {
		return ("http://www.ncga.state.nc.us/gascripts/members/viewMember.pl?sChamber=$this->chamber&nUserID=$this->uid");
	}	
	public function print_list_votes()
	{
		get_table("vote_data")->print_list_votes($this->key,0);
	}
	public function print_list_sponsorship()
	{
		get_table("vote_data")->print_list_votes($this->key,1);
	}
	public function print_survey() {
		get_table("survey_data")->printresp($this->key);
	}		
	function get_photo_url()
	{
		
		
		
	}
	public function print_list_row() {
		$person=get_table("table_person")->getobj($this->key);
		if($person)
		{
			 $person->print_list_row();
			 return;
		}
		echo("<div>". $this->name . " NOT FOUND</div>");
		
		echo("<table>");
		//echo("<tr><td colspan='2'/><h4>Office<h4><td/></tr>");
		$office=get_chamber($this->chamber) . ' district #' . $this->district;
		$email_link="<a  href='mailto:'" .$this->email . ">" . $this->email . "</a>";

		print_table_row('Office',$office);
		print_table_row('Email',$email_link);
		print_table_row('Phone',$this->offphone);

		echo ('</table>');
	}
		
};
function sort_func_grade($a, $b) {
	if ($a->score == $b->score) {
		return 0;
	}
	return ($a->score < $b->score) ? 1 : -1;
}
function sort_func_dist($a, $b) {
	if ($a->office->district == $b->office->district) {
		return 0;
	}
	return ($a->office->district < $b->office->district) ? -1 : 1;
}



class table_office  extends table_base
{
	public $people;
	function get_columns()
	{
		return ['key','chamber','uid','party','district','email','offphone','offaddr','offaddr2','offzip','name'];
	}	
	function create_from_spreadsheet()
	{
		$this->create('data_v2','owx3nyv','office','key');
	}
	public function print_list($chamber) {
		$this->get_people_list();
		echo "<div class='tbl_leglist' >";
		foreach ( $this->people as $d )
		{
			if($chamber)
				if($chamber != $d->office->chamber)
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
                &&($chamber==$leg->chamber))
				return $leg;
		}
		return 0;
	}
	public function get_people_list() {
		if($this->people)
			return $this->people;
		$this->people=array();
		foreach ( $this->list as $d )
		{
			$person=get_table("table_person")->getobj($d->key);
			$this->people[$d->key]=$person;
		}	
	}
		
	public function sort() {
	
		$this->get_people_list();
		
		$sort=getParam("sort");
		if($sort=='grade')
		{
			uasort($this->people, 'sort_func_grade');
		}
		else
			if($sort=='dist')
			{
				uasort($this->people, 'sort_func_dist');
			}
		else
			ksort ( $this->people );
	
	}	
}




?>

