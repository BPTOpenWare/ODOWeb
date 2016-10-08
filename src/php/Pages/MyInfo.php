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

if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOMenuO")) {
	$ODOMenuO = new ODOMenu;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOMenuO", $ODOMenuO);

} else {

	$ODOMenuO =& $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"];
}

?>

<!DOCTYPE HTML>
<html>
<head><title>Bluff Point Technologies LLC</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="Bluff Point Technologies LLC is a developer of several online products relating to online music sales and content management systems.">
<meta name="keywords" content="Online Music, Live song tagging, ODOMuse, ODOMuse Live, ODOMuse Radio, ODOWeb, CMS, ODOTouch, information technology, Software development, Technology startup, St. Louis">
<meta name="author" content="Bluff Point Technologies LLC">
<link rel='stylesheet' type='text/css' href='css/global.css'>
<link rel='stylesheet' type='text/css' href='css/menus.css'>
<script type="text/javascript" src="js/odoweb.js"></script>
</head>
<body>
<div class="LeftNav">
<center>
<br>
<div id="logo"></div>
<br>
<?php

$Output = $ODOMenuO->GetODOLeftMenu("PublicSite");

echo $Output;

?>
<br><br><br><br>
<sub>Bluff Point Tech LLC &copy</sub>
</center>
</div>
<div class="Right">
<center>
<BR>
<img src="images/Header.jpg" width="450" height="100" border="0">
<BR>
<img src="images/HR2.jpg" width="100%" height="2px">

<BR><BR>
<table class="MyInfoBlock" cellspacing=0>
<TR><TH colspan="2" class="MyInfoTitle">My Information</tH></TR>
<form action="index.php" enctype="application/x-www-form-urlencoded" method="POST">
	<input type="hidden" name="pg" value="MyInfo">
	<input type="hidden" name="UPDATE" value="UPDATE">
	

<?PHP

$Output = "";

if((isset($_SESSION["ODOSessionO"]->EscapedVars["UPDATE"]))&&(isset($_SESSION["ODOSessionO"]->EscapedVars["email"]))&&(isset($_SESSION["ODOSessionO"]->EscapedVars["fname"]))&&(isset($_SESSION["ODOSessionO"]->EscapedVars["lname"]))) {
	
	$email = htmlentities($_SESSION["ODOSessionO"]->EscapedVars["email"]);
	$fname = htmlentities($_SESSION["ODOSessionO"]->EscapedVars["fname"]);
	$lname = htmlentities($_SESSION["ODOSessionO"]->EscapedVars["lname"]);
	
	try {
		
		$_SESSION["ODOUserO"]->updateUserInfo($email, $lname, $fname);
		
		$Output .= "<TR><TH colspan=\"2\" class=\"MyInfoMessage\">Update complete.</TH></TR>";
		
	} catch(Exception $ex) {
		
		$GLOBALS['globalref'][4]->LogEvent("ODOUSERUPDATE", "Updating user information failed." . $ex->getMessage(), ODOUSERUPDATEERROR);
		
		$Output .= "<TR><TH colspan=\"2\" class=\"MyInfoError\">We had an error updating your information. We have been notified of this issue. Please try again later.</TH></TR>";
		
	}
	
}

$Output .= "<TR><TD>UserName:</TD><TD>" . $_SESSION["ODOUserO"]->getusername() . "</TD></TR>";

$Output .= "<TR><TD>E-mail Address:</TD><TD><input type=\"text\" name=\"email\" value=\"" . $_SESSION["ODOUserO"]->getemail() . "\" size=\"25\" maxlength=\"100\"></TD></TR>";

$Output .= "<TR><TD>First Name:</TD><TD><input type=\"text\" name=\"fname\" value=\"" . $_SESSION["ODOUserO"]->getfirstname() . "\" size=\"20\" maxlength=\"100\"></TD></TR>";


$Output .= "<TR><TD>Last Name:</TD><TD><input type=\"text\" name=\"lname\" value=\"" . $_SESSION["ODOUserO"]->getlastname() . "\" size=\"20\" maxlength=\"100\"></TD></TR>";

$Output .= "<TR><TD colspan=\"2\" class=\"MyInfoBlocksubmit\"><input type=\"submit\" name=\"submit\" value=\"submit\"></TD></TR></form>";

echo $Output;

?>
</table>
</center>
</div>
<div id="footer">
<img src="images/HR2.jpg" width="100%" height="2px">
<sub>"Someday, all the quotes in the world will be a number and a thought away...483920"~Some Guy&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<a href="index.php?pg=3">Contact Us</a>|<a href="index.php?pg=Legal">Legal</a>|<a href="index.php?pg=ODOStore">Products</a>|</sub>
<br>
</div>
</body>
</html>

<?PHP




?>