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
		$canidate=get_table("table_election")->get_row($this->key);
		
		
		
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




?>

