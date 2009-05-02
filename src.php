<?php
$srcFile = "src.zip";
header("Content-length: ".filesize($srcFile) );
header("Content-Disposition: filename=\"".$srcFile."\"");
header("Content-Transfer-Encoding: binary");
readfile($srcFile);
?>