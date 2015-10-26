<table border="0">
  <tr>
  	<td colspan="2"><br><strong>Checking PHP settings and making sure we have everything to begin the installation</strong><br><br></td>
  </tr>
  <tr>
    <td>PHP Version</td>
    <td>
		<?php
			$fatal = false;
			// Let's check the PHP Version
			if (phpversion() < 4.2)
			{
				echo '<font color="red">4.2+ required, '. phpversion(). ' found - fatal error!</font>';
				?>
				&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $php_version_error; ?>');" onmouseout="return nd();">?</a>
				<?php
				$fatal = true;
			} else {
				echo ' <font color="green">'. phpversion(). ' found (4.2 or higher required)</font>';
			}
		?>
	</td>
  </tr>
  <tr>
    <td>PHP Session Support</td>
    <td>
		<?php
			$fatal = false;
			$jz_sess_test_var = 2;
			$jz_sess_test_var = $_SESSION['jz_sess_test'] + 1;
			if (!function_exists('session_name') or $jz_sess_test_var <> 1)
			{
				echo '<font color="red">PHP Session Support not found/functioning - fatal!</font>';
				?>
				&nbsp; <a class="helpbox2" href="javascript:void(0);" onmouseover="return overlib('<?php echo $php_session_error; ?>');" onmouseout="return nd();">?</a>
				<?php
				$fatal = true;
			} 
			else 
			{
				echo '<font color="green">PHP Session Support Enabled!</font>';
			}
		?>
	</td>
  </tr>
  <tr>
    <td>PHP SQLite Support</td>
    <td>			
		<?php
			// Now let's check for GD support
			if (extension_loaded("pdo_sqlite")) 
			{
			  $pg = true;
			} 
			else 
			{
			  $pg = false;
			}
			if (!$pg)
			{
				echo '<font color="orange">Not found - only necessary if you want to use SQLite.</font>';
			}
			else
			{
				echo '<font color="green">SQLite Support found!</font>';
			}
		?>
	</td>
  </tr>
  <tr>
    <td>PHP Register Globals</td>
    <td>
		<?php
			// Now let's check for GD support
			if (ini_get('register_globals') == "1")
			{
				echo '<font color="red">On - <strong>HUGE Possible Security Risk</strong></font>';
			} 
			else 
			{
				echo '<font color="green">Off</font>';
			}
		?>	
	</td>
  </tr>
  <tr>
  	<td colspan="2"><br><strong>Checking Permissions</strong><br><br>
  </tr>
  <tr>
    <td>Settings</td>
    <td>
		<?php
			// Now let's check to see if things are writeable
			$file = "../settings";
			$error = true;
			if (file_exists($file) and is_file($file))
			{
				for ($i = 0; $i < 2; $i++)
				{
					if (!is_writable($file))
					{
						@chmod($file, 660);
					}
					else
					{
						$error = false;
						break;						
					}
				}
				if ($error)
					echo "<font color='red'>Settings file not writable! Please make sure the file \"settings\" can be written 
					 by the webserver but, only during the installation. When the installation ends it should be back to normal permissions.</font><br>";
			}
			else
			{
				if(fclose(fopen("settings")) === true)
					$error = false;
				else
					echo "<font color='red'>Settings file does not exist and I cant create it. Please create an empty file called \"settings\".</font><br>";
			}
			if (!$error)
				echo '<font color="green">Writable!</font><br>';
		?>	
	</td>
  </tr>
  <tr>
    <td>Database directory (./db)</td>
    <td>
		<?php
			// Now let's check all the directories
			$dirs = array("../db");
			
			// Now let's test each dir
			$fileError = false;
			foreach($dirs as $dir)
			{
				$file = $include_path. $dir;
				if (!is_writable($file))
				{
					$fileError = true;
					echo $file. " - not writable!<br>";
				}
			}
			if ($fileError)
			{
				$error = true;
			}
			if ($error)
			{
				$fatal = true;
				echo '<font color="red">FATAL ERROR: the database directory is not writable</font>';
				echo '<br>';
			} 
			else 
			{
				echo '<font color="green">this dir is writable</font><br>';
			}
		?>	
	</td>
  </tr>
  <tr>
    <td colspan="2"><br><strong>Checking Files</strong><br><br></td>
  </tr>
	<?php
		// Now let's make sure ALL the files exist
		$fileMiss = false;
		$cArray = file('filelist');
		for ($i = 0; $i < count($cArray); $i++)
		{
			//echo "\"".$cArray[$i]."\"";
			if (!is_file(trim($cArray[$i])))
			{
				$fatal = true;
				$fileMiss = true;
				$missing[] = $cArray[$i];
			}
		}
	?>  
  <tr>
    <td><?php echo "Checking $i files"; ?></td>
    <td>
		<?php
			if (!$fileMiss)
			{
				echo '<font color="green">All required files found!</font>';
			} 
			else 
			{
				echo '<font color="red"><strong>This files are missing: </strong><br />';
				foreach($missing as $file)
				{
					echo $file. "<br />";
				}
				echo '</font>';
			}
		?>	
	</td>
  </tr>
  <tr>
  <td colspan="2"><br><strong>PHP.ini recomended settings</strong><br><br></td>
    <tr>
  	  <td>PHP Settings (php.ini)</td>
    <td>
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td width="50%" class="td">
					<strong>Setting</strong>
				</td>
				<td width="25%" align="center" class="td">
					<strong>Actual</strong>
				</td>
				<td width="25%" align="center" class="td">
					<strong>Recommend</strong>
				</td>
			</tr>
			<tr>
				<td width="50%" class="td">
					max_execution_time:
				</td>
				<td width="25%" align="center" class="td">
					<?php
						if (ini_get('max_execution_time') > 299)
						{
							echo '<font color="green">';
						} 
						else 
						{
							echo '<font color="red">';
							$recheck = true;
						}
						echo ini_get('max_execution_time'). "</font><br>\n"
					?>
				
				</td>
				<td width="25%" align="center" class="td">
					300+
				</td>
			</tr>
			<tr>
				<td width="50%" class="td">
					memory_limit:
				</td>
				<td width="25%" align="center" class="td">
					<?php
						if (ini_get('memory_limit') >= 32)
						{
							echo '<font color="green">';
						} 
						else 
						{
							echo '<font color="red">';
							$recheck = true;
						}
						echo ini_get('memory_limit'). "</font><br>\n"
					?>
				</td>
				<td width="25%" align="center" class="td">
					32M+
				</td>
			</tr>
			<tr>
				<td width="50%" class="td">
					post_max_size:
				</td>
				<td width="25%" align="center" class="td">
					<?php
						if (ini_get('post_max_size') >= 32)
						{
							echo '<font color="green">';
						} 
						else 
						{
							echo '<font color="red">';
							$recheck = true;
						}
						echo ini_get('post_max_size'). "</font><br>\n";
					?>
				</td>
				<td width="25%" align="center" class="td">
					32M+
				</td>
			</tr>
			<tr>
				<td width="50%" class="td">
					file_uploads:
				</td>
				<td width="25%" align="center" class="td">
					<?php
						if (ini_get('file_uploads') > 0)
						{
							echo '<font color="green">';
						} 
						else 
						{
							echo '<font color="red">';
							$recheck = true;
						}
						echo ini_get('file_uploads'). "</font><br>\n";
					?>
				</td>
				<td width="25%" align="center" class="td">
					<font color="green">on</font>
				</td>
			</tr>
			<tr>
				<td width="50%" class="td">
					upload_max_filesize:
				</td>
				<td width="25%" align="center" class="td">
					<?php
						if (ini_get('upload_max_filesize') >= 32)
						{
							echo '<font color="green">';
						} 
						else 
						{
							echo '<font color="red">';
							$recheck = true;
						}
						echo ini_get('upload_max_filesize'). "</font><br>\n";
					?>
				</td>
				<td width="25%" align="center" class="td">
					32M+
				</td>
			</tr>
		</table>	
	</td>
  </tr>
  <tr>
  	<td>
		<br>
		<form action="./index.php" method="post">
			<input type="submit" value="Recheck" class="submit">
		</form>
	</td>
	<td>
		<br>
		<form action="./index.php?step=2" method="post">
			<input type="submit" value="Continue to Step 2 >" class="submit">
		</form>		
	</td>
  </tr>
</table>