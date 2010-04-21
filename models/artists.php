<?php
require_once dirname(__FILE__).'/settings.php';
require_once dirname(__FILE__).'/result.php';

class ArtistsModel
{
	private $dbh;
	private $result;
	public $art_id;

	function __construct($art_id)
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

		if (isset ($art_id))
		{
			$this->art_id = $art_id;
		}
	}

	function __destruct()
	{
		unset($this->dbh);
		unset($this->result);
	}

	//----------------------------------------------GET ARTIRSTS------------------------------------------------------------------
	function getArtists()
	{
		if (isset ($this->art_id))
		{
			//get info about this artist
		}
		else//else get a list of all artists
		{
			try
			{
				$query = $this->dbh->query("SELECT * FROM artists ORDER BY `art_name`");
				if ($query)
				{
					$queryArr = $query->fetchAll();
					$artArr = array();
					for ($i = 0; $i < count($queryArr); $i++)
					{
						$artArr[] = array("id" => $queryArr[$i]["art_id"], "name" => $queryArr[$i]["art_name"]);
					}

					$this->result->data = $artArr;
				}
				else
				{
					$this->result->isError = true;
					$error = $this->dbh->errorInfo();
					$this->result->errorCode = $error[1];
					$this->result->errorStr = $error[2];
				}
			}
			catch (PDOException $e)
			{
				$this->result->isError = true;
				$this->result->errorCode = $e->getCode();
				$this->result->errorStr = $e->getMessage();
			}
		}

		return $this->result;
	}
}
?>
