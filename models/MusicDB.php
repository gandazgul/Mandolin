<?php
class MusicDB 
{
	private $dbh;
	private $sngCount;
	private $artCount;
	private $albCount;
	private $resultArr;
	private $plFormats;
	public $plFormatsMimeTypes;
	
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
		
		$this->resultArr = array();
		$this->resultArr['isError'] = false;
		$this->resultArr['resultStr'] = "";
		
		$this->plFormats['xspf']['head'] = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\">\n\t<trackList>\n";
		$this->plFormats['xspf']['track'] = "\t\t<track>\n\t\t\t<title>".'%3$d%1$s'."</title>\n\t\t\t<location>".'%2$s'."</location>\n\t\t</track>\n"; 
		$this->plFormats['xspf']['foot'] = "\t</trackList>\n</playlist>";
		$this->plFormats['xspf']['amp'] = "&amp;";
		$this->plFormats['m3u']['head'] = "#EXTM3U\n";
		$this->plFormats['m3u']['track'] = '#EXTINF:0,%3$d%1$s'."\n".'%2$s'."\n";
		$this->plFormats['m3u']['foot'] = "";
		$this->plFormats['m3u']['amp'] = "&";
		
		$this->plFormatsMimeTypes['m3u'] = "audio/x-mpegurl";
		$this->plFormatsMimeTypes['xspf'] = "application/xspf+xml";
	}
	
	function __destruct()
	{
		unset($this->dbh);
		unset($this->resultArr);
		unset($this->plFormats);
		unset($this->plFormatsMimeTypes);
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
	
	function getColumnsFromID($song_id, $columns)
	{
		$this->resultArr['isError'] = false;
		
		$columns = implode(',', $columns);
		$queryArr = $this->dbh->query("SELECT $columns FROM music WHERE song_id='$song_id'");
		$queryArr = $queryArr->fetchAll();
		if (count($queryArr) == 0)
		{
			$this->resultArr['isError'] = true;
			$error = $this->dbh->errorInfo();
			$this->resultArr['resultStr'] = "ERROR: Couldn't retreive the requested information: ".$error[2];
		}
		else
		{
			$this->resultArr['resultStr'] = $queryArr;
		}
		
		return $this->resultArr;
	}
	
	//-------------------------------------------------------Search-------------------------------------------------------------------------
	function search_json($queryStr)
	{
		$resultArr = array();
		$queryArr = array();
		
		$queries = array();
		$queries[] = "SELECT art_id, art_name FROM artists WHERE `art_name`  LIKE '%$queryStr%'";
		$queries[] = "SELECT alb_id, alb_name FROM albums WHERE `alb_name` LIKE '%$queryStr%'";
		$queries[] = "SELECT song_id, song_name, song_comments FROM music WHERE `song_name` LIKE '%$queryStr%'";
		$sections = array();
		$sections[] = "art";
		$sections[] = "alb";
		$sections[] = "sng";
		$attributes = array();
		$attributes[] = "id";
		$attributes[] = "name";
		$attributes[] = "comm";
		
		for ($section = 0; $section < 3; $section++)//go thru the 3 queries and sections
		{
			$queryArr = $this->dbh->query($queries[$section])->fetchAll();
			$curSection = $sections[$section];
			
			if (count($queryArr) != 0)// if we found something
			{
				for ($result = 0; $result < count($queryArr); $result++)//go thru all the results
				{
					for ($attr = 0; $attr < count($queryArr[$result]) / 2; $attr++)//go thru all the attributes in each result
					{
						$resultArr[$curSection][$result][$attributes[$attr]] = $queryArr[$result][$attr];
					}
				}
			}
			else
				$resultArr[$curSection] = array();
		}
	
		//print_r($resultArr);
		return json_encode($resultArr);
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
		$extArr = array("mp3", "ogg", "flac", "wma", "mp4", "ape", "php", "sys", "inf", "dll"); //m4a is itunes witchery. CONVERT YOUR FILES TO OGG. thank you.
		if (substr($folder, -1) != '/')	{ $folder .= '/'; }
		$songStmt = $this->dbh->prepare("INSERT INTO music(song_id, song_path, song_name, song_ext, song_album, song_art) VALUES (:song_id, :song_path, :song_name, :song_ext, :alb_id, :art_id)");
		$artStmt = $this->dbh->prepare("INSERT INTO artists(art_name) VALUES (?)");
		$albStmt = $this->dbh->prepare("INSERT INTO albums(alb_name, alb_art_id) VALUES (?, ?)");
		
		//echo $folder;
		
		$dirH = opendir($folder);
		if(!$dirH)
		{
			echo ("FATAL ERROR: Can't read the root directory($folder)\n");
			return false;
		}
		else
		{
			while (($file = readdir($dirH)) !== false)
			{
				if (($file == ".") or ($file == ".."))
					continue;
				//echo $file."\n";
				if (is_dir($folder.$file))
				{
					//echo $folder.$file."\n";
					$this->_addToDB($folder.$file, $root_length);
				}
				else
				{
					//echo $folder.$file."\n";
					$song_ext = strtolower(substr($file, strrpos($file, '.') + 1));
					//echo $song_ext."\n";
					if (array_search($song_ext,  $extArr) !== false)
					{
			        	$song_path = $folder.$file;
			        	$song_name = substr($file, 0, strrpos($file, '.'));
						$sng_path_no_root = substr($song_path, $root_length + 1, (strlen($song_name.$song_ext) + 1) * -1);
						//echo $sng_path_no_root."\n";
						$pathArr = explode("/", $sng_path_no_root);
						//print_r($pathArr);
						if (isset($pathArr[0])) $artist = $pathArr[0]; else $artist = ""; 
						if (isset($pathArr[1])) $album = $pathArr[1]; else $album = "";
						//echo "art: $artist, alb: $album\n\n";
						
						$art_id = 0;
						$alb_id = 0;
						if ($artist != '')
						{
            				//echo $artist."\n";
							try
							{
								$result = $artStmt->execute(array($artist));
								if ($result == 0)
								{
									echo "ERROR: Adding artist to DB: $artist<br/> <br /> ";
									print_r($this->dbh->errorInfo());
									return false;
								}
								else
								{
									$this->artCount++;
									$query = $this->dbh->query("SELECT max(art_id) FROM artists");
									$art_id = $query->fetchAll();
									//print_r($art_id);
									$art_id = $art_id[0][0];
									$result = $albStmt->execute(array('unknown', $art_id));
									if ($result == 0)
									{
										echo "ERROR: Adding unknown album to new artist: $art_id<br/> <br /> ";
										print_r($this->dbh->errorInfo());
										return false;
									}
								}
							}
							catch (PDOException $e)
							{
								if ($e->getCode() == 23000)
								{
									$artist = str_replace("'", "''", $artist);
									//echo "SELECT art_id FROM artists WHERE `art_name`='$artist'<br />\n";
									$query = $this->dbh->query("SELECT art_id FROM artists WHERE `art_name`='$artist'");
									$art_id = $query->fetchAll();
									//print_r($art_id);
									$art_id = $art_id[0][0];
								}
								else
								{
									echo $e->getMessage();
									return false;
								}
							}
							$query = $this->dbh->query("SELECT count(alb_name) FROM albums");
							$alb_id = $query->fetchAll();
							//print_r($alb_id);
							$alb_id = $alb_id[0][0] - 1;
						}

						if ($album != '')
						{
							//echo $album."\n";
							$getAlbumIDStmt = $this->dbh->prepare("SELECT alb_id FROM albums WHERE alb_name=? AND alb_art_id=?");
							$getAlbumIDStmt->execute(array($album, $art_id));
							$alb_id = $getAlbumIDStmt->fetchAll();
							//print_r($alb_id);
							if (count($alb_id) == 0)
							{
								$result = $albStmt->execute(array($album, $art_id));
								if ($result == 0)
								{
									echo "ERROR: Adding album to DB: $album, $art_id<br/> <br /> ";
									print_r($this->dbh->errorInfo());
									return false;
								}
								else
								{
									$this->albCount++;
									$getAlbumIDStmt->execute(array($album, $art_id));
									$alb_id = $getAlbumIDStmt->fetchAll();
									if (count($alb_id) == 0)
									{
										echo "ERROR: Can't retrieve the album ID for the lastest added album($album)<br />";
										print_r($this->dbh->errorInfo());
										return false;
									}
									else
									{
										$alb_id = $alb_id[0]['alb_id'];
									}
								}
							}
							else
							{
								$alb_id = $alb_id[0]['alb_id'];
							}
						}
						else
						{
							
						}
						
						$paramArr = array(':song_id' => sha1($folder.$file), ':song_path' => $song_path, 
								  ':song_name' => $song_name, ':song_ext' => $song_ext, ':art_id' => $art_id, ':alb_id' => $alb_id);
						//print_r($paramArr);
						
						try
						{
							$result = $songStmt->execute($paramArr);
						}
						catch(PDOException $e)
						{
							exit($e->getMessage());
						}
						if ($result == 0)
						{
							echo "ERROR: Adding song to DB: <br/> <br /> ";
							print_r($paramArr);
							echo "<br/><br/>";
							print_r($this->dbh->errorInfo());
							return false;
						}
						else
							$this->sngCount++;
					}//EXT not recognized
					else
						continue;
			    }//this is a file
			}//while
			closedir($dirH);
		}//opendir
		
		echo "	<script language=\"javascript\">
					document.getElementById('sng').innerHTML = $this->sngCount;
					document.getElementById('art').innerHTML = $this->artCount;
					document.getElementById('alb').innerHTML = $this->albCount;
				</script>\n";
		return true;
	}//function

	function addToDB($folder, $root_length)
	{
		if ($folder == "") return false;
		
		$this->sngCount = 0;
		$this->artCount = 0;
		$this->albCount = 0;
		
		return $this->_addToDB($folder, $root_length);
	}
	//------------------------------------------------------------ Retrieve Playlists --------------------------------------------------------
	function getPLContents($plArr)
	{
		$resultArr = array();
		
		$sngStmt = $this->dbh->prepare("SELECT song_name FROM music WHERE `song_id`=?");
		for ($i = 0; $i < count($plArr); $i++)
		{
			$sng_id = $plArr[$i];
			//echo $sng_id;
			try	{ $sngStmt->execute(array($sng_id)); } catch(PDOException $e) { exit($e->getMessage()); }
			
			$queryArr = $sngStmt->fetchAll();
			if (count($queryArr) != 0)
				$resultArr[] = array("id" => $sng_id, "name" => $queryArr[0]["song_name"]);
		}
		
		return $resultArr;
	}
	
	function getPLContents_json($plArr)
	{
		return json_encode($this->getPLContents($plArr));
	}
	
	function getPlaylist($plFormat, $plArr, $musicURL)
	{
		$result = array();
		$result = $this->plFormats[$plFormat]['head'];
		
		$sngStmt = $this->dbh->prepare("SELECT song_id, song_name FROM music WHERE `song_id`=?");
		for ($i = 0; $i < count($plArr); $i++)
		{
			try
			{
				$sngStmt->execute(array($plArr[$i]));
			}
			catch (PDOException $e) { exit($e->getMessage()); }
			
			$queryArr = $sngStmt->fetchAll();
			$song_id = $queryArr[0]['song_id'];
			$song_name = $queryArr[0]['song_name'];
			$songURL = $musicURL."server/stream.php?k=".$_SESSION["key"].$this->plFormats[$plFormat]['amp']."s=$song_id";	
			//			#EXTINF:LENGTH,SONG_NAME";
			$result .= sprintf($this->plFormats[$plFormat]['track'], $song_name, $songURL, $i + 1);
		}
		
		$result .= $this->plFormats[$plFormat]['foot'];
		
		return $result;		
	}

	//------------------------------------------------------------ Recreate DB --------------------------------------------------------
	function recreateDB()
	{
		unset($this->dbh);
		unlink($this->dbfilepath);
		
		$this->dbh = new PDO("sqlite:".$this->dbfilepath);
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		//-------------------------------------------------TABLE ARTISTS DEFINITION------------------------------
		try
		{
			$this->dbh->exec("CREATE TABLE artists (
			  art_id    integer PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
			  art_name  varchar(60) NOT NULL UNIQUE
			);			
			CREATE TRIGGER artists_au_fkr_albums
			  AFTER UPDATE OF art_id
			  ON artists
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM albums WHERE alb_art_id = OLD.art_id) IS NOT NULL
			    THEN RAISE(ABORT, 'Can''t change Artist ID because this artist has albums conected with it.')
			  END;
			END;
			
			CREATE TRIGGER artists_au_fkr_music
			  AFTER UPDATE OF art_id
			  ON artists
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM music WHERE song_art = OLD.art_id) IS NOT NULL
			    THEN RAISE(ABORT, 'Can''t update artist id because it doesn''t exist on table artists')
			  END;
			END;
			
			CREATE TRIGGER artists_bd_fkr_albums
			  BEFORE DELETE
			  ON artists
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM albums WHERE alb_art_id = OLD.art_id) IS NOT NULL
			    THEN RAISE(ABORT, 'Can''t delete this artist because there are albums conected to it.')
			  END;
			END;
			
			CREATE TRIGGER artists_bd_fkr_music
			  BEFORE DELETE
			  ON artists
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM music WHERE song_art = OLD.art_id) IS NOT NULL
			    THEN RAISE(ABORT, 'Can''t delete this artist, because there is music conected to it')
			  END;
			END;");
		}
		catch (PDOException $e) { exit($e->getMessage()); }
		
		try 
		{
			$this->dbh->exec("INSERT INTO artists(art_id, art_name) VALUES (0, 'unknown')"); 
		} 
		catch(PDOException $e) 
		{ 
			echo("FATAL ERROR: Inserting 'unknown' artist entry\n");
			exit($e->getMessage());
			
		}
		//-------------------------------------------------TABLE ALBUMS DEFINITION------------------------------
		try
		{
			$this->dbh->exec("CREATE TABLE albums (
			  alb_id      integer PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
			  alb_name    varchar(60) NOT NULL,
			  alb_art_id  integer NOT NULL,
			  /* Foreign keys */
			  FOREIGN KEY (alb_art_id)
			    REFERENCES artists(art_id)
			);
			
			CREATE TRIGGER albums_bi_fk_artists
			  BEFORE INSERT
			  ON albums
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM artists WHERE artists.art_id = NEW.alb_art_id) IS NULL
			    THEN RAISE(ABORT, 'Can''t insert that album because artist ID doesnt exist on artists table')
			  END;
			END;
			
			CREATE TRIGGER albums_bu_fk_artists
			  BEFORE UPDATE OF alb_art_id
			  ON albums
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM artists WHERE artists.art_id = NEW.alb_art_id) IS NULL
			    THEN RAISE(ABORT, 'Can''t update record. artist id is doesn''t exist in the artists table.')
			  END;
			END;");
		} 
		catch(PDOException $e) 
		{ 
			echo("FATAL ERROR: Creating table 'albums'\n");
			exit($e->getMessage());
		}
		
		try
		{
			$this->dbh->exec("INSERT INTO albums(alb_id, alb_name, alb_art_id) VALUES (0, 'unknown', 0)");
		}
		catch(PDOException $e)
		{
			echo("FATAL ERROR: Inserting 'unknown' album for the 'unknown' artist entry\n");
			exit($e->getMessage());
		}
		//-------------------------------------------------TABLE MUSIC DEFINITION------------------------------
		try
		{
			$this->dbh->exec("CREATE TABLE music (
			  song_id		varchar(40) PRIMARY KEY NOT NULL UNIQUE,
			  song_path		varchar(255) NOT NULL UNIQUE,
			  song_name		varchar(60) NOT NULL,
			  song_ext		varchar(4) NOT NULL,
			  song_album	integer NOT NULL DEFAULT 0,
			  song_art		integer NOT NULL DEFAULT 0,
			  song_comments	varchar(255),
			  /* Foreign keys */
			  FOREIGN KEY (song_album)
			    REFERENCES artists(alb_id), 
			  FOREIGN KEY (song_art)
			    REFERENCES albums(art_id)
			);
			
			CREATE TRIGGER music_bi_fk_albums
			  BEFORE INSERT
			  ON music
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM albums WHERE albums.alb_id = NEW.song_album) IS NULL
			    THEN RAISE(ABORT, 'Can''t insert that song because the album ID doesn''t exist in the albums table')
			  END;
			END;
			
			CREATE TRIGGER music_bi_fk_artists
			  BEFORE INSERT
			  ON music
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM artists WHERE artists.art_id = NEW.song_art) IS NULL
			    THEN RAISE(ABORT, 'Can''t insert this song, because the artist id doesn''t exist in the artists table')
			  END;
			END;
			
			CREATE TRIGGER music_bu_fk_albums
			  BEFORE UPDATE OF song_album
			  ON music
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM albums WHERE albums.alb_id = NEW.song_album) IS NULL
			    THEN RAISE(ABORT, 'Can''t update this song''s album ID because it doesnt exist in the albums table')
			  END;
			END;
			
			CREATE TRIGGER music_bu_fk_artists
			  BEFORE UPDATE OF song_art
			  ON music
			BEGIN
			  SELECT CASE
			    WHEN (SELECT 1 FROM artists WHERE artists.art_id = NEW.song_art) IS NULL
			    THEN RAISE(ABORT, 'Can''t update artist id for this song because it doesnt exist in the table artists')
			  END;
			END;");
		}
		catch(PDOException $e)
		{
			echo("FATAL ERROR: Creating table 'music'\n");
			exit($e->getMessage());
		}
	} 
}

?>
