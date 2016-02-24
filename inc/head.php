<!DOCTYPE html>
<html>
<head>

<meta name="viewport"
	content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="www.ncvaw.org" />



<!-- FACEBOOK  -->
<meta property="fb:app_id" content="556863147812162" />
<meta property="og:site_name" content="NC Voters For Animal Welfare" />
<meta property="og:type" content="Website" />

<?php
$fb_description=str_replace ( "\"", "&quot;", $fb_description ) ;

if ($fb_description)
	echo ("<meta property='og:description' content='$fb_description'/>");

if (! $fb_meta_images)
	$fb_meta_images = "<meta property='og:image' content='$fb_image'/>";
echo ("$fb_meta_images");
echo ("$meta_extra");

if($fb_title==null)
	$fb_title=$page_title;
?>	
	
<link href='/inc/ncvaw.css' rel='stylesheet' type='text/css'>
<link href='/inc/zs_menu.css' rel='stylesheet' type='text/css'>
<title>
<?php
if ($page_title)
	echo ("NCVAW - $page_title");
else
	echo ("NC Voters For Animal Welfare");
?></title>
<meta property="og:title" content="<?php echo $fb_title ?>" />
    
<?php
include $root . '/inc/headscripts.p';
include $root . '/inc/zs_menu.php';
?>
</head>

<body>
	<div id="wrapper">
		<div style="text-align: center;">
			<a style="margin:5px" href="/home.html">
			<img style="float: left; max-width: 25%;" src="/img/NCVAW_paw.png"
				alt="North Carolina Voters for Animal Welfare"></a>
			<a style="margin: 5px" href="/home.html"> <img
				style="text-align: center; margin: auto; max-width: 40%"
				src="/img/title.png"></a> <a href="http://www.facebook.com/NCVAW"
				target="_new"> <img style="float: right; max-width: 25%"
				src="/img/facebook.png" alt="Follow us on Facebook"></a>

		</div>
		<div style='clear: both'></div>
  	
<?php
$menu_admin = 0;

if ($g_admin) {
	$menu_admin = z_menu ( "Admin", [ 
			z_mi ( "Admin Off", "/home.html?admin=off", "" ) 
	] );
}
make_menu ( [ 
		z_menu ( "About", [ 
				z_mi ( "About Us", "http://www.ncvaw.org/about/", "HOME" ),
				z_mi ( "Who We Are", "http://www.ncvaw.org/about/our-board/", "MAP" ),
				z_mi ( "Contact", "http://www.ncvaw.org/about/contact-us/", "MAP" ),
				z_mi ( "Lobbying 101", "/about/lobbying101.html", "" ),
				z_mi ( "How You Can Help", "/about/getinvolved.html", "" ) 
		] ),
		z_mbi ( "News", "http://www.ncvaw.org", "Support Animal Welfare in NC", 0 ),
		z_mbi ( "Facebook Feed", "/facebook.html", "Support Animal Welfare in NC", 0 ),
		z_mbi ( "Donate", "http://www.ncvaw.org/donate/", "Support Animal Welfare in NC", 0 ),
		
		z_menu ( "Your Voting Districts", [
				z_mi ( "Find your Districts", "/guide/find.html", "" ),
				z_mi ( null, "", "", "z_mi_dist_senate" ),
				z_mi ( null, "", "", "z_mi_dist_house" ),
				z_mi ( "List of all Districts", "/districts.php", "" ) 
		] ),
		z_menu ( "Legislature Guide", [ 
				z_mi ( "District Lists", "/districts.php", "" ),
				z_mi ( "Recent Legislation", "/bills.php", "" ),
				z_mi ( "Senate List", "/guide/leglist.html?ch=S", "HOME" ),
				z_mi ( "House List", "/guide/leglist.html?ch=H", "" ),
				z_mi ( "Complete List", "/guide/leglist.html", "" ) 
		] ),
		
		z_menu ( "Voters Guide", [ 
				
				z_mi ( "Find your Voting Districts", "/guide/find.html", "" ),
				z_mi ( "In The News", "/links.php", "" ),
				z_mi ( "Recent Legislation", "/bills.php", "" ),
				z_mi ( "Survey", "/guide/survey.html", "" ) 
		] ),
		($g_debug ? z_menu ( "Debug", [ 
				z_mi ( "Debug objs", "/guide/debug.html" ),
				z_mi_param ( "Admin Off", "admin", "off" ),
				z_mi_param ( "Debug Off", "debug", "off" ),
				
				z_mi_param ( "Debug ON", "debug", "on" ),
				
				z_mi_param ( "offline", "offline", "on" ),
				z_mi_param ( "online", "offline", "off" ),
				z_mi_param ( "Delete Pdata", "delpdata", "true" ),
				z_mi_param ( "Delete all", "deldata", "true" ) 
		] ) : 0),
		($g_admin ? z_menu ( "Admin", [ 
				z_mi_param ( "Reload from spreadsheets", "deldata", "true" ),
				z_mi_param ( "Admin Off", "admin", "off" ),
				z_mi_param ( "Debug ON", "debug", "on" ) 
		] ) : 0),
		($g_debug ? 

		z_menu ( "Tables", [
            z_mi ( "Person", "/v2/table.html?table=table_person" ),
            z_mi ( "Survey", "/v2/table.html?table=table_survey" ),
				z_mi ( "Election", "/v2/table.html?table=table_election" ),
				z_mi ( "Office", "/v2/table.html?table=table_office" ),
				z_mi ( "People List", "/v2/bio.php", "" ) 
		] ) : 0) 
] );

?>
		<div id="main">