<?php
include_once  $root.'/inc/db.php';

$g_parties=array(

		'DEM'=>'Democratic',
		'REP'=>'Republican',
		'IND' => 'Independent'

);
function print_table_row($label, $val,$color=null) {
	$style="";
	if($color)
		$style="style='color:$color;font-weight:bold'";
			
	echo "<tr><td class='leg_label'>$label: </td><td class='leg_val' $style>$val</td></tr>";
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
	function printtable()
	{
		$column="key";
		foreach ($this->list as $row )
		{
			$val=$row->$column;
			echo($val);
		}
	}
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
	public $first;
	public $middle;
	public $last;
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
	
	public function init()
	{
		if($this->inited)
			return;
		$this->inited=true;
		$this->office	=get_table("table_office")->getobj($this->key);
		$this->candidate=get_table("table_election")->getobj($this->key);
		
		
		$this->fullname=$this->first . " " . $this->last;
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
					$this->photo="http://www.ncleg.net/$chamber/pictures/$uid.jpg";
			
		}
		if($this->candidate)
		{
			
		}
		else
			$this->election="Not running";
		//$candidate=get_table("table_election")->getobj($this->key);
		
		
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
	public function print_list_row() {
	
		$this->init();
		
		if($this->office)
		{
			$leglist=get_table("leg_list");
			
			//$running=$this->candidate->party;
			
				
			//$this->print_table_row ( '2014 Election', $running );
		}
	
		$data_key=$this->key;
	
		echo ("<div class='leg_bio' data-name='$data_key'><hr>");
		//thumbnail
	
		if($this->photo)
		{
	
			echo ("<div class='leg_thumb' ><a href='/v2/bio.php?key=$this->key'>");
			echo ("<img src='$this->photo'/></a></div>");
		}
		else {
			echo ("<div class='leg_thumb' ><img src='/img/unknown.png'/></div>");
	
		}
	
		echo ("<div class='leg_info' ><a href='/v2/bio.php?key=$this->key'><h2>$this->fullname</h2></a><table><tr><td/><td/></tr>");
		//$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		//$this->print_table_row ( 'District', "<a href=$district_url>$this->district</a>" );
	
		print_table_row ( 'Party', $this->party );
        $responded="No";
        if(get_table("survey_data")->check($this->key))
            $responded="<a style='color:green;font-weight:bold;' href='/guide/legpage.php?id=$this->key'>Yes</a>";
        
     
        print_table_row ( 'Responded to survey', $responded );			
		if($this->candidate)
		{
			$running=$this->candidate->party;
		
			$running.='general election 11/4/2014';
		
		
			//$running.=$this->party . ' primary election 5/6/2014';
			$this->print_table_row ( '2014 Election', $running );
		}
			

	
		if($this->website)
		{
			$link="<a href='".$this->website."' target='_blank'>".$this->website."</a>";
			$this->print_table_row ( 'Website', $link );
	
		}
	
	
		$this->print_table_row ( 'Email', $this->email );
		$this->print_table_row ( 'Phone', $this->phone );
	
		echo ('</table>');
			
		echo ("</div></div><div style='clear:both'></div>");
	}	
	
	/*
	public function __construct($d,$index) {
		//table data
		$this->key = getj($d,'key');
		$this->grade=getj($d,"grade");
		$this->gradecomment=getj($d,"gradecomment");
		$this->fullname = getj($d,'fullname');
		$this->first = getj($d,'first');
		$this->middle = getj($d,'middle');
		$this->last = getj($d,'last');
		$this->phone = getj($d,'phone');
		$this->email=getj($d,"email");
		$this->init=false;

	}*/
}

class table_person  extends table_base
{
	function get_columns()
	{
		return ['key','grade','gradecomment','fullname','first','middle','last','phone','email','facebook','website'];
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
	public $email;
	function get_photo_url()
	{
		
		
		
	}
		
};
class table_office  extends table_base
{
	
	function get_columns()
	{
		return ['key','chamber','uid','party','district','email'];
	}	
	function create_from_spreadsheet()
	{
		$this->create('data_v2','owx3nyv','office','key');
	}

}




?>

