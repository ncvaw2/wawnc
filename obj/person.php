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
	public $district;

			
	public function init()
	{
		global $g_debug;
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
	public function print_short_bio() {
	
		$this->init();
		
		if($this->office)
		{
			$leglist=get_table("leg_list");
			/* temporary patch */
			$leg=get_table("leg_list")->get_leg_by_key($this->key);
			if($leg)
			{
				$leg->print_list_row();
				return;
			}
		}
	
		$data_key=$this->key;
	
		echo ("<span class='short_bio' data-name='$data_key'>");
		//thumbnail
	
		if($this->photo)
		{
	
			echo ("<div class='leg_thumb' ><a href='/v2/bio.php?key=$this->key'>");
			echo ("<img src='$this->photo'/></a></div>");
		}
		else {
			echo ("<div class='leg_thumb' ><img src='/img/unknown.png'/></div>");
	
		}
	
		echo ("<div class='leg_info' ><a href='/v2/bio.php?key=$this->key'><h2>Candidate $this->fullname</h2></a>");

		if($this->candidate)
		{
			if($this->candidate->endorsements=='Y')
			{
				echo("<img class='endorsesmall' src='img/endorse_small.png'><h5>NCVAW Endorsed</h5>");

			}
		}


		echo("<table><tr><td/><td/></tr>");
		//$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		//$this->print_table_row ( 'District', "<a href=$district_url>$this->district</a>" );
		
		$chamberId=$this->candidate->chamber;
		$chamber=get_chamber($chamberId);
		$district_url="'/district.php?dist=". $this->district . "&ch=" . $chamberId . "'";
		print_table_row ( 'Party', $this->party );
        $responded="No";
        if(get_table("survey_data")->check($this->key))
            $responded="<a style='color:green;font-weight:bold;' href='/v2/bio.php?key=$this->key'>Yes</a>";
        
     
        print_table_row ( 'Survey', $responded );	

			

	
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

	
		echo ('</table>');
			
		echo ("</div></span>");
	}	
	
	public function print_list_row($class='leg_bio') {
		global $root;
		$this->init();
		
		if($this->office)
		{
			$leglist=get_table("leg_list");
			/* temporary patch */
			$leg=get_table("leg_list")->get_leg_by_key($this->key);
			if($leg)
			{
				$leg->print_list_row();
				return;
			}
		}
	
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
	
		echo ("<div class='leg_info' ><a href='/v2/bio.php?key=$this->key'><h2>Candidate $this->fullname</h2></a>");

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
		
		$chamberId=$this->candidate->chamber;
		$chamber=get_chamber($chamberId);
		$district_url="'/district.php?dist=". $this->district . "&ch=" . $chamberId . "'";
		print_table_row ( '2014 Election', "Candidate" );
		print_table_row ( 'District', "<a href=$district_url>$chamber # $this->district</a>" );
		
		print_table_row ( 'Party', $this->party );
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
		return ['key','grade','gradecomment','fullname','first','middle','last','phone','email','photo','facebook','website'];
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
	function get_photo_url()
	{
		
		
		
	}

	function printbio()
	{
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
		return ['key','chamber','uid','party','district','email','offphone','offaddr','offaddr2','offzip'];
	}	
	function create_from_spreadsheet()
	{
		$this->create('data_v2','owx3nyv','office','key');
	}

}




?>

