<?php
//GetImage.php


/**
 * Copyright (C) 2016  Bluff Point Technologies LLC
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if((!isset($_GET["FID"]))&&(!isset($_POST["FID"])) ) {
	header("HTTP/1.0 404 Not Found");
	echo("404 File Not Found!");
	exit(1);
}


define("DBIPADD", "127.0.0.1");
define("DBUNAME", "ODOWebtest");
define("DBPWORD", "ODOWebtest");
define("DBNAME", "OrcimWeb");



$link = mysql_connect(DBIPADD, DBUNAME, DBPWORD)
			or die('Could not connect: ' . mysql_error());

mysql_select_db(DBNAME) or trigger_error('Could not select database');

//ID flag is just used to tell if we have an ID.

$Rvalue = "";
if(isset($_GET["FID"])) {
	$Rvalue = $_GET["FID"];
} elseif($_POST["FID"]) {
	$Rvalue = $_POST["FID"];
}

if(get_magic_quotes_gpc()) {
	$Rvalue = stripslashes($Rvalue);
}

$Rvalue = mysql_real_escape_string($Rvalue, &$link);
$Rvalue = intval($Rvalue);

$query = "SELECT FID, BFile, PermFlag, Type, Name, Size FROM ODOFiles, FileTypes WHERE ODOFiles.FTypeID=FileTypes.FTypeID AND ODOFiles.FID=" . $Rvalue;


$result = mysql_query($query) or trigger_error('Query failed at 103a: Query:' . $myquery . "Error:" . mysql_error(), E_USER_ERROR);

if(!mysql_num_rows($result)) {
	header("HTTP/1.0 404 Not Found");
	echo("404 File Not Found!");
	mysql_close($link);
	exit(1);
}

$row = mysql_fetch_assoc($result);

if($row["PermFlag"] == 1) {
	include "class.php";
	//Create ODODB object
	$ODODBO = new ODODB;

	//establish connection
	$ODODBO->Connect();
	session_start();

	if(!isset($_SESSION["ODOUserO"]))
	{

		$_SESSION["ODOUserO"] = new ODOUser;

	}


	//verify user
	//if verify is on then verify...otherwise we only check the flag
	if(VERIFYUIDPERPAGE)
	{

		if(!$_SESSION["ODOUserO"]->VerifyUser())
		{
			trigger_error("ODOError: Verify Failed", E_USER_ERROR);
			//echo page to log back in.
		}
	}

	$query = "SELECT ODOFiles.Name FROM ODOFiles, GroupsForFiles, ODOGroups, ODOUserGID WHERE ODOUserGID.UID=" . $_SESSION["ODOUserO"]->getUID() . " AND ODOUserGID.GID=ODOGroups.GID AND ODOGroups.GID=GroupsForFiles.GID AND GroupsForFiles.FID=" . $row["FID"];

	$result2 = mysql_query($query) or trigger_error('Query failed at 103a: Query:' . $myquery . "Error:" . mysql_error(), E_USER_ERROR);

	if(mysql_num_rows($result2) > 0) {
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header('Content-Transfer-Encoding: binary');
		//header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $row["Name"] . '";\n\n');
		//header("Content-Disposition: inline; filename=$name");
		header("Pragma: cache");
		header("Cache-Control: private");
		header('Content-Length: ' . $row["Size"]);
		header('Content-type: ' . $row["Type"]);


		print $row["BFile"];


	} else {

		echo "<html><title>ODOWeb Access denied</title><body><center><H1>ACCESS DENIED</H1></center></body></html>";

	}

} else {

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header('Content-Transfer-Encoding: binary');
	//header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="' . $row["Name"] . '";\n\n');
	//header("Content-Disposition: inline; filename=$name");
	header("Pragma: cache");
	header("Cache-Control: private");
	header('Content-Length: ' . $row["Size"]);
	header('Content-type: ' . $row["Type"]);

	print $row["BFile"];

}

mysql_close($link);

?>