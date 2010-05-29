<?php
	session_name("Mandolin");
	session_start();
?>

var SID = '<?php echo sha1(session_id()); ?>';

<?php
echo "\n\n//---------------------------------------------------- JQUERY ----------------------------------------------------------------------\n\n";
readfile('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
echo "\n\n//---------------------------------------------------- JQUERY UI ----------------------------------------------------------------------\n\n";
readfile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js');
echo "\n\n//---------------------------------------------------- MAIN.JS ----------------------------------------------------------------------\n\n";
include_once './main.js';
if (!isset ($_GET['p'])) exit();

if (($_GET['p'] != 'login') and ($_GET['p'] != 'checkAuth') and ($_GET['p'] != 'about'))
{
	echo "\n\n//---------------------------------------------------- ".$_GET['p'].".js ----------------------------------------------------------------------\n\n";
	include_once './'.$_GET['p'].'.js';
}
switch ($_GET['p'])
{
	case 'music':
	case 'movies':
	{
		echo "\n\n//---------------------------------------------------- CONTEXT MENU PLUGIN ----------------------------------------------------------------------\n\n";
		include_once './lib/jquery.contextMenu.js';
		break;
	}
	case 'adm':
	{
		echo "\n\n//---------------------------------------------------- TABLE SORTER PLUGIN ----------------------------------------------------------------------\n\n";
		include_once './lib/jquery.tablesorter.min.js';
		echo "\n\n//---------------------------------------------------- JSON LIB ----------------------------------------------------------------------\n\n";
		include_once './lib/json2.min.js';
		echo "\n\n//---------------------------------------------------- AJAX UPLOAD LIB ----------------------------------------------------------------------\n\n";
		include_once './lib/ajaxupload_min.js';
		echo "\n\n//---------------------------------------------------- JQUERY TEMPLATES ----------------------------------------------------------------------\n\n";
		include_once './lib/jquery-jtemplates.min.js';
		break;
	}
	case 'playlists':
	{
		echo "\n\n//---------------------------------------------------- JSON LIB ----------------------------------------------------------------------\n\n";
		include_once './lib/json2.min.js';
		echo "\n\n//---------------------------------------------------- CONTEXT MENU PLUGIN ----------------------------------------------------------------------\n\n";
		include_once './lib/jquery.contextMenu.js';
		break;
	}

}
?>
