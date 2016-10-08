<?PHP

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

$ODOMenuO;
//Comented out as ODOStore is not part of first release
//$ODOStoreO;
$ODORegisterO;

if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOMenuO")) {
	$ODOMenuO = new ODOMenu;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOMenuO", $ODOMenuO);

} else {

	$ODOMenuO =& $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"];
}

//Commented out as ODOStore is not part of first release
//if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOStoreO")) {
//	$ODOStoreO = new ODOStore;
//	$_SESSION["ODOSessionO"]->RegisterObject("ODOStoreO", $ODOStoreO);
//
//} else {
//
//	$ODOStoreO =& $_SESSION["ODOSessionO"]->UserObjectArray["ODOStoreO"];
//}


if(!$_SESSION["ODOSessionO"]->IsRegistered("ODORegisterO")) {
	$ODORegisterO = new ODORegister;
	$_SESSION["ODOSessionO"]->RegisterObject("ODORegisterO", $ODORegisterO);

} else {

	$ODORegisterO =& $_SESSION["ODOSessionO"]->UserObjectArray["ODORegisterO"];
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>
<?php
echo SERVERNAME
?></title>
<link rel='stylesheet' type='text/css' href='css/global.css'>
<link rel='stylesheet' type='text/css' href='css/menus.css'>

<script type="text/javascript" src="js/odoweb.js"></script>
</head>
<body>
<div class="LeftNav">
<center>
<br>
<div id="logo"></div>
<br><br>
<?php

echo $ODOMenuO->GetODOLeftMenu("PublicSite");

?>
<br><br><br><br>
<sub>Bluff Point Tech LLC &copy</sub>
</center>
</div>

<div class="Right" align="Center">
<IMG src="images/Header.jpg" width="450" height="100" border="0">
<BR><img src="images/HR2.jpg" width="100%" height="2px">
<H3>Site Registration</H3>
<?php

//set the default registration if no RegID was passed

if(!isset($_SESSION["ODOSessionO"]->EscapedVars["RegID"])) {
	$_SESSION["ODOSessionO"]->EscapedVars["RegID"] = 1;
}

if(isset($_SESSION["ODOSessionO"]->EscapedVars["RegMe"])) {

	if($ODORegisterO->ProcRegisterPage()) {

		echo $ODORegisterO->Content;
		
		//output next link
		

	} else {
		//error messagees
		//style="color:#FF0D05"
		echo "<div id=\"regcontenterrormsg\">Please fix the following issues:" . $ODORegisterO->MissingText . "</div>";
		
		if($ODORegisterO->LoadRegisterPage()) {

			echo $ODORegisterO->Content;

		} else {

			echo "<BR><H4>You have passed an invalid Register ID! Please hit your browser back button.</H4>";

		}
	}
	

} else {

	if($ODORegisterO->LoadRegisterPage()) {

		echo $ODORegisterO->Content;

	} else {

		echo "<BR><H4>You have passed an invalid Register ID! Please hit your browser back button.</H4>";

	}

}
?>

</div>
<BR>
<div id="footer">
<img src="images/HR2.jpg" width="100%" height="2px">
<br></div></body></html>

<?php

?>