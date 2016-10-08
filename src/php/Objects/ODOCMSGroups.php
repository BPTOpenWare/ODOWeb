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

class ODOCMSGroups {

	var $GID;
	var $GroupName;
	var $GroupDesc;
	var $Content;

	function __construct() {
		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["GID"])) {
			$this->GID = $_SESSION["ODOSessionO"]->EscapedVars["GID"];
			$this->SelectGroup($this->GID);
			
		} else {
			$this->ClearSelected();
		}
		
		$this->Content = "";

	}

	
	//******************************************************************************//
	//Function: ODOCMSPages::LoadODOMenus($MenuType)				//
	//Parameters: MenuType-integer: simple select statement choses the menu 	//
	//type to output								//
	//Description: Used by every ODO Object to create content to be displayed	//
	//for menus only								//
	//******************************************************************************//
	private function LoadODOMenus() {
		
		//menu Group selection
		//Groups
		//->New Groups
		//->List Groups
		//->Edit Group Name
		
		//menu Permissions
		//Permissions
		//->Update users in group
		//->Update pages in group
		//->Update Objects in group
		
		
		//Permissions
		//Menu Placement
		if($this->GID != 0)
		{
			
			$this->Content = $this->Content . "<br><table border=1><TH><B>Selected Group</B></TH><TR><TD>GroupID: </TD><TD>" . $this->GID . "</TD></TR><TR><TD>Group Name: </TD><TD>" . $this->GroupName . "</td></tr>\n</table>\n";

		}
		

	}

	private function SelectGroup($NewGID) {
		$query = "select * from ODOGroups WHERE GID=" . $NewGID;
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		if($row = mysqli_fetch_assoc($TempRecords)) {

			$this->GID = $row["GID"];
			$this->GroupName = $row["GroupName"];
			$this->GroupDesc = $row["GroupDesc"];
			
		} else {
			trigger_error("Group ID not found!", E_USER_ERROR);
		}

	}

	private function ClearSelected() {
		$this->GID = 0;
		$this->GroupName = "";
		$this->GroupDesc = "";
	}

