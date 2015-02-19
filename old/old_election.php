<?php

class candidate {
	public $displayname;
	public $key;
	public $party;
	public $party_id;
	public $first;
	public $last;

	public $id;
	public $uid;
	public $chamberId;
	public $chamber;
	public $district;
	public $url_cover_jpg;

	public $jpg_path;
	public $url;
	public $photo;
	public $website;
	public $phone;
	public $email;
	public $election;



	public function __construct($d,$index) {
		$this->photo =getj($d,'photo');
		$this->election=getj($d,'election');
		$this->displayname=getj($d,'nameonballot');
		$this->party_id = getj($d,'party');
		$this->phone = getj($d,'phone');
		$this->district = getj($d,'district');
		$this->chamberId = getj($d,'chamber');
		$this->key = getj($d,'key');
		$this->website = getj($d,'website');
		$this->party=$this->party_id;
        
        
		if($this->party_id=='DEM')
		{
			$this->party='Democratic';
		}
		if($this->party_id=='REP')
		{
			$this->party='Republican';
		}
		if($this->chamberId=='H')
			$this->chamber='House';
		else
			$this->chamber='Senate';
			
	}

	public function print_table_row($label, $val) {
		echo ("<tr><td class='leg_label'>$label: </td><td class='leg_val'>$val</td></tr>");
	}
	public function get_running() {
		$running="Running for re-election in the ";

		if($this->election=='gen')
		{
			$running.='general election 11/4/2014';
				
		}
		else
		{
			$running.=$this->party . ' primary election 5/6/2014';
		}
		return $running;
	}
	public function get_local_page_url() {	
		$url="/guide/candidate.php?key=$this->key";
		$leg=get_table("table_office")->get_leg_by_key($this->key);
		if($leg)
		{
			$url="/guide/legpage.php?id=$this->key";
			
		}
		return $url;
	}
	public function print_list_row() {

		$leg=get_table("table_office")->get_leg_by_key($this->key);
		if($leg)
		{
			$leg->print_list_row();
			return;
		}
		$person = get_table ( "table_person" )->getobj ( $key );
		$person->print_list_row();
		return;
		$data_key=$this->key;
		
		echo ("<div class='leg_bio' data-name='$data_key'><hr>");
		//thumbnail
		
		if($this->photo)
		{

			echo ("<div class='leg_thumb' ><a href='/guide/candidate.php?key=$this->key'>");
			echo ("<img src='$this->photo'/></a></div>");
		}
		else {
			echo ("<div class='leg_thumb' ><img src='/img/unknown.png'/></div>");
				
		}

		echo ("<div class='leg_info' ><a href='/guide/candidate.php?key=$this->key'><h2>$this->displayname</h2></a><table><tr><td/><td/></tr>");
		$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		$this->print_table_row ( 'District', "<a href=$district_url>$this->chamber # $this->district</a>" );

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

}


class candidates extends table_base
{
	function create_from_spreadsheet()
	{
		$this->create1('data_v1',5,'candidate','key');
	}	

	public function get_candiate($key) {
        if(array_key_exists ($key,$this->list))
            return $this->list[$key];
        //person may not be running
        return null;
	}	
	
	public function getlist($ch,$num,$elect)
	{
		$set=array();
		foreach ( $this->list as $c )
		{
			if( 
            ($c->district==$num)&&
            ($c->chamberId==$ch)&&
            ($c->election==$elect))
			{
				$set [] =$c;
			}	
		}
		return $set;
	}
	public function get_candate_links($ch,$num,$elect)
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
				
				$link_string.="<div>$x->party_id: <a href='/guide/legpage.php?id=$x->key'>$x->displayname $grade</a></div>";
				
			}
			else
				$link_string.="<div>$x->party_id: <a href='/v2/bio.php?key=$x->key'>$x->displayname $grade</a></div>";

			
		}
		if(count($set)==1)
		{
			$link_string.="<div>(uncontested)</div>";
			
		}
		return $link_string;
	}	
	public function printlist($ch,$num,$elect)
	{
		
	    $set=$this->getlist($ch,$num,$elect);
		foreach ( $set as $x )
				$x->print_list_row();
	}	
} ?>