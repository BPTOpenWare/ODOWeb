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

//Create and register CSRF object

$testCSRFObject;

if(!$_SESSION["ODOSessionO"]->IsRegistered("testCSRFObject")) {
	
	$testCSRFObject = new TestCSRFObject();
	
	$_SESSION["ODOSessionO"]->RegisterObject("testCSRFObject", $testCSRFObject);
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("testCSRFObject", "testCSRFObject", "requestTestTwo");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("testCSRFObject", "testCSRFObject", "requestTestOne");
	
} else {

	$testCSRFObject =& $_SESSION["ODOSessionO"]->UserObjectArray["testCSRFObject"];
}

//Create a new token

//output token on post form

	//uncoment to test calling object with test one function
	$ourToken = $_SESSION["ODOSessionO"]->generateCSRFToken(true, "TestCSRF", "testCSRFObject", "requestTestOne");

	//uncoment to test calling object with test two function
	$ourToken = $_SESSION["ODOSessionO"]->generateCSRFToken(true, "TestCSRF", "testCSRFObject", "requestTestTwo");
		
	//uncoment to test calling object into any page test two function
	$ourToken = $_SESSION["ODOSessionO"]->generateCSRFToken(true, null, "testCSRFObject", "requestTestOne");

	$ourToken = $_SESSION["ODOSessionO"]->generateCSRFToken(true, null, "testCSRFObject", "requestTestTwo");
	
	//page test
	$ourToken = $_SESSION["ODOSessionO"]->generateCSRFToken(true, "TestCSRF", null, null);
	

?>
<!DOCTYPE HTML>
<html>
<head><title>Bluff Point Technologies LLC</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="author" content="Bluff Point Technologies LLC">
<link rel='stylesheet' type='text/css' href='css/global.css'>
<link rel='stylesheet' type='text/css' href='css/menus.css'>
<script type="text/javascript" src="js/odoweb.js"></script>
</head>
<body>
<div><B>Testing CSRF</B></div>
<BR>
<BR>
<div>Current testCSRF test one:<?php echo $testCSRFObject->getTestVarOne(); ?></div>

<div>Current testCSRF test two:<?php echo $testCSRFObject->getTestVarTwo(); ?></div>
<BR>
<BR>
<div><B>Test valid request</B></div>
<form action="index.php" enctype="application/x-www-form-urlencoded" method="POST">
	<input type="hidden" name="pg" value="TestCSRF">
	<input type="hidden" name="odowebcsrf" value="<?php echo $ourToken;?>">
	<!-- 
	<input type="hidden" name="ob" value="testCSRFObject">
	<input type="hidden" name="fn" value="requestTestOne">
	 -->
	<input type="submit" name="submit" value="Test Valid Request">

</form>
</body>
</html>
<?php 


?>