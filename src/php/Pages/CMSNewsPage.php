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

//News admin page
$ODOCMSNewsO;
$ODOMenuO;


if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOMenuO")) {
	$ODOMenuO = new ODOMenu;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOMenuO", $ODOMenuO);

} else {

	$ODOMenuO =& $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"];
}

if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSNewsO")) {
	$ODOCMSNewsO = new ODOCMSNews;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSNewsO", $ODOCMSNewsO);
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "ListPosts");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "NewPost");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "EditPost");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "DeletePost");

	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "DeteletCategory");	
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "AddRemoveGroups");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "EditCategory");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "NewCategory");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "ListCategories");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSNewsO", "ODOCMSNews", "DeleteCategory");

} else {

	$ODOCMSNewsO =& $_SESSION["ODOSessionO"]->UserObjectArray["ODOCMSNewsO"];
}

?>

<!DOCTYPE HTML>
<html>
<head>

<title>
<?PHP

echo SERVERNAME;

?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel='stylesheet' type='text/css' href='css/global.css'>
<link rel='stylesheet' type='text/css' href='css/menus.css'>
<script type="text/javascript" src="js/odoweb.js"></script>
<script type="text/javascript">
var _editor_url  = document.location.href.replace(/index.php.*/, 'Xinha/')

var _editor_lang = "en";
</script>
<!-- Load up the actual editor core -->
<script type="text/javascript" src="Xinha/XinhaCore.js"></script>
<script type="text/javascript">

var xinha_plugins =
[
 'Linker'
];
var xinha_editors =
[
  'article'
];

function xinha_init()
{
  if(!Xinha.loadPlugins(xinha_plugins, xinha_init)) return;

  var xinha_config = new Xinha.Config();

  xinha_editors = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);

  Xinha.startEditors(xinha_editors);
}
Xinha.addOnloadHandler(xinha_init);
</script>

</head>
<body>
<div class="LeftNav">
<center>
<br>
<div id="logo"></div>
<br>

<?PHP

echo $ODOMenuO->GetODOLeftMenu("Admin");

?>

<br><br><br><br>
<sub>Bluff Point Tech LLC &copy</sub>
</center>
</div>
<div class="Right">

<?php

$Output = "";
if(isset($_SESSION["FirstLoadNews"])) {
	if((isset($_SESSION["ODOSessionO"]->EscapedVars["ob"] ))&&($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSNewsO")) {
		$Output = $Output . $ODOCMSNewsO->Content . "\n</div>\n</body>\n</html>";
	} else {
		 $ODOCMSNewsO->ListPosts();
        $Output = $Output . $ODOCMSNewsO->Content . "\n</div>\n</body>\n</html>";
	}


} else {
	$_SESSION["FirstLoadNews"] = true;
        //make a call to load up News Edit content.
        $ODOCMSNewsO->ListPosts();
        $Output = $Output . $ODOCMSNewsO->Content . "\n</div>\n</body>\n</html>";
	
}

echo($Output);




?>