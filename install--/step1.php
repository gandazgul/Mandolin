<p style="margin-top: 10px" class="title">If this is the first time you access Mandolin, then thank you for downloading SCTree Mandolin. This is
		the installation script. Just follow the wizard and provide the necessary information</p>

<p style="margin-top: 10px" class="title">If you already completed the installation then, delete the "install" directory before trying to login.</p>

<table border="0">
  <tr>
  	<td colspan="2"><br><strong>Checking PHP settings and making sure we have everything to begin the installation</strong><br><br></td>
  </tr>
  <tr>
    <td>PHP Version</td>
    <td>
		<?php
			$fatal = false;
			$recheck = false;
			// Let's check the PHP Version
			if (phpversion() < 4.3)
			{
				echo '<font color="red">4.3+ required, '. phpversion(). ' found - fatal error!</font>';
				$fatal = true;
			}
			else
				echo ' <font color="green">'. phpversion(). ' found (4.3 or higher required)</font>';
		?>
	</td>
  </tr>
  <tr>
    <td>PHP Session Support</td>
    <td>
		<?php
			$jz_sess_test_var = 2;
			$jz_sess_test_var = $_SESSION['jz_sess_test'] + 1;
			if (!function_exists('session_name') or $jz_sess_test_var <> 1)
			{
				echo '<font color="red">PHP Session Support not found/functioning - fatal!</font>';
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
			if (function_exists("sqlite_query"))
				echo '<font color="green">SQLite Support found!</font>';
			else
			{
				$fatal = true;
				echo '<font color="orange">Not found</font>';
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
		$cArray = file('./install/filelist');
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
				foreach($missing as $file) { echo $file. "<br />"; }
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
						$max_execution_time = ini_get('max_execution_time');
						if ($max_execution_time > 299)
						{
							echo '<font color="green">';
						} 
						else 
						{
							echo '<font color="red">';
							$recheck = true;
							//$fatal = true;
						}
						echo $max_execution_time. "</font><br>\n"
					?>
				
				</td>
				<td width="25%" align="center" class="td">
					300+
				</td>
			</tr>
			<!--tr>
				<td width="50%" class="td">
					memory_limit:
				</td>
				<td width="25%" align="center" class="td">
					<?php
						/*if (ini_get('memory_limit') >= 32)
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
						echo ini_get('upload_max_filesize'). "</font><br>\n"*/;
					?>
				</td>
				<td width="25%" align="center" class="td">
					32M+
				</td>
			</tr-->
		</table>	
	</td>
  </tr>
  <tr>
  	<td>
		<?php if ($recheck): ?>
			<br>
			<form action="./?p=install" method="post">
				<input type="submit" value="Recheck" class="ui-state-default ui-corner-all" />
			</form>
		<?php endif; ?>
	</td>
	<td>
		<?php if (!$fatal): ?>
			<br>
			<form action="./?p=install&step=2" method="post">
				<input type="submit" value="Continue to Step 2 &gt;" class="ui-state-default ui-corner-all" />
			</form>
		<?php endif; ?>
	</td>
  </tr>
</table>
