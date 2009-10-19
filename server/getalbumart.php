#!/usr/bin/php
<?
/* ----------------------------------------------------------------------------
	App Title:		Get Album Art
	App Version:	1.0
	Author:			Jared Breland <jbreland@legroom.net>
	Homepage:		http://www.legroom.net/mysoft

	Script Function:
		Download cover image for a specific album

	Instructions:
		Configure the variables under "Setup Environment" for your environment

	Requirements:
		The following programs must be installed and available
		ImageMagick (http://www.imagemagick.org/)
			used to create freedesktop.org icon

	Please visit the app's homepage for additional information.
---------------------------------------------------------------------------- */

/*
	TODO:
*/

# Setup environment
$convert = "/usr/bin/convert";
$minx = 250;
$miny = 250;
$name = "00-Cover Front";
$desktop = false;
$path = getcwd();
$albums = array();
$images = 0;
$dvdalbum = 0;
$sorder = array("buy", "walmart", "amazon");
$prog = basename($argv[0]);

# Check for arguments
for ($i = 1; $i <= $argc; $i++) {
	switch (strtolower($argv[$i])) {
		case "-h":
		case "--help":
		case "-?":
			warning();
			break;
		case "-c":
			$artist = basename(dirname(getcwd()));
			$album = basename(getcwd());
			break;
		case "-r":
			$artist = $argv[$i+1];
			break;
		case "-l":
			$album = $argv[$i+1];
			break;
		case "-p":
			$path = realpath($argv[$i+1]);
			break;
		case "-n":
			$name = $argv[$i+1];
			break;
		case "-d":
			$desktop = true;
			break;
		case "-q":
			$quiet = true;
			break;
	}
}

# Make sure necessary album information exists
if ($artist == "" or $album == "") {
	warning();
}

# Check that required programs exist
if ($desktop && !file_exists($convert)) {
	echo "Error: $convert does not exist\n";
	exit;
}

# Check for existing image
if (file_exists("$path/$name.jpg")) {
	if (!$quiet) echo "$path/$name.jpg already exists.  Skipping image download.\n";
	if ($desktop) desktop_icon();
	exit;
}

# Check if the album is a DVD
if (strtoupper(substr($album, -6)) == " (DVD)")
	$dvdalbum = substr($album, 0, strlen($album)-6);

# Set buy.com search options
if ($dvdalbum)
	$buysearch = "http://www.buy.com/retail/searchresults.asp?search_store=4&qu=".urlencode("$artist, $dvdalbum");
else
	$buysearch = "http://www.buy.com/retail/searchresults.asp?search_store=6&qu=".urlencode("$artist, $album");
$buyimg = "http://ak.buy.com/db_assets/large_images/";

# Set walmart.com search options
if ($dvdalbum)
	$walsearch = "";
else
	$walsearch = "http://www.walmart.com/catalog/search-ng.gsp?search_constraint=4104&search_query=".urlencode("$artist, $album");

# Set amazon.com search options
if ($dvdalbum)
	$azsearch = "http://www.amazon.com/exec/obidos/external-search/?index=dvd&field-title=".urlencode("$artist - $dvdalbum");
else
	$azsearch = "http://www.amazon.com/exec/obidos/external-search/?index=music&field-artist=".urlencode($artist)."&field-title=".urlencode($album);
$azimg1 = "http://images.amazon.com/images/P/";
$azimg2 = ".01._SCLZZZZZZZ_.jpg";

# Search each store as necessary
foreach ($sorder as $item) {
	# Search store for album
	$albums = album_search($item);

	# If at least one found, download and validate
	if ($albums) $images = album_download($albums);

	# If download successful, break.  Otherwise, process next store.
	if ($images) break;
}

# Check for success
if ($images == 0) {
	if (!$quiet) echo "\nNo (new) suitable cover images could be found.\n";

# Show notice for multiple images
} else {
	# Create (or update) .directory files
	if ($desktop) desktop_icon();

	# Show warning if multiple images found
	if ($images > 1) {
		if (!$quiet) {
			echo "\nWarning: Multiple images were found.\n";
			echo "It is recommended that you review these images and select the correct one.\n";
		}
	}
}
exit;

# Print usage message and exit is program called improperly
function warning() {
	global $prog;
    echo "Usage: $prog {-c | -r \"Artist Name\" -l \"Album Name\"} \\\n";
	echo "       ".str_repeat(" ", strlen($prog))." [-h] [-p path] [-n imagename] [-d] [-q]\n";
    echo "Download album art for given artist and album\n";
	echo "\nMandatory Arguments: (either -c or -r and -l must be specified)\n";
	echo "   -c         Use directory structure for album information:\n";
	echo "                 Album's name = current dir\n";
	echo "                 Artist's name = parent dir\n";
    echo "   -r artist  Specify Artist's name\n";
    echo "   -l album   Specify Album's name\n";
	echo "\nOptions:\n";
    echo "   -h            Display this help information\n";
    echo "   -p path       Downloaded location;  Default is current directory\n";
    echo "   -n imagename  Image base name;      Default is \"00-Cover Front.jpg\"\n";
    echo "   -d            Enable freedesktop.org .directory icon support\n";
    echo "   -q            Quiet mode\n";
	echo "\nArguments with spaces must be enclosed by quotes.\n";
	exit;
}

