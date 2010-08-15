<?php
require_once dirname(__FILE__).'/settings.php';
require_once dirname(__FILE__).'/result.php';

/**
 * Model to query amazon and get album art
 *
 * @author gandazgul
 */
class AmazonModel
{
	private $dbh;
	private $result;

	function __construct()
	{
		global $settings;

		$this->result = new Result();

		try
		{
			//$this->dbh = new PDO($settings->get("dbDSN"), $settings->get("dbUser"), $settings->get("dbPassword"), array(PDO::ATTR_PERSISTENT => true));
			$this->dbh = new PDO($settings->get("dbDSN"));
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
			$this->result->isError = true;
			$this->result->errorCode = $e->getCode();
			$this->result->errorStr = $e->getMessage();
		}
	}

	function __destruct()
	{
		unset($this->dbh);
		unset($this->result);
	}

	function getAlbumArt()
	{
		
	}
}
?>
