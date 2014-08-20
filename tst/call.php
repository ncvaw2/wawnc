<?php

require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata
 */
Zend_Loader::loadClass('Zend_Gdata');

Zend_Loader::loadClass('Zend_Gdata_AuthSub');

Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Docs');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');


/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using the ClientLogin credentials supplied.
 *
 * @param  string $user The username, in e-mail address format, to authenticate
 * @param  string $pass The password for the user specified
 * @return Zend_Http_Client
 */
function getClientLoginHttpClient($user, $pass)
{
  $service = Zend_Gdata_Docs::AUTH_SERVICE_NAME;
  $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
  return $client;
}

// ************************ BEGIN CLI SPECIFIC CODE ************************

/**
 * Display list of valid commands.
 *
 * @param  string $executable The name of the current script. This is usually available as $argv[0].
 * @return void
 */


/**
 * Displays the titles for the Google Documents entries in the feed. In HTML
 * mode, the titles are links which point to the HTML version of the document.
 *
 * @param  Zend_Gdata_Docs_DocumentListFeed $feed
 * @param  boolean                          $html True if output should be formatted for display in
 *                                          a web browser
 * @return void
 */
function printDocumentsFeed($feed, $html)
{
  if ($html) {echo "<ul>\n";}

  // Iterate over the document entries in the feed and display each document's
  // title.
  foreach ($feed->entries as $entry) {

    if ($html) {
        // Find the URL of the HTML view of the document.
        $alternateLink = '';
        foreach ($entry->link as $link) {
            if ($link->getRel() === 'alternate') {
                $alternateLink = $link->getHref();
            }
        }
        // Make the title link to the document on docs.google.com.
        echo "<li><a href=\"$alternateLink\">\n";
    }

    echo "$entry->title\n";

    if ($html) {echo "</a></li>\n";}
  }

  if ($html) {echo "</ul>\n";}
}

/**
 * Obtain a list of all of a user's docs.google.com documents and print the
 * titles to the command line.
 *
 * @param  Zend_Gdata_Docs $client The service object to use for communicating with the Google
 *                                 Documents server.
 * @param  boolean         $html   True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveAllDocuments($client, $html)
{
  if ($html) {echo "<h2>Your documents</h2>\n";}

  $feed = $client->getDocumentListFeed();

  printDocumentsFeed($feed, $html);
}

/**
 * Obtain a list of all of a user's docs.google.com word processing
 * documents and print the titles to the command line.
 *
 * @param  Zend_Gdata_Docs $client The service object to use for communicating with the Google
 *                                 Documents server.
 * @param  boolean         $html   True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveWPDocs($client, $html)
{
  if ($html) {echo "<h2>Your word processing documents</h2>\n";}

  $feed = $client->getDocumentListFeed(
      'https://docs.google.com/feeds/documents/private/full/-/document');

  printDocumentsFeed($feed, $html);
}

/**
 * Obtain a list of all of a user's docs.google.com spreadsheets
 * documents and print the titles to the command line.
 *
 * @param  Zend_Gdata_Docs $client The service object to use for communicating with the Google
 *                                 Documents server.
 * @param  boolean         $html   True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveSpreadsheets($client, $html)
{
  if ($html) {echo "<h2>Your spreadsheets</h2>\n";}

  $feed = $client->getDocumentListFeed(
      'https://docs.google.com/feeds/documents/private/full/-/spreadsheet');

  printDocumentsFeed($feed, $html);
}

/**
 * Obtain a list of all of a user's docs.google.com documents
 * which match the specified search criteria and print the titles to the
 * command line.
 *
 * @param  Zend_Gdata_Docs $client The service object to use for communicating with the Google
 *                                 Documents server.
 * @param  boolean         $html   True if output should be formatted for display in a web browser.
 * @param  string          $query  The search query to use
 * @return void
 */
function fullTextSearch($client, $html, $query)
{
  if ($html) {echo "<h2>Documents containing $query</h2>\n";}

  $feed = $client->getDocumentListFeed(
      'https://docs.google.com/feeds/documents/private/full?q=' . $query);

  printDocumentsFeed($feed, $html);
}


// ************************ BEGIN WWW SPECIFIC CODE ************************

/**
 * Writes the HTML prologue for this app.
 *
 * NOTE: We would normally keep the HTML/CSS markup separate from the business
 *       logic above, but have decided to include it here for simplicity of
 *       having a single-file sample.
 *
 *
 * @param  boolean $displayMenu (optional) If set to true, a navigation menu is displayed at the top
 *                              of the page. Default is true.
 * @return void
 */
