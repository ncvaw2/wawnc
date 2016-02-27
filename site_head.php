<?php


function option( $name)
{
	global $options;
	if(array_key_exists($name,$options))
		return $options[$name];
	return null;
}

$zs_foot_jsfile=["/inc/jquery.js","/inc/ncvaw.js","/inc/zs_menu.js"];
$zs_head_jsfile=array();
$zs_foot_script=array();
$funcs_init=["ncvaw_init()"];

function add_init_js($code)
{
	global $funcs_init;
	array_push($funcs_init,$code);
}

function getCookie( $name)
{
	if(array_key_exists($name,$_COOKIE))
		return $_COOKIE[$name];
	return null;
}

function getParam( $name)
{
	if(array_key_exists($name,$_GET))
		return $_GET[$name];
	return null;
}
function getUrlWithParam($newkey,$newvalue)
{
	$params=$_GET;
	$params[$newkey]=$newvalue;	
	$url=$_SERVER["SCRIPT_NAME"].'?';
	$addAnd=false;
	foreach($params as $key => $val)
	{
		if($addAnd) $url .='&';
		$url .=$key . '=' . $val;
		$addAnd=true;
	}
	return $url;
	
	
	
}
function setFlag( $name)
{
	$flag=getParam($name);
	if($flag)
	{
		if($flag=='on')
		{
			setcookie($name,'on',time()+90000000,'/');
		}	
		if($flag=='off')
		{
			setcookie($name,'',0,'/');
			$flag=0;
		}	
	}
	else
	{
		$flag=getCookie($name);
	}
	return $flag;
}
$meta_extra='';
$page_title='';

$fb_domain="http://elect.ncvaw.org";
$fb_link = $fb_domain.$_SERVER['REQUEST_URI'];
$fb_title = null;
$fb_image= $fb_domain."/img/NCVAW_logo_fb.jpg";
$fb_meta_images="";
$fb_description="Find your 2016 Voting district and make sure your NC legislators are fighting for animal welfare.";

date_default_timezone_set('US/Eastern');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes


$current_url =$_SERVER['PHP_SELF'];

$g_refresh_data=getParam("refresh");
if(getParam("refresh_elect"))
{
	array_map('unlink', ["$root/data2/table_election.json","$root/data2/table_election.pdata"]);
}

if(getParam("deldata"))
{
    $url=$_SERVER["SCRIPT_NAME"];
	array_map('unlink', glob("$root/data/*.json"));
	array_map('unlink', glob("$root/data/*.pdata"));
	array_map('unlink', glob("$root/data2/*.json"));
	array_map('unlink', glob("$root/data2/*.pdata"));
    $meta_extra="<meta http-equiv='refresh' content='0; url=$url'>";
}
if(getParam("delpdata"))
{
    $url=$_SERVER["SCRIPT_NAME"];
	array_map('unlink', glob("$root/data/*.pdata"));
	array_map('unlink', glob("$root/data2/*.pdata"));
    $meta_extra="<meta http-equiv='refresh' content='0; url=$url'>";
}

$header=$root.'/inc/head.php';
$footer=$root.'/inc/foot.php';
set_include_path(get_include_path() . PATH_SEPARATOR. $root. '/lib');
//TODO make this an array
$g_flag_showscore=setFlag('showscores');
$g_admin=setFlag('admin');
if($g_admin)
	$g_flag_showscore=true;
	
$g_debug=setFlag('debug');
$g_offline=setFlag('offline');
$g_electionMode=false;


?>