//end header

	function NewGroup() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if($this->GID != 0 ) {
			$this->ClearSelected();
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["CreateGroup"])) {
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["GName"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["GName"]) < 1)) {
				$this->Content = $this->Content . "<BR>\n<BR><CENTER><H3>You need to enter a name for the group!</H3></center>";
				return;
			}

			$query = "select * from ODOGroups WHERE GroupName='" . $_SESSION["ODOSessionO"]->EscapedVars["GName"] . "'";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<BR>\n<BR><CENTER><H3>Group Name <I>" . $_SESSION["ODOSessionO"]->EscapedVars["GName"] . "</I> already exists! Please choose a new name.</H3></center>";
				return;
			}

			$query = "INSERT INTO ODOGroups (GroupName, GroupDesc) values('" . $_SESSION["ODOSessionO"]->EscapedVars["GName"] . "','";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["GroupDesc"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["GroupDesc"];
			} else {
				$query = $query . "NO DESCRIPTION";
			}

			$query = $query . "')";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT * FROM ODOGroups WHERE GroupName='" . $_SESSION["ODOSessionO"]->EscapedVars["GName"] . "'";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(!($row = mysqli_fetch_assoc($TempRecords))) {
				trigger_error("Error adding group!", E_USER_ERROR);
			} else {
				$this->SelectGroup($row["GID"]);
				$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

				$this->LoadODOMenus();
		
				$this->Content = $this->Content . "<BR><BR><H2><B>Group Added!</B></H2></CENTER>";

			}

		} else {
			$this->Content = $this->Content . "<br>\n<center><H2><B>Create Group</B></H2><br><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"NewGroup\"><input type=\"hidden\" name=\"CreateGroup\" value=\"CreateGroup\"><table border=1><TR><TD>Group Name:</TD><TD><input type=\"text\" name=\"GName\" size=\"20\" maxlength=\"254\"></TD></TR><TR><TD>Group Description:</TD><TD><textarea rows=\"4\" cols=\"40\" maxlength=\"254\" name=\"GroupDesc\"></textarea></TD></TR></Table><br><br><input type=\"submit\" name=\"Create\" value=\"Create\"></FORM></center>";

		}

	}

	function ListGroups() {
		if($this->GID != 0 ) {
			$this->ClearSelected();
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

	
		//output cats in pages.
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
		}

		$query = "SELECT * FROM ODOGroups";
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		$RecordCount = mysqli_num_rows($TempRecords);
		$NumofPages = $RecordCount / 15;
		$Count = 0;

		if( ($RecordCount % 15 > 0) ) {
			$NumofPages = $NumofPages + 1;
		}

		if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
			$this->Content = $this->Content . "<center><h3>Page number is out of bounds!</h3><br><br></center>";
		} else {
			$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
			mysqli_data_seek($TempRecords, $ChangeToRow);

			$this->Content = $this->Content . "<center><br>\n<table border=1><TR><TH>Group ID</TH><TH>Group Name</TH><TH>Group Description</TH><TH>Edit Group</TH><TH>Delete Group</TH></TR>";
			while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
				$this->Content = $this->Content . "\n<TR><TD>" . $row["GID"] . "</TD><TD>" . $row["GroupName"] . "</TD><TD>" . $row["GroupDesc"] . "</TD>";
	
				$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"EditGroup\"><input type=\"hidden\" name=\"GID\" value=\"" . $row["GID"] . "\"><TD align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></FORM><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteGroup\"><input type=\"hidden\" name=\"GID\" value=\"" . $row["GID"] . "\"><TD align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></TD></FORM></TR>";

				$Count = $Count + 1;
			}

			$this->Content = $this->Content . "</table><br><br>";

			$CurPage = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"];
		
			//we want to count down the Pages
			$NumberofLinks = 1;
			//$i is the current position
			$i = $CurPage - 4;
			if($i < 1) {
				$i = 1;
			}

			if(($CurPage > 5)) {
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSGroupsO&fn=ListGroups&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
			} else {
				$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSGroupsO&fn=ListGroups&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if($i > $NumofPages) {
				$this->Content = $this->Content . "&nbsp;Next->";
			} else {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSGroupsO&fn=ListGroups&PageNum=" . $i . "\">Next-></a>";
			}

			$this->Content = $this->Content . "</center>";
		}
	}

	function DeleteGroup() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["GID"])) {

			if($_SESSION["ODOSessionO"]->EscapedVars["GID"] != $this->GID) {
				$this->ClearSelected();
				$this->SelectGroup($_SESSION["ODOSessionO"]->EscapedVars["GID"]);
			}
		} else {
			$this->Content = $this->Content . "<center><BR><H3>Incorrect Parameters passed to function. Please call this function from a valid area of ODOCMS.</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["ConfirmDelete"])) {
			$query = "DELETE FROM ODOUserGID WHERE GID=" . $this->GID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "DELETE FROM ODOGACL WHERE GID=" . $this->GID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "DELETE FROM ODOMenus WHERE GID=" . $this->GID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "DELETE FROM ODOGroups WHERE GID=" . $this->GID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->ClearSelected();
			$this->Content = $this->Content . "<center><BR><H2><B>Group Deleted!</B></H2></center>";
			
		} else {
			$this->Content = $this->Content . "\n<center><br><br><H2>Delete Group?</H2><br><table border=0><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteGroup\"><input type=\"hidden\" name=\"GID\" value=\"" . $this->GID . "\"><input type=\"hidden\" name=\"ConfirmDelete\" value=\"Yes\"><TD align=\"center\"><input type=\"submit\" name=\"Yes\" value=\"Yes\"></TD></FORM><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"ListGroups\"><TD align=\"center\"><input type=\"submit\" name=\"No\" value=\"No\"></TD></FORM></TR></table></center>";	

		}


	}

	function EditGroup() {
		
		if($this->GID == 0) {
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

			$this->LoadODOMenus();

			$this->Content = $this->Content . "<BR><BR>\n<H3>Please select a group to edit!</H3></center>";
			return;
			
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			if((!isset($_SESSION["ODOSessionO"]->EscapedVars["GroupName"])) || (strlen($_SESSION["ODOSessionO"]->EscapedVars["GroupName"]) < 1)) {
				$this->Content = $this->Content . "<br><BR><center><br>\n<H3>Group Name is not valid!</H3></center>";
				return;
			} 
	
			$query = "SELECT * FROM ODOGroups WHERE GroupName='" . $_SESSION["ODOSessionO"]->EscapedVars["GroupName"] . "'";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($row = mysqli_fetch_assoc($TempRecords)) {
				if($row["GID"] != $this->GID) {
					$this->Content = $this->Content . "<BR><BR><CENTER><BR>\n<H3>Group Name already exists!</H3></center>";
					return;
				}
			}

			$query = "UPDATE ODOGroups SET GroupName='" . $_SESSION["ODOSessionO"]->EscapedVars["GroupName"] . "', GroupDesc='";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["GroupDesc"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["GroupDesc"] . "' ";
			} else {
				$query = $query . "NO DESCRIPTION' ";
			}

			$query = $query . "WHERE GID=" . $this->GID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

			$this->LoadODOMenus();
		
			$this->Content = $this->Content . "<BR><BR><H3>Group Updated.</H3></center>";

		} else {
			$this->Content = $this->Content . "<BR><CENTER><H2><B>Edit Group</B></H2><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"EditGroup\"><input type=\"hidden\" name=\"GID\" value=\"" . $this->GID . "\"><input type=\"hidden\" name=\"Update\" value=\"Yes\">\n<table border=1><TR><TD>GID:</TD><TD>" . $this->GID . "</TD></TR>\n<TR><TD>Group Name:</TD><TD><input type=\"text\" name=\"GroupName\" value=\"" . $this->GroupName . "\" size=\"20\" maxlength=\"254\"></TD></TR><TR><TD>Group Description:</TD><TD><textarea rows=\"4\" cols=\"40\" maxlength=\"254\" name=\"GroupDesc\">" . $this->GroupDesc . "</textarea></TD></TR></TABLE><BR><BR><input type=\"submit\" name=\"Update Group\" value=\"Update Group\"></form></center>";

		}

	}

	function UpdateUsers() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if($this->GID == 0) {
			$this->Content = $this->Content . "<center><BR><H3><B>Please select a Group first.</B></H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			
			$query = "DELETE FROM ODOUserGID WHERE GID=" . $this->GID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT UID FROM ODOUsers";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["is" . $row["UID"]])) {
					$query = "insert into ODOUserGID(GID,UID) values(" . $this->GID . "," . $row["UID"] . ")";
					
					$TempRecords2 = $GLOBALS['globalref'][1]->Query($query);
				}
			
			}

			$this->Content = $this->Content . "<CENTER><H3>All Users Selected Added to Group.</H3></center>";
			
		} else {

			$query = "SELECT ODOUsers.UID,ODOUsers.user,ODOUsers.emailadd, ODOUsers.rnameLast,ODOUsers.rnameFirst,ODOUserGID.GID FROM ODOUsers LEFT JOIN ODOUserGID ON ODOUsers.UID=ODOUserGID.UID AND ODOUserGID.GID=" . $this->GID . " ORDER BY ODOUsers.user";

			$this->Content = $this->Content . "<center><BR><H2><B>Update Users</B></H2><br><br>\n<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"UpdateUsers\"><input type=\"hidden\" name=\"Update\" value=\"Update\"><input type=\"hidden\" name=\"GID\" value=\"" . $this->GID . "\"><table border=1><TR><TH>UID</TH><TH>Username</TH><TH>e-mail</TH><TH>First Name</TH><TH>Last Name</TH><TH>In Group</TH></TR>\n";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			$i = 0;
			$RecordCount = mysqli_num_rows($TempRecords);
			$NumofPages = $RecordCount / 15;
			$NumofPages = intval($NumofPages);

			if( ($RecordCount % 15 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<TR id=\"rownum" . $i . "\"";

				if($i > 14) {
					$this->Content = $this->Content . " style=display:none";
				}
		
				$this->Content = $this->Content . "><TD>" . $row["UID"] . "</TD><TD>" . $row["user"] . "</TD><TD>" . $row["emailadd"] . "</TD><TD>" . $row["rnameFirst"] . "</TD><TD>" . $row["rnameLast"] . "</TD><TD><input id=\"is" . $row["UID"] . "\" type=\"checkbox\" name=\"is" . $row["UID"] . "\" value=\"true\" ";
				if($row["GID"] == $this->GID) {
					$this->Content = $this->Content . "checked></TD>";
				} else {
					$this->Content = $this->Content . "></TD>";
					
				}
				$i = $i + 1;
				$this->Content = $this->Content . "</TR>";
			}

			$this->Content = $this->Content . "</TABLE><BR><BR><input type=\"submit\" name=\"Update Group Users\" value=\"Update Group Users\"></FORM></CENTER>";
			//ODOWeb javascript page view
			if($NumofPages > 1) {
				$i = 1;
				$this->Content = $this->Content . "<center><BR><BR><a href=\"javascript:ChangePage(-1, " . $RecordCount . ", " . $NumofPages . ")\"><-Previous</a>&nbsp;&nbsp;";
				while($i <= $NumofPages) {
					if($i%10 == 0) {
							$this->Content = $this->Content . "<BR>";
					}
					$this->Content = $this->Content . "<a id=\"LinkID" . $i . "\" href=\"javascript:ChangePage(" . $i . ", " . $RecordCount . ", " . $NumofPages . ")\">" . $i . "</a>&nbsp;";
					$i = $i + 1;
				}

				$this->Content = $this->Content . "&nbsp;<a href=\"javascript:ChangePage(-2, " . $RecordCount . ", " . $NumofPages . ")\">Next-></a><center>";
			}	
			
		}
	}

	function UpdatePages() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if($this->GID == 0) {
			$this->Content = $this->Content . "<center><BR><H3><B>Please select a Group first.</B></H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			$query = "DELETE FROM ODOGACL WHERE GID=" . $this->GID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT PageID FROM ODOPages";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["read" . $row["PageID"]])) {
					$query = "insert into ODOGACL(GID,PageID,WriteOK) values(" . $this->GID . "," . $row["PageID"] . ",";
					if(isset($_SESSION["ODOSessionO"]->EscapedVars["write" . $row["PageID"]])) {
						$query = $query . "1)";
					} else {
						$query = $query . "0)";
					}
					$TempRecords2 = $GLOBALS['globalref'][1]->Query($query);
				}
			
			}

			$this->Content = $this->Content . "<CENTER><H3>All Pages updated.</H3></center>";

		} else {

			$this->Content = $this->Content . "<center><BR><H2>Update Pages</H2><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Update\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"UpdatePages\"><input type=\"hidden\" name=\"GID\" value=\"" . $this->GID . "\"><table border=1><TR><TH>PageID</TH><TH>Page Name</TH><TH>Page Description</TH><TH>Is Admin</TH><TH>Is Dynamic</TH><TH>ModID</TH><TH>Read</TH><TH>Write</TH></TR>";

			$query = "SELECT ODOPages.PageID, ODOPages.PageName, ODOPages.PageDesc, ODOPages.IsDynamic, ODOPages.IsAdmin, ODOPages.ModID, ODOGACL.GID, ODOGACL.WriteOK FROM ODOPages LEFT JOIN ODOGACL on ODOPages.PageID = ODOGACL.PageID AND ODOGACL.GID=" . $this->GID;

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			$i = 0;
			$RecordCount = mysqli_num_rows($TempRecords);
			$NumofPages = $RecordCount / 15;
			$NumofPages = intval($NumofPages);

			if( ($RecordCount % 15 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<TR id=\"rownum" . $i . "\"";

				if($i > 14) {
					$this->Content = $this->Content . " style=display:none";
				}
		
				$this->Content = $this->Content . "><TD>" . $row["PageID"] . "</TD><TD>" . $row["PageName"] . "</TD><TD>" . $row["PageDesc"] . "</TD><TD>" . $row["IsAdmin"] . "</TD><TD>" . $row["IsDynamic"] . "</TD><TD>" . $row["ModID"] . "</TD><TD><input id=\"read" . $row["PageID"] . "\" type=\"checkbox\" name=\"read" . $row["PageID"] . "\" value=\"true\" onclick=\"GroupReadClick(" . $row["PageID"] . ")\" ";
				if($row["GID"] == $this->GID) {
					$this->Content = $this->Content . "checked></TD><TD><input id=\"write" . $row["PageID"] . "\" type=\"checkbox\" value=\"true\" name=\"write" . $row["PageID"] . "\" ";
					if($row["WriteOK"] == 1) {
						$this->Content = $this->Content . "checked";
					}
					$this->Content = $this->Content . ">";
				} else {
					$this->Content = $this->Content . "></TD><TD><input id=\"write" . $row["PageID"] . "\" type=\"checkbox\" value=\"true\" name=\"write" . $row["PageID"] . "\" disabled>";
					
				}
				$i = $i + 1;
				$this->Content = $this->Content . "</TD></TR>";
			}

			$this->Content = $this->Content . "</TABLE><BR><BR><input type=\"submit\" name=\"Update Group Rights\" value=\"Update Group Rights\"></FORM></CENTER>";
			//ODOWeb javascript page view
			if($NumofPages > 1) {
				$i = 1;
				$this->Content = $this->Content . "<center><BR><BR><a href=\"javascript:ChangePage(-1, " . $RecordCount . ", " . $NumofPages . ")\"><-Previous</a>&nbsp;&nbsp;";
				while($i <= $NumofPages) {
					$this->Content = $this->Content . "<a id=\"LinkID" . $i . "\" href=\"javascript:ChangePage(" . $i . ", " . $RecordCount . ", " . $NumofPages . ")\">" . $i . "</a>&nbsp;";
					$i = $i + 1;
				}

				$this->Content = $this->Content . "&nbsp;<a href=\"javascript:ChangePage(-2, " . $RecordCount . ", " . $NumofPages . ")\">Next-></a><center>";
			}	
		}
	}

	function UpdateObjects() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSGroups");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";
		
		if($this->GID == 0) {
			$this->Content = $this->Content . "<center><BR><H3><B>Please select a Group first.</B></H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			$ObjectArray = array();
			$CodeSegArray = array();
			$IsWhole = array();

			$query = "SELECT * FROM ObjectNames";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["Allow" . $row["ObjID"]])) {
					$ObjectArray[$row["ObjID"]] = true;
				} else {
					$ObjectArray[$row["ObjID"]] = false;
				}
				if($row["IsWhole"]==1) {
					$IsWhole[$row["ObjID"]] = true;
				} else {
					$IsWhole[$row["ObjID"]] = false;
				}
			}

			//clear out 
			$query = "delete from ODOObjectACL WHERE GID=" . $this->GID;

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);


			$query = "SELECT CodeSegID, IsHeader, IsFooter, ObjID FROM Objects";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			

			while($row = mysqli_fetch_assoc($TempRecords)) {
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["code" . $row["CodeSegID"]])) {
					$CodeSegArray[$row["CodeSegID"]] = true;
					
				} else {
					if((isset($IsWhole[$row["ObjID"]]))&&($IsWhole[$row["ObjID"]])&&($ObjectArray[$row["ObjID"]])) {
						
						$CodeSegArray[$row["CodeSegID"]] = true;
					} else {
						
						$CodeSegArray[$row["CodeSegID"]] = false;
					}

					if((($row["IsHeader"])||($row["IsFooter"]))&&($ObjectArray[$row["ObjID"]])) {
						$CodeSegArray[$row["CodeSegID"]] = true;
					}
				}
				
			}

			
			foreach( $CodeSegArray as $CodeSeg=>$Value) {
				if($Value) {
					$query="INSERT INTO ODOObjectACL (CodeSegID,GID) values(" . $CodeSeg . "," . $this->GID . ")";
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				}
			}

			$this->Content = $this->Content . "<center><H3>Objects Added to Group!</H3></center>";
			
			
		} else {
			
			$ObjectArray = array();
			$ObjectIsWhole = array();
			$ObjectDesc = array();
			$ObjectIsSet = array();
			$ObjectNameIsSet = array();
			$RecordCount=0;
			$NumofPages=0;

			$query = "SELECT * FROM ObjectNames";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			$RecordCount = mysqli_num_rows($TempRecords);
			$NumofPages = $RecordCount / 15;
			$NumofPages = intval($NumofPages);

			if( ($RecordCount % 15 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			//prime array
			while($row = mysqli_fetch_assoc($TempRecords)) {
				$ObjectArray[$row["ObjID"]]=$row["Name"];
				$ObjectDesc[$row["ObjID"]]=$row["Description"];
				if($row["IsWhole"]==1) {
					$ObjectIsWhole[$row["ObjID"]]=true;
				} else {
					$ObjectIsWhole[$row["ObjID"]]=false;
				}
			}

			$query = "SELECT Objects.CodeSegID, ObjID FROM Objects, ODOObjectACL WHERE Objects.CodeSegID=ODOObjectACL.CodeSegID AND ODOObjectACL.GID=" . $this->GID;

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$ObjectIsSet[$row["CodeSegID"]]=$row["ObjID"];
				
				$ObjectNameIsSet[$row["ObjID"]]=true;
			}

			$query= "SELECT Objects.CodeSegID, Objects.fnName, Objects.ObjID, Objects.IsFooter, Objects.IsHeader, ODOObjectACL.GID FROM Objects LEFT JOIN ODOObjectACL on Objects.CodeSegID=ODOObjectACL.CodeSegID AND ODOObjectACL.GID=" . $this->GID . " ORDER BY ObjID";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->Content = $this->Content . "<center><BR><H2><B>Update Objects</B></H2><br><br>\n<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSGroupsO\"><input type=\"hidden\" name=\"fn\" value=\"UpdateObjects\"><input type=\"hidden\" name=\"GID\" value=\"" . $this->GID . "\"><input type=\"hidden\" name=\"Update\" value=\"Update\"><table border=1><TR><TH>Object Name</TH><TH>Object Description</TH><TH>Grant Access</TH><TH>Function Access</TH></TR>";
	
			$TempObjID=-1;
			$i=0;
			while($row = mysqli_fetch_assoc($TempRecords)) {
				if($TempObjID != $row["ObjID"]) {
					//then we are on a new row. Generate
					if($TempObjID != -1) {
						if((!isset($ObjectArray[$TempObjID])) || (isset($ObjectIsWhole[$TempObjID]))&&(!$ObjectIsWhole[$TempObjID])) {
							$this->Content = $this->Content . "</TABLE>";
						} 
						$this->Content = $this->Content . "</td></TR>";
					} 
					
					if($i > 14) {
						$this->Content = $this->Content . "<TR id=\"rownum" . $i . "\" style=display:none>";
					} else {
						$this->Content = $this->Content . "<TR id=\"rownum" . $i . "\">";
					}

					$this->Content = $this->Content . "\n<TD>";

					if(isset($ObjectArray[$row["ObjID"]])) {
						$this->Content = $this->Content . $ObjectArray[$row["ObjID"]];
					} else {
						$this->Content = $this->Content . "NO OBJECT FOUND!";
					}

					$this->Content = $this->Content . "</td><TD>";

					if(isset($ObjectDesc[$row["ObjID"]])) {
						$this->Content = $this->Content . $ObjectDesc[$row["ObjID"]];
					} else {
						$this->Content = $this->Content . "NO OBJECT FOUND! You should run a check on linking in your database.";
					}

					$this->Content = $this->Content . "</TD><TD><input type=\"checkbox\" id=\"Allow" . $row["ObjID"] . "\" name=\"Allow" . $row["ObjID"] . "\" value=\"true\"";

					if((isset($ObjectIsWhole[$row["ObjID"]]))&&($ObjectIsWhole[$row["ObjID"]])) {
						if(isset($ObjectNameIsSet[$row["ObjID"]])) {
							$this->Content = $this->Content . " checked";
						}

						$this->Content = $this->Content . "></TD><TD>Object is whole and can not be selected per method.";
						
					} else {
						
						$this->Content = $this->Content . " onclick=\"ObjectReadClick(" . $row["ObjID"] . ")\"";

						if(isset($ObjectNameIsSet[$row["ObjID"]])) {
							$this->Content = $this->Content . " checked></TD><TD><table id=\"ObjTbl" . $row["ObjID"] . "\" border=0>";
						} else {
							$this->Content = $this->Content . "></TD><TD><table id=\"ObjTbl" . $row["ObjID"] . "\" border=0 style=display:none>";
						}

					}

					
					$i=$i+1;
				}

				$TempObjID = $row["ObjID"];
				if((!isset($ObjectArray[$row["ObjID"]]))||((isset($ObjectIsWhole[$row["ObjID"]]))&&(!$ObjectIsWhole[$row["ObjID"]]))) {

					$this->Content = $this->Content . "<TR><TD>" . $row["fnName"] . "()</TD><TD><input type=\"checkbox\" name=\"code" . $row["CodeSegID"] . "\" value=\"true\"";
					if(isset($ObjectIsSet[$row["CodeSegID"]])) {
						$this->Content = $this->Content . " checked";
					}
					$this->Content = $this->Content . "></TD></TR>\n";
					
				}
			}
			
			if((isset($ObjectIsWhole[$TempObjID]))&&(!$ObjectIsWhole[$TempObjID])) {
				$this->Content = $this->Content . "</TABLE>";
			}
 
			$this->Content = $this->Content . "</TD></TR></TABLE><BR><BR><input type=\"submit\" name=\"Update Group Rights\" value=\"Update Group Rights\"></FORM></CENTER>";
			//ODOWeb javascript page view
			if($NumofPages > 1) {
				$i = 1;
				$this->Content = $this->Content . "<center><BR><BR><a href=\"javascript:ChangePage(-1, " . $RecordCount . ", " . $NumofPages . ")\"><-Previous</a>&nbsp;&nbsp;";
				while($i <= $NumofPages) {
					$this->Content = $this->Content . "<a id=\"LinkID" . $i . "\" href=\"javascript:ChangePage(" . $i . ", " . $RecordCount . ", " . $NumofPages . ")\">" . $i . "</a>&nbsp;";
					$i = $i + 1;
				}

				$this->Content = $this->Content . "&nbsp;<a href=\"javascript:ChangePage(-2, " . $RecordCount . ", " . $NumofPages . ")\">Next-></a><center>";
			}


		}
	}

//begin footer

	function __wakeup() {
		$this->Content = "";
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["GID"])) {
			$this->GID = $_SESSION["ODOSessionO"]->EscapedVars["GID"];
			$this->SelectGroup($this->GID);
			
		} 
	}
	
	
	function __sleep() {
		$this->Content = "";
		return( array_keys( get_object_vars( $this ) ) );
	}
	
}


?>