<?php
Class MoviesDB
{
	private $dbh;
	
	function __construct()
	{
		include "../config.php";

		try
		{
			$this->dbh = new PDO($settings["dbDSN"], $settings["dbUser"], $settings["dbPassword"], array(PDO::ATTR_PERSISTENT => true));
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
			die($e->getMessage());
		}
	}
	
	function __destruct()
	{
		unset($this->dbh);
	}
	
	function getMovies()
	{
		$movArr = array();
		$query = $this->dbh->query("SELECT DISTINCT category FROM movies ORDER BY `category`");
		$catArr = $query->fetchAll();
		//print_r($catArr);
		$movStmt = $this->dbh->prepare("SELECT title, mID FROM movies WHERE `category`=?");
		for($i = 0; $i < count($catArr); $i++)
		{
			$category = $catArr[$i][0];
			$movStmt->execute(array($category));
			$movStmtArr = $movStmt->fetchAll();
			$movies[$i][] = $category;
			for ($j = 0; $j < count($movStmtArr); $j++)
			{	
				$movies[$i][] = array("id" => $movStmtArr[$j]["mID"], "title" => $movStmtArr[$j]["title"]);
			}
		}
		
		return $movies;
	}
	
	function getMovies_json()
	{
		return json_encode($this->getMovies());
	}
	
	function getMovieEmbedCode($id, $musicURL)
	{
		echo "<embed src='./client/jwPlayer.swf' width='512' height='404' type='application/x-shockwave-flash' 
				pluginspage='http://www.macromedia.com/go/getflashplayer' 
				bgcolor='#FFFFFF' 
				name='theMediaPlayer' 
				allowfullscreen='true' 
				flashvars='file=".$musicURL."server/vstream.php?k=".$_SESSION["key"]."&amp;s=$id&.flv'
			  </embed>";
	}
}