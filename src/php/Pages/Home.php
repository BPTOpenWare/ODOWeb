<?php

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
</center>
<BR><BR>
<span class="sectiontitle">ODOWeb Content Management System</span>
<a href="images/screen.jpg"><div class="floatright"><img src="images/screen.jpg" width="350" height="200"></div></a>
<P>ODOWeb is yet another content management system. It is geared towards developers with small businesses as clients. ODOWeb provides group level access to pages, menus, objects, and methods of objects. This means one user could have access to methods a, b, and c on an object while another user may have access to a, b, c, and d. For users that do not have access rights to methods of an object the method is simply not loaded or available for that user. Some of ODOweb's features are listed below. </P>
<UL>
<LI>Logging system that records a user's ID, asign a severity level, and add custom comments.</LI>
<LI>E-mail the admin when logging reaches a given severity level.</LI>
<LI>Dynamic Menu system based off of user rights, conditional request variables, and CSS styles</LI>
<LI>Group level permissions on pages, menus, objects, and methods.</LI>
<LI>Mycrpt encryption support with per user Initialization Vectors.</LI>
<LI>MD5SUM, SHA256, SHA512 hashing support.</LI>
<LI>Basic news system with categorical viewing which can be associated to groups.</LI>
<LI><a href="index.php?pg=ODOWeb">Read more...</a></LI>
</UL>

<BR><BR>
<span class="sectiontitle">ODOMuse Live Song Tagging</span>
<P>ODOMuse is a web service designed for artists to promote sales during live performances. Please visit <a href="http://www.odomuse.com">http://www.odomuse.com/</a> for more information.</P>
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