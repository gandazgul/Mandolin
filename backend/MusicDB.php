<?php

class MusicDB 
{
	private $dbfilepath;
	private $dbh;
	private $sngCount;
	
	function __construct($dbfilepath)
	{
		$this->dbfilepath = $dbfilepath;
		$this->dbh = new PDO("sqlite:$dbfilepath");
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	function __destruct()
	{
		$this->dbh = null;
	}
	
	//----------------------------------------------GET ARTIRSTS------------------------------------------------------------------
	function getArtists()
	{
		$query = $this->dbh->query("SELECT * FROM artists ORDER BY `art_name`");			
		$queryArr = $query->fetchAll();
		//print_r($queryArr);		
		$artArr = array();
		for ($i = 0; $i < count($queryArr); $i++)
		{
			$artArr[] = array("id" => $queryArr[$i]["art_id"], "name" => $queryArr[$i]["art_name"]);
		}
		//print_r($artArr);
		return $artArr;
	}
	
	function getArtists_json()
	{
		
		return json_encode($this->getArtists());
	}
	
	//----------------------------------------------GET ALBUMS------------------------------------------------------------------	
	function getAlbums($art_id)
	{
		$albArr = array();	
		$tok = strtok($art_id, "|");
		while($tok !== false)
		{
			$query = $this->dbh->query("SELECT alb_id, alb_name FROM albums WHERE `alb_art_id`='$tok' ORDER BY `alb_name`");
			$queryArr = $query->fetchAll();
			//print_r($queryArr);
			for($i = 0; $i < count($queryArr); $i++)
			{
				$albArr[] = array("id" => $queryArr[$i]["alb_id"], "name" => $queryArr[$i]["alb_name"]);
			}
			$tok = strtok("|");
		}//from the while
		
		return $albArr;
	}
	
	function getAlbums_json($art_id)
	{
		return json_encode($this->getAlbums($art_id));
	}
	
	//----------------------------------------------GET SONGS------------------------------------------------------------------
	function getSongs($alb_id)
	{
		$sngArr = array();	
		$tok = strtok($alb_id, "|");
		while($tok !== false)
		{
			$query = $this->dbh->query("SELECT song_id, song_name FROM music WHERE `song_album`='$tok' ORDER BY `song_name`");
			$queryArr = $query->fetchAll();
			//print_r($queryArr);
			for($i = 0; $i < count($queryArr); $i++)
			{
				$sngArr[] = array("id" => $queryArr[$i]["song_id"], "name" => $queryArr[$i]["song_name"]);
			}
			$tok = strtok("|");
		}//from the while
		
		return $sngArr;
	}
	
	function getSongs_json($alb_id)
	{
		return json_encode($this->getSongs($alb_id));
	}
	
	//-----------------------------------------------------GET TOTALS----------------------------------------------------------------------
	function getTotals_json()
	{
		$queries = array();
		$queries[] = "SELECT COUNT(art_id) FROM artists";
		$queries[] = "SELECT COUNT(alb_id) FROM albums";
		$queries[] = "SELECT COUNT(song_id) FROM music";
		
		$resultArr = array();
		
		for ($i = 0; $i < 3; $i++)
		{
			$query = $this->dbh->query($queries[$i]);
			$queryArr = $query->fetchAll();
			$resultArr[] = $queryArr[0][0];		
		}
		
		return json_encode($resultArr);
	}
	
	//------------------------------------------------------------ ADD Folder to DB --------------------------------------------------------
	function _addToDB($folder, $root_length) 
	{
		$extArr = array("mp3", "ogg", "flac", "wma", "mp4", "php"); //m4a is itunes witchery. CONVERT YOUR FILES TO OGG. thank you.
		if (substr($folder, -1) != '/')
		{
			$folder .= '/';
		}
		
		$songStmt = $this->dbh->prepare("INSERT INTO music(song_id, song_path, song_name, song_ext, song_album, song_art) VALUES (:song_id, :song_path, :song_name, :song_ext, :alb_id, :art_id)");
		$artStmt = $this->dbh->prepare("INSERT INTO artists(art_name) VALUES (?)");
		$albStmt = $this->dbh->prepare("INSERT INTO albums(alb_name, alb_art_id) VALUES (?, ?)");
		
		$dirH = opendir($folder);
		if(!$dirH)
			echo ("FATAL ERROR: Can't read the root directory($folder)\n");
		else
		{
			while (($file = readdir($dirH)) !== false)
			{
				if (($file == ".") or ($file == ".."))
					continue;
				//echo $file."\n";	
				if (is_dir($folder.$file))
				{
					$this->_addToDB($folder.$file, $root_length);
				}
				else
				{
					$ext = substr($file, strrpos($file, '.') + 1);
					//echo $ext."\n";
					if (array_search($ext,  $extArr) != 0)
					{
			        	$song_path = $folder.$file;
			        	$song_name = substr($file, 0, strpos($file, '.'));
						$song_ext = substr($file, strrpos($file, '.') + 1);
						$sng_path_no_root = substr($song_path, $root_length, (strlen($song_name.$song_ext) + 1) * -1);
						//echo $sng_path_no_root."\n";
						$pathArr = explode("/", $sng_path_no_root);
						//print_r($pathArr);
						if (isset($pathArr[0])) $artist = $pathArr[0]; else $artist = ""; 
						if (isset($pathArr[1])) $album = $pathArr[1]; else $album = "";
						//echo "art: $artist, alb: $album\n";
						
						$art_id = 0;
						$alb_id = 0;
						if ($artist != '')
						{
							try
							{
								$artStmt->execute(array($artist));
								$query = $this->dbh->query("SELECT count(art_name) FROM artists");
								$art_id = $query->fetchAll();
								//print_r($art_id);
								$art_id = $art_id[0][0] - 1;
								try
								{
									$albStmt->execute(array('unknown', $art_id));
									
								}
								catch (PDOException $e)
								{
									exit($e->getMessage());
								}
							}
							catch (PDOException $e)
							{
								if ($e->getCode() == 23000)
								{
									$query = $this->dbh->query("SELECT art_id FROM artists WHERE art_name='$artist'");
									$art_id = $query->fetchAll();
									$art_id = $art_id[0][0];
								}
								else
									exit($e->getMessage());
							}
							$query = $this->dbh->query("SELECT count(alb_name) FROM albums");
							$alb_id = $query->fetchAll();
							//print_r($alb_id);
							$alb_id = $alb_id[0][0] - 1;
						}

						if ($album != '')
						{
							//echo $album;
							$query = $this->dbh->query("SELECT alb_id FROM albums WHERE alb_name='$album' AND alb_art_id=$art_id");
							$alb_id = $query->fetchAll();
							//print_r($alb_id);
							if (count($alb_id) == 0)
							{
								try
								{
									$albStmt->execute(array($album, $art_id));
									$query = $this->dbh->query("SELECT count(alb_name) FROM albums");
									$alb_id = $query->fetchAll();
									//print_r($alb_id);
									$alb_id = $alb_id[0][0] - 1;
								}
								catch (PDOException $e)
								{
									exit($e->getMessage());
								}
							}
							else
							{
								//print_r($alb_id);
								$alb_id = $alb_id[0][0];
							}
						}
						
						$paramArr = array(':song_id' => sha1($folder.$file), ':song_path' => $song_path, 
											':song_name' => $song_name, ':song_ext' => $song_ext, ':art_id' => $art_id, ':alb_id' => $alb_id);
						//print_r($paramArr);
						try
						{
							$songStmt->execute($paramArr);
							$this->sngCount++;
							echo "<script language=\"javascript\">document.getElementById('sng').innerHTML = $this->sngCount;</script>\n";
						}
						catch (PDOException $e)
						{
							exit($e->getMessage());
						}
					}//EXT not recognized
			    }//this is a file
			}//while
			closedir($dirH);
		}//opendir
	}//function

	function addToDB($folder, $root_length)
	{
		if ($folder == "") return;
		
		$this->sngCount = 0;
		
		$this->_addToDB($folder, $root_length);
		
		echo "<p style='font-size: 16px; color: red; '>DONE</p>";
	}
}

?>