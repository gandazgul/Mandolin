<?php
	/* This code is the work of Andy Moore (http://www.andymoore.info/) and you can find the original here (http://www.andymoore.info/php-to-detect-mobile-phones/).
	 * Date Retrieved: 10/15/2009
	 * 
	 * Improvements made by: ronan
	 * Posted to: http://mobiforge.com/developing/story/lightweight-device-detection-php 
	 * The changes changes made from Andy's original version are:
	 * - Adding the W3C UA string (Default Delivery Context)
	 * - Specal case detection for Opera Mini
	 * - Catch-all exception for devices with Windows in the UA string (Opera 9 for Windows was being recognised as a mobile device) 
	 * 
	 * If anyone has any improvements on this code, or implementations for other languages, please let me know!
	 */
	$mobile_browser = '0';
	 
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) 
	{
	    $mobile_browser++;
	}
	 
	if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) 
	{
	    $mobile_browser++;
	}    
	 
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
	$mobile_agents = array(
	    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
	    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
	    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
	    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
	    'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
	    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
	    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
	    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
	    'wapr','webc','winw','winw','xda','xda-');
	 
	if(in_array($mobile_ua,$mobile_agents)) 
	{
	    $mobile_browser++;
	}
	
	if (isset($_SERVER['ALL_HTTP']) and (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0)) 
	{
	    $mobile_browser++;
	}
	 
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) 
	{
	    $mobile_browser = 0;
	} 

	require_once './models/Settings.php';
	$mainPage = $settings->get("mainPage");
	if (!file_exists("./client/$mainPage.php"))
	{
		exit("FATAL ERROR: The page configured in the settings as main ($mainPage) doesnt exist, plase correct this before using the application. Default Value: music");
	}

	try
	{
		$dbh = new PDO($settings->get("dbDSN"), $settings->get("dbUser"), $settings->get("dbPassword"), array(PDO::ATTR_PERSISTENT => true));
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if($mobile_browser > 0){ include('index_mobi.php'); }else{ include('index_pc.php'); }
		unset($dbh);
	}
	catch (PDOException $e)
	{
		die($e->getMessage());
	}
?>