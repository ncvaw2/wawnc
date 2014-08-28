<?php
include $root.'/obj/tables.php';

class canidate
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

	public function __construct($d,$index) {
		//table data
		$this->key = getj($d,'key');
		$this->year=getj($d,"year");
		$this->type=getj($d,"type");
		$this->district = getj($d,'district');
		$this->chamber = getj($d,'chamber');
		$this->party = getj($d,'party');
		$this->nameonballot = getj($d,'nameonballot');
		$this->endorsements = getj($d,'endorsements');
	}
}
class table_election  extends table_base
{
	function create_from_spreadsheet()
	{
		$this->get_json_data('oi0q51k','canidate','key','1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc');
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
	public $first;
	public $middle;
	public $last;
	public $phone;
	public $email;
	public $addr;
	public $city;
	public $state;
	public $zip;

	//lookup
	public $canidate;
	public function init()
	{
		
		
		$canidate=get_table("table_election")->get_row($this->key);
		//$canidate=get_table("table_election")->get_row($this->key);
		
		
		
	}
	public function print_list_row() {
	
		$leg=get_table("leg_list")->get_leg_by_key($this->key);
		if($leg)
		{
			$leg->print_list_row();
			return;
		}
		$data_key=$this->key;
	
		echo ("<div class='leg_bio' data-name='$data_key'><hr>");
		//thumbnail
	
		if($this->photo)
		{
	
			echo ("<div class='leg_thumb' ><a href='/guide/canidate.php?key=$this->key'>");
			echo ("<img src='$this->photo'/></a></div>");
		}
		else {
			echo ("<div class='leg_thumb' ><img src='/img/unknown.png'/></div>");
	
		}
	
		echo ("<div class='leg_info' ><a href='/guide/canidate.php?key=$this->key'><h2>$this->displayname</h2></a><table><tr><td/><td/></tr>");
		$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		$this->print_table_row ( 'District', "<a href=$district_url>$this->district</a>" );
	
		$this->print_table_row ( 'Party', $this->party );
		$running="Challenger in the ";
	
		if($this->election=='gen')
		{
			$running.='general election 11/4/2014';
	
		}
		else
		{
			$running.=$this->party . ' primary election 5/6/2014';
		}
	
		$this->print_table_row ( '2014 Election', $running );
	
		if($this->website)
		{
			$link="<a href='".$this->website."' target='_blank'>".$this->website."</a>";
			$this->print_table_row ( 'Webiste', $link );
	
		}
	
	
		$this->print_table_row ( 'Email', $this->email );
		$this->print_table_row ( 'Phone', $this->phone );
	
		echo ('</table>');
			
		echo ("</div></div><div style='clear:both'></div>");
	}	
	
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




	}
}
class table_person  extends table_base
{
	function create_from_spreadsheet()
	{
		$this->get_json_data('oc8fqax','person','key','1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc');
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
class table_office  extends table_base
{
	function create_from_spreadsheet()
	{
		$this->get_json_data('owx3nyv','office','key','1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc');
	}

}




?>

