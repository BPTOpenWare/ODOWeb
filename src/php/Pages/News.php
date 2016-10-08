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
<head>
<title>
<?php
echo SERVERNAME
?></title>
<link rel='stylesheet' type='text/css' href='css/global.css'>
<link rel='stylesheet' type='text/css' href='css/menus.css'>

<script type="text/javascript" src="js/odoweb.js"></script>
<style type="text/css">
.loginbox {
	padding: 0px;
	top:0px;
	left:0px;
  margin-top: 0px;
  margin-left: 0px;
  margin-bottom: 0px;  
  margin-right: 1em;  
position:relative;
}
</style>
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

<?php

//Load up by Post first then Cat then Date if no parameters are specified then we need to load all 
//cats we have permission for. The result is displayed by the dynamic news page which everyone in the
//system has access to.
if(isset($_SESSION["ODOSessionO"]->EscapedVars["PostID"])) {
	//verify they have access rights to it also
	//if guest though we need to check based on guest group and not guest userid
	//this allows guests to be in the system but not have a guest login id

	if(!is_numeric($_SESSION["ODOSessionO"]->EscapedVars["PostID"])) {
		trigger_error("PostID is not numeric! Your IP and this event have been logged.", E_USER_ERROR);
		exit(1);
	}

	if($_SESSION["ODOUserO"]->getIsGuest()) {

		$query = "SELECT news.PriKey, news.title, news.Article, news.Date, newsCats.CatName, news.CatID from news, newsCats where news.AllowGuest=1 and news.CatID=newsCats.PriKey and news.PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"];

	} else {

		$query = "SELECT distinct news.PriKey, news.title, news.Article, news.Date, newsCats.CatName, news.CatID FROM news, ODOUserGID, newsGACL, newsCats where ODOUserGID.UID=" . $_SESSION["ODOUserO"]->getUID() . " and ODOUserGID.GID=newsGACL.GID and newsGACL.newsGID=news.CatID and news.CatID=newsCats.PriKey and news.PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"];

	}

	

} else {
	//else we are going to do a standard search given params and permissions
	

	if($_SESSION["ODOUserO"]->getIsGuest()) {
		$query = "SELECT news.PriKey, news.title, news.Article, news.Date, newsCats.CatName, news.CatID FROM news, newsCats where news.AllowGuest=1 and news.CatID=newsCats.PriKey";
	
	} else {
		$query = "SELECT distinct news.PriKey, news.title, news.Article, news.Date, newsCats.CatName, news.CatID FROM news, ODOUserGID, newsGACL, newsCats where ODOUserGID.UID=" . $_SESSION["ODOUserO"]->getUID() . " and ODOUserGID.GID=newsGACL.GID and newsGACL.newsGID=news.CatID and news.CatID=newsCats.PriKey";
	}

	

	if(isset($_SESSION["ODOSessionO"]->EscapedVars["CatID"])) {
		
		if(!is_numeric($_SESSION["ODOSessionO"]->EscapedVars["CatID"])) {
			trigger_error("CatID is not numeric! Your IP and this event have been logged.", E_USER_ERROR);
			exit(1);
		}

		$query = $query . " and news.CatID=" . $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
	}
	
	if(isset($_SESSION["ODOSessionO"]->EscapedVars["sDate"])) {

		$query = $query . " and news.Date>=" . date("Y-m-d", strtotime($_SESSION["ODOSessionO"]->EscapedVars["sDate"]));

	}

	if(isset($_SESSION["ODOSessionO"]->EscapedVars["eDate"])) {

		$query = $query . " and news.Date<=" . date("Y-m-d", strtotime($_SESSION["ODOSessionO"]->EscapedVars["eDate"]));
	}
}

$query = $query . " order by news.Date DESC";
$TempRecords = $GLOBALS['globalref'][1]->Query($query);

$Output = "<div id=\"MiddleContent\">";

while ($row = mysqli_fetch_assoc($TempRecords)) {

		date_default_timezone_set('America/Chicago');
		
		//format date
		$datefromdb = $row["Date"];
		$year = substr($datefromdb,0,4);
		$mon  = substr($datefromdb,5,2);
		$day  = substr($datefromdb,8,2);
		//$hour = substr($datefromdb,8,2);
		//$min  = substr($datefromdb,10,2);
		//$sec  = substr($datefromdb,12,2);
		//$orgdate = date("l F dS, Y h:i A",mktime($hour,$min,$sec,$mon,$day,$year));
		$mktimeDate = mktime( 0, 0, 0,$mon, $day, $year);

		$Output = $Output . "<div class=\"post\"><a href=\"index.php?pg=News&PostID=" . $row["PriKey"] . "\"><h2 class=\"title\">" . $row["title"] . "</h2></a>";

		$Output = $Output . "<div class=\"meta\"><p>Posted under <a href=\"index.php?pg=News&CatID=" . $row["CatID"] . "\">" . $row["CatName"] . "</a>&nbsp;&nbsp;Date Posted:" . date('F', $mktimeDate) . "&nbsp;" . date('j', $mktimeDate) . ", " . date('Y', $mktimeDate) . "</p></div>";
		
		//story
		//we only output the entire story if PostID is set in Get
		if(isset($_GET["PostID"])) {
			$Output = $Output . "<div class=\"story\">" . $row["Article"] . "</div></div>";   
		} else {

			$Output = $Output . "<div class=\"story\">" . strip_tags(substr($row["Article"], 0, 700), '<p><a>');

			$Output = $Output . "&nbsp;&nbsp;<a href=\"index.php?pg=News&PostID=" . $row["PriKey"] . "\">Full Article..</a></div></div>";
		}

	}


$Output = $Output . "</div>";

//Right column
$query = "select * from newsCats";
$TempRecords = $GLOBALS['globalref'][1]->Query($query);

$Output = $Output . "<div id=\"RightContent\"><img src=\"images/News.jpg\"><br><br><div id=\"menu\" class=\"boxed\"><center><h2 class=\"title\">Categories</h2></center><div class=\"content\"><ul>";

if(!mysqli_num_rows($TempRecords))
{
	//do nothing since no Cats are available
} else {
	while ($row = mysqli_fetch_assoc($TempRecords)) {
		$Output = $Output . "<li class=\"active\"><a href=\"index.php?pg=News&CatID=" . $row["PriKey"] . "\">" . $row["CatName"] . "</a></li>";
	
	}
}

$Output = $Output . "</ul></div></div>";

//Login Box
$Output = $Output . "<br><br><br>";

//check if we are logged in. If we are guests then we are not logged in
if($_SESSION["ODOUserO"]->getIsGuest()) {
	
	$Output = $Output . $_SESSION["ODOUserO"]->GetLoginForm(false) . "</div>";

} else {
	
	$Output = $Output . "<div id=\"menu\" class=\"boxed\"><center><h2 class=\"title\">Logged In As</h2></center><div class=\"content\"><center><B>" . $_SESSION["ODOUserO"]->getusername() . "</B><BR><form action=\"index.php\" method=\"POST\" enctype=\"application/x-www-form-urlencoded\"><input type=\"hidden\" name=\"pg\" value=\"" . $_SESSION["ODOSessionO"]->pg . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOUserO\"><input type=\"hidden\" name=\"fn\" value=\"LogoutPublic\"><input type=\"hidden\" name=\"ObjectOnlyOutput\" value=\"1\"><input type=\"submit\" name=\"logout\" value=\"logout\"></form></center></div></div></div>";

}

$Output = $Output . "</body></html>";


echo $Output;

?>