function startHTML($displayMenu = true)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Documents List API Demo</title>

    <style type="text/css" media="screen">
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: small;
        }

        #header {
            background-color: #9cF;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            padding-left: 5px;
            height: 2.4em;
        }

        #header h1 {
            width: 49%;
            display: inline;
            float: left;
            margin: 0;
            padding: 0;
            font-size: 2em;
        }

        #header p {
            width: 49%;
            margin: 0;
            padding-right: 15px;
            float: right;
            line-height: 2.4em;
            text-align: right;
        }

        .clear {
            clear:both;
        }

        h2 {
            background-color: #ccc;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            margin-top: 1em;
            padding-left: 5px;
        }

        .error {
            color: red;
        }

        form {
            width: 500px;
            background: #ddf8cc;
            border: 1px solid #80c605;
            padding: 0 1em;
            margin: 1em auto;
        }

        .warning {
            width: 500px;
            background: #F4B5B4;
            border: 1px solid #900;
            padding: 0 1em;
            margin: 1em auto;
        }

        label {
            display: block;
            width: 130px;
            float: left;
            text-align: right;
            padding-top: 0.3em;
            padding-right: 3px;
        }

        .radio {
            margin: 0;
            padding-left: 130px;
        }

        #menuSelect {
            padding: 0;
        }

        #menuSelect li {
            display: block;
            width: 500px;
            background: #ddf8cc;
            border: 1px solid #80c605;
            margin: 1em auto;
            padding: 0;
            font-size: 1.3em;
            text-align: center;
            list-style-type: none;
        }

        #menuSelect li:hover {
            background: #c4faa2;
        }

        #menuSelect a {
            display: block;
            height: 2em;
            margin: 0px;
            padding-top: 0.75em;
            padding-bottom: -0.25em;
            text-decoration: none;
        }
        #content {
            width: 600px;
            margin: 0 auto;
            padding: 0;
            text-align: left;
        }
    </style>

</head>

<body>

<div id="header">
    <h1>Documents List API Demo</h1>
    <?php if ($displayMenu === true) { ?>
        <p><a href="?">Main</a> | <a href="?menu=logout">Logout</a></p>
    <?php } ?>
    <div class="clear"></div>
</div>

<div id="content">
<?php
}

/**
 * Writes the HTML epilogue for this app and exit.
 *
 * @param  boolean $displayBackButton (optional) If true, displays a link to go back at the bottom
 *                                    of the page. Defaults to false.
 * @return void
 */
function endHTML($displayBackButton = false)
{
    if ($displayBackButton === true) {
        echo '<div style="clear: both;">';
        echo '<a href="javascript:history.go(-1)">&larr; Back</a></div>';
    }
?>
</div>
</body>
</html>
<?php
exit();
}

/**
 * Displays a notice indicating that a login password needs to be
 * set before continuing.
 *
 * @return void
 */
function displayPasswordNotSetNotice()
{
?>
    <div class="warning">
        <h3>Almost there...</h3>
        <p>Before using this demo, you must set an application password
            to protect your account. You will also need to set your
            Google Apps credentials in order to communicate with the Google
            Apps servers.</p>
        <p>To continue, open this file in a text editor and fill
            out the information in the configuration section.</p>
    </div>
<?php
}

/**
 * Displays a notice indicating that authentication to Google Apps failed.
 *
 * @return void
 */
function displayAuthenticationFailedNotice()
{
?>
    <div class="warning">
        <h3>Google Docs Authentication Failed</h3>
        <p>Authentication with the Google Apps servers failed.</p>
        <p>Please open this file in a text editor and make
            sure your credentials are correct.</p>
    </div>
<?php
}

/**
 * Outputs a request to the user to login to their Google account, including
 * a link to the AuthSub URL.
 *
 * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
 *
 * @param  string $linkText
 * @return void
 */
function requestUserLogin($linkText)
{
    $authSubUrl = getAuthSubUrl();
    echo "<a href=\"{$authSubUrl}\">{$linkText}</a>";
}

/**
 * Returns the AuthSub URL which the user must visit to authenticate requests
 * from this application.
 *
 * Uses getCurrentUrl() to get the next URL which the user will be redirected
 * to after successfully authenticating with the Google service.
 *
 * @return string AuthSub URL
 */
function getAuthSubUrl()
{
    $next = getCurrentUrl();
    $scope = 'https://docs.google.com/feeds/documents';
    $secure = false;
    $session = true;
    return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure,
        $session);
}

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using AuthSub authentication.
 *
 * Uses the $_SESSION['sessionToken'] to store the AuthSub session token after
 * it is obtained.  The single use token supplied in the URL when redirected
 * after the user succesfully authenticated to Google is retrieved from the
 * $_GET['token'] variable.
 *
 * @return Zend_Http_Client
 */