# Function to search the given store
function album_search($store) {
	global $artist, $album, $path, $name, $buysearch, $buyimg, $walsearch, $azsearch, $azimg1, $azimg2, $quiet;
	$covers = array();
	if ($store == "buy") $search = $buysearch;
	elseif ($store == "walmart") $search = $walsearch;
	elseif ($store == "amazon") $search = $azsearch;

	# Skip if search string is invalid
	if ($search == "")
		return 0;

	# Search web site
	if (!$quiet) {
		echo "\nFetching cover\n";
		echo "For:  $artist - $album\n";
		echo "To:   $path/$name\n";
		echo "From: $search ... ";
	}
	$results = file($search);
	if (!$quiet) echo "complete.\n";

	# Scan through results for album image link(s)
	$multiple = false;
	foreach ($results as $line) {
		# Results contain multiple hits - break and process separately
		if ($store == "buy") $multstring = ": Music Search Results";
		elseif ($store == "walmart") $multstring = "Search results for";
		elseif ($store == "amazon") $multstring = "Amazon.com: Music Search";
		if (stristr($line, $multstring)) {
			$multiple = true;
			break;
		}

		# Results contain single hit
		if ($store == "buy") {
			if (stristr($line, "javascript:largeIM")) {
				$line = substr($line, strpos($line, "javascript:largeIM") + 20);
				$covers[0] = substr($line, 0, strpos($line, "'"));
				return $covers;
			}
		} elseif ($store == "walmart") {
			if (stristr($line, "javascript:photo_opener")) {
				$line = substr($line, strpos($line, "javascript:photo_opener") + 25);
				$covers[0] = substr($line, 0, strpos($line, "&"));
				return $covers;
			}
		} elseif ($store == "amazon") {
			if (stristr($line, "SCMZZZZZZZ")) {
				$line = substr($line, strpos($line, "img src=") + 9);
				$thumb = substr($line, 0, strpos($line, '"'));
				$prod = basename($thumb);
				list($prod) = explode('.', urldecode($prod));
				$covers[0] = $azimg1.$prod.$azimg2;
				return $covers;
			}
		}
	}

	# Process results for multiple hits
	if ($multiple) {
		$i = 0;
		$process = false;
		if ($store == "buy") {
			$head = "Sorted by:";
			$foot = "matching products.";
		} elseif ($store == "walmart") {
			# Cannot determine product image URL from this results page
			return 0;
		} elseif ($store == "amazon") {
			$head = "Sort by:";
			$foot = "/associates/";
		}
		foreach ($results as $line) {
			# Skip buy.com lookup if not found
			if ($store == "buy")
				if (stristr($line, "We could not find an exact match"))
					return 0;
			# Drop header/left
			if (stristr($line, $head)) $process = true;
			if ($process) {
				if ($store == "buy") {
					if (stristr($line, "http://ak.buy.com/db_assets/ad_images/")) {
						$line = substr($line, strpos($line, "http://ak.buy.com/db_assets/ad_images/") + 38);
						$thumb = substr($line, 0, strpos($line, '"'));
						list($thumb) = explode('.', urldecode($thumb));
						$covers[$i++] = $buyimg.$thumb.".jpg";
					}
				} elseif ($store = "amazon") {
					if (stristr($line, "THUMB")) {
						$line = substr($line, strpos($line, "img src=") + 9);
						$thumb = substr($line, 0, strpos($line, '"'));
						$prod = basename($thumb);
						list($prod) = explode('.', urldecode($prod));
						$covers[$i++] = $azimg1.$prod.$azimg2;
					}
				}
				# Drop right/footer
				if (stristr($line, $foot)) break;
			}
		}
		return $covers;
	}
	return 0;
}

# Function to download found covers
function album_download($covers) {
	global $path, $name, $minx, $miny, $quiet;
	$saved = 0;

	foreach ($covers as $cover) {
		# Set first available output filename
		$file = "$path/$name";
		$i = 1;
		if (file_exists("$file.jpg")) {
			while (file_exists($file.++$i.".jpg")) {}
			$file .= $i;
		}

		# Download image
		$file .= ".jpg";
		copy($cover, $file);
		# Verify image dimensions
		$size = getimagesize($file);
		if ($size[0] < $minx || $size[1] < $miny) {
			unlink($file);
			continue;
		}

		# Check for duplicate image
		for ($j = $i-1; $j >= 1; $j--) {
			if ($j == 1) $x = "";
			else $x = $j;
			if (filesize($file) == filesize("$path/$name$x.jpg")) {
				unlink($file);
				continue 2;
			}
		}
		# Report and count successes
		if (!$quiet) echo "\nSaving $file\n";
		$saved++;
	}
	return $saved;
}

# Function to create freedesktop.org icon
function desktop_icon() {
	global $convert, $path, $name, $quiet;
	if (file_exists("$path/.directory.png")) return false;
	passthru("$convert -scale 32x32 \"$path/$name.jpg\" \"$path/.directory.png\"");
	$outfile = fopen("$path/.directory", 'w');
	fwrite($outfile, "[Desktop Entry]\nIcon=./.directory.png\n");
	fclose($outfile);
	if (!$quiet) echo "\nfreedesktop.org directory icon created from $path/$name.jpg\n";
}
?>
