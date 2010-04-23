<?php
require_once dirname(__FILE__).'/settings.php';
require_once dirname(__FILE__).'/result.php';

class AlbumsModel
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

		$this->art_id = $art_id;
	}

	function __destruct()
	{
		unset($this->dbh);
		unset($this->result);
	}

	//----------------------------------------------GET ALBUMS------------------------------------------------------------------
	function getAlbums()
	{
		$stmt = $this->dbh->prepare("SELECT alb_id, alb_name FROM albums WHERE alb_art_id=? ORDER BY alb_name");
		$artIDArr = explode("|", $this->art_id);
		foreach ($artIDArr as $art_id)
		{
			try//try the query
			{
				if ($stmt->execute(array($art_id)) === true)//if we got it
				{
					$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$this->result->data = array_merge($this->result->data, $data);
				}
				else
				{
					$this->result->isError = true;
					$error = $this->dbh->errorInfo();
					$this->result->errorCode = $error[1];
					$this->result->errorStr = $error[2];
					break;
				}
			}
			catch (PDOException $e)
			{
				$this->result->isError = true;
				$this->result->errorCode = $e->getCode();
				$this->result->errorStr = $e->getMessage();
				break;
			}
		}//from the foreach
		//print_r($this->result->data);
		return $this->result;
	}
}
?>
