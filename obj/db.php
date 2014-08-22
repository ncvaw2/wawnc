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
	
	function get_json_data2($tab,$spreadsheetid=null)
	{
		global $root;
		global $refresh_data;
	
		$this->list=array();
		$cn=get_class($this);
		$filename=$root."/data2/$cn.json";
		if(! file_exists ( $filename ))
			$refresh_data=1;
		if($refresh_data)
		{
			if(!$spreadsheetid)
			{
				$spreadsheetid = '1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc';
			}
		
			$url = "https://spreadsheets.google.com/feeds/cells/$spreadsheetid/$tab/public/basic?alt=json-in-script&callback=JSON_CALLBACK";

			$file = file_get_contents ( $url );
			$fp = fopen ( $filename, 'w' );
			fwrite ( $fp, $file );
			fclose ( $fp );
		}
	}

}
class table_person  extends table_base 
{
	function create_from_spreadsheet()
	{
		$this->get_json_data2('203963372','person','key');
	}	

}

?>

