<?php
/**
  UA for Search Bots Copyright 2013 Adrian Vender - @adrianvender
**/


/** Is this a bot? **/

// load bot config
require_once('botconfig.php');

// Get the user agent and attempt to match it with an item in the $bots array
$userAgent=$_SERVER['HTTP_USER_AGENT'];
$botname="";
foreach( $bots as $pattern => $bot ) {
  if ( preg_match( '#'.$pattern.'#i' , $userAgent ) == 1 )
  {
    $botname = preg_replace ( "/\\s{1,}/i" , '-' , $bot );
    break;
  }
}

//Exit GA for Search Bots script if no identified botname exists
if($botname=="") {
  return;
}

/** Yes, it's a bot. Let move forward **/


/** Basic Variable Setup **/

//Setup the UA parameters array
$uaParams =  array();

//Set the required Protocol Version
$uaParams['v'] = 1;

//Set the UA accound id
$uaParams['tid'] = $UA_SB_ACCOUNT_ID;

/** End Basic Variable Setup **/

  
// Generate UUID v4 function - needed to generate a CID when one isn't available
// Credits for this function goes to Stu Miller - http://www.stumiller.me/implementing-google-analytics-measurement-protocol-in-php-and-wordpress/
function gaGenUUID() {
  return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    // 32 bits for "time_low"
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

    // 16 bits for "time_mid"
    mt_rand( 0, 0xffff ),

    // 16 bits for "time_hi_and_version",
    // four most significant bits holds version number 4
    mt_rand( 0, 0x0fff ) | 0x4000,

    // 16 bits, 8 bits for "clk_seq_hi_res",
    // 8 bits for "clk_seq_low",
    // two most significant bits holds zero and one for variant DCE1.1
    mt_rand( 0, 0x3fff ) | 0x8000,

    // 48 bits for "node"
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
  );
}


function getUACookie() {
  if(isset($_COOKIE['__uasearchcid'])) {
    return $_COOKIE['__uasearchcid'];
  } else {
    $newcid = gaGenUUID();
    setcookie('__uasearchcid',$newcid,time() + (86400 * 365 * 2)); // 86400 = 1 day
    return $newcid;
  }
}


// Sends a collection hit to the GA servers
function uaCollectHit($utmUrl) {
  $cu = curl_init();
  curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($cu, CURLOPT_URL, $utmUrl);
  $uaResult = curl_exec($cu);
  curl_close($cu);
}

/** Set the parameters **/

  
// Set a random 'cid'
$uaParams['cid'] = getUACookie();

// Set the hit type to pageview
$uaParams['t'] = 'pageview';

// Get the hostname
$domainName = $_SERVER["SERVER_NAME"];
if ($domainName != '') {
  $uaParams['dh'] = $domainName;
}

// Get the URI of the page
$documentPath = $_SERVER["REQUEST_URI"];
if (empty($documentPath)) {
  $documentPath = "";
} else {
  $documentPath = $documentPath;
}
$uaParams['dp'] = $documentPath;

// Get the referrer from the utmr parameter.
$documentReferer = $_SERVER["HTTP_REFERER"];
if (empty($documentReferer) && $documentReferer !== "0") {
  $documentReferer = "-";
} else {
  $documentReferer = $documentReferer;
}
$uaParams['dr'] = $documentReferer;

// Set bot name as campaign source
$uaParams['cs'] = $botname;

// Set the campaign medium to 'bot'
$uaParams['cm'] = 'bot';

// Setup Cache Buster param to prevent caching of requests (using a unix timestamp)
$uaParams['z'] = time();
  


/**  Now let's prepare and send the data to GA **/

// The UA collection URL
$uaPostLocation = "http://www.google-analytics.com/collect";

// Set the parameters to a key=value payload
$theParamList = "";
foreach($uaParams as $key => $value) {
  $theParamList .= $key."=".$value."&";
}

// Construct the gif hit url.
$utmUrl = $uaPostLocation . "?" .$theParamList;

// Finally send the data to GA
uaCollectHit($utmUrl);

/** el fin **/
?>
