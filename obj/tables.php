<?php
/*
 * bill_list - tab 1- 
 * canidates - tab 2-
 * leg_list  - tab 3-
 * districts - tab 4
 * vote_data - tab 5-
 * links 	 - tab 6-
 * 
 * 
 * 
 */

$g_table_array=array();

function get_table($type) {
	global $root;
	global $g_table_array;
	$table=null;

	if(!array_key_exists($type,$g_table_array))
	{
		if (class_exists($type)) 
		{
			$filename=$root."/data2/$type.pdata";
			if( file_exists ( $filename ))
			{
				$data = file_get_contents($filename);
				$table = unserialize($data);
			}
			else {
				$table=new $type();
				$table->create_from_spreadsheet();
				$data=serialize($table);
				file_put_contents($filename, $data);				
			}
			$g_table_array[$type]=$table;
            
		}
		else {
			throw new Exception("Invalid product type given.");
		}
	}
    
	return $g_table_array[$type];
}



function getj(&$row,$id)
{
	return $row->{	'gsx$'.$id }->{'$t' };
}
/*
 to get the feed id's 
 
 https://spreadsheets.google.com/feeds/worksheets/1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc/public/basic
 
  
 */
class table_base
{

	public $list;
	function create_from_spreadsheet()	
	{
	
	}
	/*
	https://spreadsheets.google.com/feeds/cells/
	SHEET-IDENTIFIER/
	SHEET_INDEX/
	public/basic?alt=json-in-script&callback=JSON_CALLBACK*/
	
	function get_json_data2($tab,$objname,$keyname=null,$spreadsheetid=null)
	{
		global $root;
		global $g_refresh_data;
		$refresh_data=$g_refresh_data;
		
		$this->list=array();
		$cn=get_class($this);
		$filename=$root."/data2/$cn.json";
		if(!$spreadsheetid)
		{
			$spreadsheetid = '1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc';
		}
		//https://spreadsheets.google.com/feeds/cells/1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc/o3dfmt2/public/basic?alt=json-in-script&callback=JSON_CALLBACK
		//$url = "https://spreadsheets.google.com/feeds/cells/$spreadsheetid/$tab/public/basic?alt=json-in-script&callback=JSON_CALLBACK";
		//$url = "https://spreadsheets.google.com/feeds/cells/$spreadsheetid/$tab/public/basic?alt=json";
		$url = "https://spreadsheets.google.com/feeds/list/$spreadsheetid/$tab/public/values?alt=json";
		if(! file_exists ( $filename ))
			$refresh_data=1;
		if($refresh_data)
		{
		
	        $file = file_get_contents ( $url );
			$fp = fopen ( $filename, 'w' );
			fwrite ( $fp, $file );
			fclose ( $fp );
		}
        $json = json_decode (  file_get_contents ( $filename ) );
        
		if($json)
			$jdata = $json->{'feed' }->{'entry' };
		else
		{
			throw new Exception("Could not get JSON data for $objname");
			return;
		}
		$index=2;
		foreach ($jdata as $row )
		{
           
			if($keyname)
			{
				$key =getj($row,$keyname);
				$this->list [$key] = new $objname ( $row,$index );
			}
			else
				$this->list [] = new $objname ( $row ,$index);
				
			$index++;
		
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
		$this->get_json_data2('oc8fqax','person','key');
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

