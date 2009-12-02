<?php
	session_name("Mandolin");
	session_start();
	
	$_SESSION = array();
	unset($sess_id);

	if (isset($_COOKIE["Mandolin"])) 
	{
	    //setcookie($sName, '', time()-42000, '/');
		setcookie(session_name(), session_id(), 1, '/');
		session_destroy();
		header("Location: .");
	}
	else
		echo "There was a problem with the session handling. Reload the page.";
?>