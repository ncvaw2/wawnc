<?php
include_once  $root.'/inc/db.php';
include_once  $root.'/obj/survey.php';
include_once  $root.'/obj/election.php';



function get_district_url($ch,$num)
{
    $district_url="'/district.php?dist=". $num . "&ch=" . $ch . "'";
    return $district_url;

}
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

function toColor($n)
{
	return("#".substr("000000".dechex($n),-6));
}
$grade_chart = [
	 [ 0.1,"F-",0xff0000],
	 [ 0.2,"F",0xee0000],
	 [ 0.25,"D-",0xdd0000],
	 [ 0.3,"D",0xdd0000],
	 [ 0.35,"D+",0xaa0044],
	 [ 0.45,"C-",0x880088],
	 [ 0.55,"C",0x0000ff],
	 [ 0.6,"C+",0x0022cc],
	 [ 0.7,"B-",0x008888],		
	 [ 0.8,"B",0x008888],
	 [ 0.85,"B+",0x00cc44],
	 [ 0.9,"A-",0x00cc00],
	 [ 0.95,"A",0x00cc00],
	 [ 999,"A+",0x00cc00],
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
	return 0;
}
function get_grade($score)
{
	global $grade_chart;

	$grade="Ungraded";
	foreach ( $grade_chart as $g )
	{
		if($score <= $g[0])
		{
			$grade= $g[1];
			break;
		}
	}

	return $grade;
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
function create_dropshadow(&$im,$x,$y,$size,$offset,$color,$text)
{
	global $root;
	for($i=-1;$i<=1;$i++)
		for($j=-1;$j<=1;$j++)
		{
			imagettftext($im, $size, 0, $x+$offset*$i,$y+$offset*$j, $color, $root ."/img/verdanab.ttf",$text);
		}
}
function get_name_link($key,$show_party)
{
    $link_string="";
    $legs=get_table("table_office");
    $party="";
    $survey="";
    $person=get_table("table_person")->getobj($key);
    $leg=$legs->get_leg_by_key($key);
    if($show_party){
        $party="<span style='font-size:smaller'>($leg->party)</span>";


    }
    $grade="";
    if(get_table ( "table_survey" )->check($key))
    {
        $survey="<span style='font-size:small'>(survey)</span>";
    }
    if($leg)
    {
        $f='normal';
        $c=get_grade_color($person->grade,$f);
        $grade="<span style='font-weight:$f;color:" .toColor($c) . "'>" . $person->grade . "</span>";

        $link_string.="<div><a href='/bio/$key'>$person->fullname $party $grade $survey </a></div>";

    }
    else
        $link_string.="<div><a href='/bio/$key'>$person->fullname $party $grade $survey</a></div>";

    return $link_string;

}

class person
{
	//columns
	public $key;
	public $grade;
	public $gender;
	public $score;
	public $points;
	public $pointstotal;
	
	public $gradecomment;
	public $party;
	public $fullname;
	public $titlename;	
	public $photo; //URL REMOTE

	public $photo_url_local;
	public $photo_url_local_grade;


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
	public function init()
	{
		
		
	}
	public function get_photo_url()
	{
		global $root;
		
		$dir='/img/people/';
		if (!file_exists($root .$dir)) {
			mkdir($root .$dir, 0777, true);
		}
		$path=$dir.$this->key . '.jpg';
		$local_filepath=$root . $path;
		$this->photo_url_local=null;

		if( file_exists ( $local_filepath ))
		{
			$this->photo_path_local=$local_filepath;
			$this->photo_url_local=$path;
			return $path;
		}
		if($this->photo)
		{
			$result='FALSE';
			$contents=null;
			try {
				//$file_headers = @get_headers($this->photo);
				//if(strpos($file_headers[0], '404') ==='FALSE')
				{
					$contents=file_get_contents($this->photo);
				}
				
			}
			catch (Exception $e){}
			if($contents)
			{
				$result=file_put_contents($local_filepath, $contents);
			}
			if($result==='FALSE')
				return null;
			$this->photo_url_local=$path;
			
		}
		return $path;

	}
	
	
	public function make_grade_photo()
	{
		global $root;
		$dir='/img/peoplegrades/';
		if (!file_exists($root .$dir)) {
			mkdir($root .$dir, 0777, true);
		}
		if($this->photo_url_local==null)
			return;
		if(! file_exists ($root . $this->photo_url_local))
			return ;
        if($this->grade == "Ungraded")
            return;
		$grade_photo_rel=$dir . $this->key . '.' . $this->grade . '.jpg';
		
		
		if(! file_exists ($root. $grade_photo_rel))
		{	
			$f='normal';

			
			$c=get_grade_color($this->grade,$f);
			list($width, $height, $type, $attr) = getimagesize($root .$this->photo_url_local);
			$size=$width/5;
			$x=10; //$width-$size*strlen($this->grade)/1.6-20;
			$y=$height-10;
	
			$im = imagecreatefromjpeg($root . $this->photo_url_local);
			if(!is_resource ($im))
				return;
			//imagestring($im, 18,$width- 40, $height-40, $this->grade, 0xFF0000);
				/*
			if($this->grade[0]=='F')
			{
				$size=100;
				$x=40;//$width/2;
				$y=$height-40;
				
				
			}	*/
			create_dropshadow($im,$x,$y,$size,2,0xdddddd,$this->grade);
			imagettftext($im, $size, 0, $x,$y, $c, $root ."/img/verdanab.ttf",$this->grade);
		
			imagepng($im, $root .$grade_photo_rel);
						
		
			imagedestroy($im);
			
		}
		$this->photo_url_local=$grade_photo_rel;
	
	}
	public function get_local_page_url() {
		$url="/bio/$this->key";
		return $url;
	}	
	public function pdata_init()
	{
		global $g_debug;
		$this->office	=get_table("table_office")->getobj($this->key);
		$this->candidate= null; //get_table("table_election")->getobj($this->key);
        $this->titlename=$this->fullname;
		if($this->office)
		{
			
			if($this->office->chamber=='H')
			{
				$this->titlename="Representative ".$this->fullname;
				$chamber='House';
			}
			else 
			{
				$chamber='Senate';
				
				$this->titlename="Senator ".$this->fullname;
				
			}
			$uid=$this->office->uid;
			
			if(!$this->photo)
					$this->photo="http://www.ncga.state.nc.us/$chamber/pictures/$uid.jpg";
			
			$this->phone=$this->office->offphone;
			$this->website=NULL;
				
			$this->email=$this->office->email;
		}
        else
        {
            if(get_table("table_election")->is_running("2016",false,$this->key))
                $this->titlename="Candidate " . $this->fullname;


        }


		$this->get_photo_url();
		// *** GRADE ****

		$pointstotal=0;
		$score=0;
		$points=get_table("vote_data")->get_votes($this->key,$pointstotal);
		$this->points=$points;
		$this->pointstotal=$pointstotal;
		if($pointstotal)
			$score=$points/$pointstotal;
		$this->score=$score;

				
		if($this->grade)
		{
			$this->score=get_score_from_grade($this->grade);
			$this->points= "forced " .  $this->points;
		}		
		else
		{
			if( $pointstotal==0)
			{
				$this->grade='Ungraded';
				$this->gradecomment='Not enough information yet to assign grade';
			}
			else
			{
				$this->grade=get_grade($score);
			}
		}
		$this->make_grade_photo();


		
	}
	

	public function print_table_row($label, $val,$color=null) {
		$style="";
		if($color)
			$style="style='color:$color;font-weight:bold'";
			
		echo "<tr><td class='leg_label'>$label: </td><td class='leg_val' $style>$val</td></tr>";
	}	
	public function printPage() {
		$this->print_list_row('page');
	}

	
	public function print_list_row($mode='list') {
		global $root;
		global $g_debug;
		global $g_admin;
		global $g_flag_showscore;
		
		
		global $fb_domain;
		$endorsed=false;
		$class='leg_bio';
		$races=get_table('table_election')->getlist(false,false,"2016",false,false,$this->key);

     	$race_pri=get_table('table_election')->getlist(false,false,"2016","pri",false,$this->key);
		$race_gen=get_table('table_election')->getlist(false,false,"2016","gen",false,$this->key);

		$data_key=$this->key;
	
		echo ("<div class='$class' data-name='$data_key'><hr>");
		//thumbnail
	
		if($this->photo_url_local)
		{
	
			echo ("<div class='leg_thumb' ><a href='/bio/$this->key'>");
			echo ("<img src='$this->photo_url_local'/></a></div>");
		}
		else {
			echo ("<div class='leg_thumb' ><img src='/img/unknown.png'/></div>");
	
		}
	
		echo ("<div class='leg_info' ><a href='/bio/$this->key'><h2>$this->titlename</h2></a>");
        foreach ( $races as $r ) {
            if($r->endorsements=='Y')
            {
                $endorsed=true;
                break;
            }
        }
		if($endorsed)
		{
				echo("<img class='endorse' style='width:40px' src='/img/endorse_small.png'><span style='color:blue'>NCVAW Endorsed</h4>");
		}



		echo("<table><tr><td/><td/></tr>");
		if($mode=='list')
		{
			$this->print_table_row ( 'Profile Page', "<a title='Click here for profile page'   href='/bio/$this->key'>Click here for profile page</a>" );
			
			
		}
//2016 Election

        if(count($races)==0)
        {
            $this->print_table_row('2016 Election', "Not running, or not yet filed ");

        }
        else
		{
			$d= ($r->chamber  == 'H'? 'House': 'Senate' ) . " district #" . $r->district;


			$district_url=get_district_url($r->chamber,$r->district);

			$link=  "<a href=$district_url>". $d. "</a>" ;
			if($race_gen)
			{
				$this->print_table_row('2016 General Election', $link);
			}
			else
				if($race_pri)
				{
					$this->print_table_row('2016 Primary', $link);
				}
		}



//GRADES

		if($this->grade)
            if(($this->office) || ($this->grade!='Ungraded'))
		{
			$f='normal';
			$c=get_grade_color($this->grade,$f);
			$gradecolor=toColor($c);
			$grade_link="<a title='Click for profile'  style='font-weight:$f;color:$gradecolor' href='/bio/$this->key'>$this->grade</a>";
			if($g_flag_showscore)
			{
				$grade_link=$grade_link. '('.$this->points .'/'.$this->pointstotal . ')';
			
			}
			
			$this->print_table_row ( 'Grade',$grade_link );

            if($mode=='page')
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
			$district_url=get_district_url($chamberId,$dist);
			print_table_row ( 'District', "<a href=$district_url>$chamber # $dist</a>" );
			
			
				
		
		}
		else {
			
		}	

		print_table_row ( 'Party', get_party($this->party ));
        $responded="No";
        if(get_table ( "table_survey" )->check($this->key))
            $responded="<a style='color:green;font-weight:bold;' href='/bio/$this->key'>Yes</a>";
        
     
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
        if($mode=='page') {
            if ($this->email)
                $this->print_table_row('Email', "<a  href='mailto:" . $this->email . "'>" . $this->email . "</a>");
            if ($this->phone)
                $this->print_table_row('Phone', $this->phone);
        }
		if($this->office)
			print_table_row ( 'NCGA website', "<a  target='_blank' href='" . $ncleg_url . "'>Click here for NCGA page</a>" );
		
		$shareurl=urlencode($fb_domain.'/bio/'.$this->key);
		if($mode=='page')
		   echo("<tr><td></td><td><a href='https://www.facebook.com/sharer/sharer.php?u=$shareurl' target='_blank'><img style='display:inline;width:80px;' src='/img/fb-share-button.png'/></a></td></tr>");
		
		echo ('</table>');
			
		echo ("</div></div><div style='clear:both'></div>");
	}

    public function print_list_votes()
    {
        if(get_table("vote_data")->print_list_votes($this->key,0)==0)
        {

            echo("<div>No votes yet recorded</div>");
        }
    }
    public function print_list_sponsorship()
    {
        if(get_table("vote_data")->print_list_votes($this->key,1)==0)
        {

            echo("<div>No bills sponsored</div>");
        }
    }
    public function print_survey() {
        get_table("survey_data")->printresp($this->key);
    }
}

class table_person  extends table_base
{
	function get_columns()
	{
		return ['key','fullname','party','gender','grade','gradecomment','addr','city','state','zip','phone','email','photo','facebook','website'];
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

	public function get_ncleg_link() {
		return ("http://www.ncga.state.nc.us/gascripts/members/viewMember.pl?sChamber=$this->chamber&nUserID=$this->uid");
	}


	public function DELETE_print_list_row() {
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
		return ['key','chamber','uid','party','district','email','offphone','offaddr','offaddr2','offzip'];
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

