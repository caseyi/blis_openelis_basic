<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">  
<html xmlns="http://www.w3.org/1999/xhtml">  
<head>
<link href="htdocs/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<!--link href="assets/css/metro.css" rel="stylesheet" />
<link href="assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" /-->
<link href="htdocs/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
<?php
#
# (c) C4G, Ricky Kitsao
# BLIS Installation Page
#

if ($_REQUEST){
	extract($_REQUEST);
	$con = mysql_connect($DB_Host, $DB_User, $DB_Pwd);
	if (!$con) die('Unable to connect to host: '.mysql_error());
	$query = 'SHOW DATABASES LIKE \''.$DB_Name.'\'';
	$result = mysql_query($query);
	if ($result){
		if (!mysql_num_rows($result)){
			$query = 'CREATE DATABASE '.$DB_Name;
			if (!mysql_query($query)) die('Unable to create database: '.mysql_error());
		} else $db_exists = true;		
	} else die('MySQL Error: '.mysql_error());
	if (!mysql_select_db($DB_Name, $con)){
		die('Unable to select database: '.mysql_error());
	}
	if ($db_exists && ($DB_Overwrite!='on')) die('<p align="center">The database '.$DB_Name.' already exists!<br><br>Please select the \'Overwrite existing data\' option on the <a href="javascript:window.back()">previous page</a> if you wish to replace the existing data</p>');
	//Run db script
	$handle = @fopen("blis_def.sql", "r");
	if ($handle){
		$currline = '';
		while (($buffer = fgets($handle)) !== false) {
			//echo 'Buf='.$buffer.'<br>';
			$currline .= $buffer;
			//echo 'Sub='.substr($buffer, -3, 1);
			if (substr($buffer, -3, 1)==';'){
				mysql_query($currline);
				$currline = '';
			}
			//echo $buffer;
		}
		if (!feof($handle)) {
			die("Error: unexpected fgets() failure\n");
		}
		fclose($handle);
		
		//Update db_constants.php
		if (rename("htdocs/includes/db_constants.php", "db_constants.php.old")){
			$infile = @fopen("htdocs/includes/db_constants.php.old", "r");
			$outfile = @fopen("htdocs/includes/db_constants.php", "w");
			if ($infile && $outfile){
				$currline = '';
				while (($buffer = fgets($infile)) !== false) {
					if ((strtolower($DB_Host)!=='localhost') && (strpos($buffer, 'DB_HOST =')!==false)){
						$buffer = 'DB_HOST = '.$DB_Host;
					} else if ((strtolower($DB_User)!=='root') && (strpos($buffer, 'DB_USER =')!==false)){
						$buffer = 'DB_USER = '.$DB_User;
					} else if ((strtolower($DB_Name)!=='blis_revamp') && (strpos($buffer, 'GLOBAL_DB_NAME =')!==false)){
						$buffer = 'GLOBAL_DB_NAME = '.$DB_Name;					
					} else if ((strtolower($DB_Pwd)!=='') && (strpos($buffer, 'DB_PASS =')!==false)){
						$buffer = 'DB_PASS = '.$DB_Pwd;
					}
					fwrite($outfile, $buffer);
				}
				if (!feof($infile)) {
					die('Error: unexpected fgets() failure\n');
				}
				fclose($infile);
				header('location:htdocs/index.php');
			} else error_log('\n'.date('Y-m-d H:i:s').':Unable to open file for reading/writing', 3, 'install.log');
		}
	} else error_log('\n'.date('Y-m-d H:i:s').':Unable to open database file', 3, 'install.log');
}

?>
</head>
<body>
<!-- BEGIN ROW-FLUID-->   
<div class="row-fluid" align="center">
	<div class="span12 sortable">
		<div class='content_div'>
				<div class="portlet box green">
								<div class="portlet-title">
									<h2><i class="icon-reorder"></i>BLIS INSTALLATION PAGE</h2>
								</div>
				</div>
				<p>
				<form method="post">
					<table>
					<tr><td>Database Host</td><td><input type="text" name="DB_Host" value="localhost" /></td></tr>
					<tr><td>Database User</td><td><input type="text" name="DB_User" value="root" /></td></tr>
					<tr><td>Database Password</td><td><input type="password" name="DB_Pwd" /></td></tr>
					<tr><td>Database Name</td><td><input type="text" name="DB_Name" value="blis" /><br><input type="checkbox" name="DB_Overwrite" />&nbsp;Overwrite existing data</td></tr>
					<tr><td colspan="2" align="right"><input type="submit" value="Proceed" /></td></tr>
					</table>
				</form>
				</p>
		</div>
	</div>
</div>
<!-- END ROW-FLUID-->  
</body>
</html>
