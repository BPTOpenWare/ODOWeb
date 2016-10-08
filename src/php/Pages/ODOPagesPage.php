<?php
//ODOCMS

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
$ODOCMSPagesO;
$ODOCMSUsersO;
$ODOCMSGroupsO;
$ODOCMSObjectsO;
$ODOCMSMenusO;
$ODOCMSLogsO;
$ODOCMSRegisterO;
$ODOCMSConstantsO;

if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOMenuO")) {
	$ODOMenuO = new ODOMenu;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOMenuO", $ODOMenuO);

} else {

	$ODOMenuO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOMenuO");
}

if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSGroupsO")) {
	$ODOCMSGroupsO = new ODOCMSGroups;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSGroupsO", $ODOCMSGroupsO);

	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSGroupsO", "ODOCMSGroups", "EditGroup");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSGroupsO", "ODOCMSGroups", "DeleteGroup");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSGroupsO", "ODOCMSGroups", "ListGroups");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSGroupsO", "ODOCMSGroups", "NewGroup");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSGroupsO", "ODOCMSGroups", "UpdateUsers");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSGroupsO", "ODOCMSGroups", "UpdatePages");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSGroupsO", "ODOCMSGroups", "UpdateObjects");

} else {

	$ODOCMSGroupsO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOCMSGroupsO");

}

if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSPagesO")) {
	$ODOCMSPagesO = new ODOCMSPages;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSPagesO", $ODOCMSPagesO);
	
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "ListCat");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "DeleteCat");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "NewCat");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "SearchPages");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "CatView");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "DeletePage");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "ListPages");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "NewPage");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "EditPage");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "AddRemoveGroups");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSPagesO", "ODOCMSPages", "AddRemoveObjects");
	


} else {
	$ODOCMSPagesO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOCMSPagesO");
}

if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSUsersO")) {
	$ODOCMSUsersO = new ODOCMSUsers;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSUsersO", $ODOCMSUsersO);
	
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "lstUsers");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "lstByGroups");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "NewUser");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "DeleteUser");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "EditUser");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "ChangeUserPassword");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "EditGroups");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "ShowUserLogs");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSUsersO", "ODOCMSUsers", "Search");

} else {
	$ODOCMSUsersO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOCMSUsersO");
}


if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSObjectsO")) {
	$ODOCMSObjectsO = new ODOCMSObjects;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSObjectsO", $ODOCMSObjectsO);
	
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "ListObjects");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "NewObject");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "AddRemovePages");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "AddRemoveGroups");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "EditObject");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "DeleteObject");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "ListFunctions");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "DeleteFunction");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "NewFunction");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSObjectsO", "ODOCMSObjects", "EditFunctions");
	
} else {
	$ODOCMSObjectsO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOCMSObjectsO");
}


if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSMenusO")) {
	$ODOCMSMenusO = new ODOCMSMenus;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSMenusO", $ODOCMSMenusO);

	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "ListHeads");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "EditTree");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "NewMenuItem");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "EditMenuItem");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "DeleteMenuItem");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "MoveUp");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "MoveDown");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "NewHead");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "DeleteTree");
        $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSMenusO", "ODOCMSMenus", "EditPermissions");

} else {

	$ODOCMSMenusO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOCMSMenusO");

}

if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSLogsO")) {
	$ODOCMSLogsO = new ODOCMSLogs;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSLogsO", $ODOCMSLogsO);

	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSLogsO", "ODOCMSLogs", "ViewByType");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSLogsO", "ODOCMSLogs", "ViewBySeverity");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSLogsO", "ODOCMSLogs", "ViewByTimeStamp");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSLogsO", "ODOCMSLogs", "UserLoginLogoutReport");

} else {

	$ODOCMSLogsO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOCMSLogsO");

}



if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSRegisterO")) {
	$ODOCMSRegisterO = new ODOCMSRegister;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSRegisterO", $ODOCMSRegisterO);

	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSRegisterO", "ODOCMSRegister", "VerifyReg");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSRegisterO", "ODOCMSRegister", "DeleteRegRequest");
    $_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSRegisterO", "ODOCMSRegister", "ListPending");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSRegisterO", "ODOCMSRegister", "ViewRegTypes");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSRegisterO", "ODOCMSRegister", "EditEmail");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSRegisterO", "ODOCMSRegister", "PrevVerified");
	
} else {

	$ODOCMSRegisterO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOCMSRegisterO");

}


if(!$_SESSION["ODOSessionO"]->IsRegistered("ODOCMSConstantsO")) {
	$ODOCMSConstantsO = new ODOCMSConstants;
	$_SESSION["ODOSessionO"]->RegisterObject("ODOCMSConstantsO", $ODOCMSConstantsO);

	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSConstantsO", "ODOCMSConstants", "ViewConstants");
	$_SESSION["ODOSessionO"]->RegisterPublicFunction("ODOCMSConstantsO", "ODOCMSConstants", "EditConstants");
	
} else {

	$ODOCMSConstantsO = $_SESSION["ODOSessionO"]->GetObjectRef("ODOCMSConstantsO");

}

$Output = "<!DOCTYPE HTML><html>\n<head>\n<title>ODOWeb Admin</title>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n<link rel='stylesheet' type='text/css' href='css/global.css'>\n<link rel='stylesheet' type='text/css' href='css/menus.css'>";

$Output = $Output . "\n<script type=\"text/javascript\" src=\"js/odoweb.js\">\n </script>\n</head>\n<body>\n<div class=LeftNav>\n<center>\n<br>\n<div id=\"logo\"></div>\n<br>";

$Output = $Output . $ODOMenuO->GetODOLeftMenu("Admin");
$Output = $Output . "<br><br><br><br>\n<sub>Bluff Point Tech LLC &copy</sub>\n</center>\n</div>\n\n<div class=Right>";

if((isset($_SESSION["FirstLoad"]))&&(isset($_SESSION["ODOSessionO"]->EscapedVars["ob"]))) {
	if($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSPagesO") {
		$Output = $Output . $ODOCMSPagesO->Content . "\n</div>\n</body>\n</html>";
	} elseif($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSGroupsO") {
		$Output = $Output . $ODOCMSGroupsO->Content . "\n</div>\n</body>\n</html>";
	} elseif($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSUsersO") {
		$Output = $Output . $ODOCMSUsersO->Content . "\n</div>\n</body>\n</html>";
	} elseif($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSObjectsO") {
		$Output = $Output . $ODOCMSObjectsO->Content . "\n</div>\n</body>\n</html>";
	} elseif($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSMenusO") {
		$Output = $Output . $ODOCMSMenusO->Content . "\n</div>\n</body>\n</html>";
	} elseif($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSLogsO") {
		$Output = $Output . $ODOCMSLogsO->Content . "\n</div>\n</body>\n</html>";
	} elseif($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSRegisterO") {
		$Output = $Output . $ODOCMSRegisterO->Content . "\n</div>\n</body>\n</html>";
	} elseif($_SESSION["ODOSessionO"]->EscapedVars["ob"] == "ODOCMSConstantsO") {
		$Output = $Output . $ODOCMSConstantsO->Content . "\n</div>\n</body>\n</html>";
	} else {
		//who knows what was passed just thorw out the default.
		$Output = $Output . "<center><H2><B>Welcome to ODOWeb!</B></H2></center><BR><BR></DIV></BODY></HTML>";
	}

} else {
	$_SESSION["FirstLoad"] = true;
	$Output = $Output . "<center><H2><B>Welcome to ODOWeb!</B></H2></center><BR><BR></DIV></BODY></HTML>";
}

echo($Output);


?>