function getAuthSubHttpClient()
{
    global $_SESSION, $_GET;
    if (!isset($_SESSION['docsSampleSessionToken']) && isset($_GET['token'])) {
        $_SESSION['docsSampleSessionToken'] =
            Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
    }
    $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['docsSampleSessionToken']);
    return $client;
}

/**
 * Returns the full URL of the current page, based upon env variables
 *
 * Env variables used:
 * $_SERVER['HTTPS'] = (on|off|)
 * $_SERVER['HTTP_HOST'] = value of the Host: header
 * $_SERVER['SERVER_PORT'] = port number (only used if not http/80,https/443)
 * $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
 *
 * @return string Current URL
 */
function getCurrentUrl()
{
    global $_SERVER;

    /**
     * Filter php_self to avoid a security vulnerability.
     */
    $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0,
    strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

    if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    $host = $_SERVER['HTTP_HOST'];
    if ($_SERVER['SERVER_PORT'] != '' &&
        (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
        ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
            $port = ':' . $_SERVER['SERVER_PORT'];
    } else {
        $port = '';
    }
    return $protocol . $host . $port . $php_request_uri;
}

/**
 * Display the main menu for running in a web browser.
 *
 * @return void
 */
function displayMenu()
{
?>
<h2>Main Menu</h2>

<p>Welcome to the Google Documents List API demo page. Please select
    from one of the following three options to see a list of commands.</p>

    <ul id="menuSelect">
        <li><a class="menuSelect" href="?menu=list">List Documents</a></li>
        <li><a class="menuSelect" href="?menu=query">Query your Documents</a></li>
        <li><a class="menuSelect" href="?menu=upload">Upload a new document</a></li>
    </ul>

<p>Tip: You can also run this demo from the command line if your system
    has PHP CLI support enabled.</p>
<?php
}

/**
 * Log the current user out of the application.
 *
 * @return void
 */
function logout()
{
session_destroy();
?>
<h2>Logout</h2>

<p>Logout successful.</p>

<ul id="menuSelect">
    <li><a class="menuSelect" href="?">Login</a></li>
</ul>
<?php
}


/**
 * Processes loading of this sample code through a web browser.
 *
 * @return void
 */
function runWWWVersion()
{
    session_start();

    // Note that all calls to endHTML() below end script execution!

    global $_SESSION, $_GET;
    if (!isset($_SESSION['docsSampleSessionToken']) && !isset($_GET['token'])) {
        requestUserLogin('Please login to your Google Account.');
        return;
    }

    /*
    $user='ncvaw2@gmail.com';
    $pass='ncvawpass';
    
    $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
   
    $spreadsheetService = new Zend_Gdata_Spreadsheets($client);
    $feed = $spreadsheetService->getSpreadsheetFeed();
    
     */
    
    
    $client = getAuthSubHttpClient();
    $docs = new Zend_Gdata_Docs($client);
    startHTML();
   // retrieveSpreadsheets($docs, true);
    $spreadsheetKey = '0AonA9tFgf4zjdHhNd1FIeFJzVWRrdDlUangxWUlkTXc';
    $worksheetId='10';
    $query = new Zend_Gdata_Spreadsheets_CellQuery();
    $query->setSpreadsheetKey($spreadsheetKey);
    $query->setWorksheetId($worksheetId);
    $str=$query->getQueryString();
    echo("<p>$str</p>");
    $spreadsheetService = new Zend_Gdata_Spreadsheets($client);
    $cellFeed = $spreadsheetService->getCellFeed($query);
    foreach($cellFeed as $cellEntry) {
    	$row = $cellEntry->cell->getRow();
    	$col = $cellEntry->cell->getColumn();
    	$val = $cellEntry->cell->getText();
    	echo "$row, $col = $val\n";
    }
    
    retrieveSpreadsheets($docs, true);
    endHTML(true);
        
  

}


// ************************** PROGRAM ENTRY POINT **************************

    // running through web server
    try {
        runWWWVersion();
    } catch (Zend_Gdata_Gapps_ServiceException $e) {
        // Try to recover gracefully from a service exception.
        // The HTML prologue will have already been sent.
        echo "<p><strong>Service Error Encountered</strong></p>\n";
        echo "<pre>" . htmlspecialchars($e->__toString()) . "</pre>";
        endHTML(true);
    }

