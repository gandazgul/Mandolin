<?php
/*if (!isset($sess_id) or ($_SESSION['userAdminLevel'] != 0))
{
	header("Location: ./index.php");
	exit();
}*/

ini_set('max_execution_time', '6000');

require_once './backend/MusicDB.php';

unlink("./db/music.db");
$dbh = new PDO("sqlite:./db/music.db");
//-------------------------------------------------TABLE ARTISTS DEFINITION------------------------------
$result = $dbh->exec("CREATE TABLE artists (
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
if ($result === false)
{
	echo("FATAL ERROR: Creating table 'artists'\n");
	print_r($dbh->errorInfo());
	die();
}
$result = $dbh->exec("INSERT INTO artists(art_id, art_name) VALUES (0, 'unknown')");
if ($result === false)
{
	echo("FATAL ERROR: Inserting 'unknown' artist entry\n");
	print_r($dbh->errorInfo());
	die();
}
//-------------------------------------------------TABLE ALBUMS DEFINITION------------------------------
$result = $dbh->exec("CREATE TABLE albums (
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
if ($result === false)
{
	echo("FATAL ERROR: Creating table 'albums'\n");
	print_r($dbh->errorInfo());
	die();
}
$result = $dbh->exec("INSERT INTO albums(alb_id, alb_name, alb_art_id) VALUES (0, 'unknown', 0)");
if ($result === false)
{
	echo("FATAL ERROR: Inserting 'unknown' album for the 'unknown' artist entry\n");
	print_r($dbh->errorInfo());
	die();
}
//-------------------------------------------------TABLE MUSIC DEFINITION------------------------------
$result = $dbh->exec("CREATE TABLE music (
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
if ($result === false)
{
	echo("FATAL ERROR: Creating table 'music'\n");
	print_r($dbh->errorInfo());
	die();
}
//---------------------------------------------NOW LET'S FILL THE DATABASE----------------------------------------
echo "<div id='nav'>
	<!-- skiplink anchor: navigation -->
	<a id='navigation' name='navigation'></a>
	<div class='hlist'>
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href='./?p=music'>Music</a></li>
			<li><a href='./?p=pl'>Music Playlists</a></li>
			<li><a href='./?p=movies'>Movies</a></li>
			<li><a href='./?p=adm'>Aministration</a></li>
			<li><a href='./?p=about'>About</a></li>
			<li><a href='./logout.php'>Logout</a></li>
		</ul>
	</div>
</div>
<div id='teaser'>
	<div id='errorDiv'></div>
</div>
<div id='main'>
	<h1>Creating the Database</h1>
	<ul>
		<li>Database deleted and new one created</li>
		<li>Scanning directories to add music to the new DB - <span style='color: FFCC00'>DO NOT HIT THE BACK BUTTON ON YOUR BROWSER!!!</span></li>
		<ul>
			<li>Artists: <span id='art'></span></li>
			<li>Albums:  <span id='alb'></span></li>
			<li>Songs: <span id='sng'></span></li>
		</ul>
	</ul>
</div>";
 ob_flush();

$musicDB = new MusicDB("./db/music.db"); 
//echo $musicDB->getTotals_json();
$musicDB->addToDB('C:/drivers/', strlen('C:/drivers/'));

?>
