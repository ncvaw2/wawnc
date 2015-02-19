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


class person
{
	//columns
	public $key;
	public $grade;
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
	public $inited;
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
			
	public function init()
	{
		global $g_debug;
		if($this->inited)
			return;
		$this->inited=true;
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
			
		}
		if($this->candidate)
		{
			$this->party=get_party($this->candidate->party);			
			$this->district=$this->candidate->district;			
		}
		else
			$this->election="Not running";
		//$candidate=get_table("table_election")->getobj($this->key);
		
		$this->photo=$this->get_photo_url();
		
		
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
		$this->init();
		

	
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
		//$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		//$this->print_table_row ( 'District', "<a href=$district_url>$this->district</a>" );
		if($this->office)
		{
			$dist=$this->office->district;
			$ncleg_url=$this->office->get_ncleg_link();
			$chamberId=$this->office->chamber;
			$chamber=get_chamber($chamberId);
			$district_url="'/district.php?dist=". $dist . "&ch=" . $chamberId . "'";
			print_table_row ( 'District', "<a href=$district_url>$chamber # $dist</a>" );
			
			print_table_row ( 'Party', $this->party );
			print_table_row ( 'NCGA website', "<a  target='_blank' href='" . $ncleg_url . "'>Click here for NCGA page</a>" );
				
		
		}		
        $responded="No";
        if(get_table("survey_data")->check($this->key))
            $responded="<a style='color:green;font-weight:bold;' href='/v2/bio.php?key=$this->key'>Yes</a>";
        
     
        print_table_row ( 'Responded to survey', $responded );	
        /*		
		if($this->candidate)
		{
			$running=$this->candidate->party;
		
			$running.='general election 11/4/2014';
		
		
			//$running.=$this->party . ' primary election 5/6/2014';
			$this->print_table_row ( '2014 Election', $running );
		}
		*/
			

	
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
		$this->print_table_row ( 'Email', $this->email );
		if($this->phone)
		$this->print_table_row ( 'Phone', $this->phone );
	
		echo ('</table>');
			
		echo ("</div></div><div style='clear:both'></div>");
	}	
	

}

class table_person  extends table_base
{
	function get_columns()
	{
		return ['key','grade','gradecomment','fullname','phone','email','photo','facebook','website'];
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
class table_office  extends table_base
{
	
	function get_columns()
	{
		return ['key','chamber','uid','party','district','email','offphone','offaddr','offaddr2','offzip','name'];
	}	
	function create_from_spreadsheet()
	{
		$this->create('data_v2','owx3nyv','office','key');
	}
	public function print_list($chamber) {
		echo "<div class='tbl_leglist' >";
		foreach ( $this->list as $d )
		{
			if($chamber)
				if($chamber != $d->chamber)
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

