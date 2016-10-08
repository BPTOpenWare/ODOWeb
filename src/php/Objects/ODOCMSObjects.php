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

class ODOCMSObjects {
	
	var $ObjectSel;
	var $ObjectName;
	var $ObjectDesc;
	var $ObjectIsWhole;
	var $ObjectIsAdmin;
	var $ModID;
	var $CodeSegSel;
	var $CodeSegName;
	var $IsHeader;
	var $IsFooter;
	var $CodeContent;
	var $Content;

	function __construct() {
		$this->ObjectSel=0;
		$this->ObjectName="";
		$this->ObjectDesc="";
		$this->ObjectIsWhole=false;
		$this->ObjectIsAdmin=false;
		$this->ModID=0;
		$this->CodeSegSel=0;
		$this->CodeSegName="";
		$this->IsHeader=false;
		$this->IsFooter=false;
		$this->CodeContent="";
		$this->Content = "";

	}


	//******************************************************************************//
	//Function: ODOCMSPages::LoadODOMenus($MenuType)				//
	//Parameters: MenuType-integer: simple select statement choses the menu 	//
	//type to output								//
	//Description: Used by every ODO Object to create content to be displayed	//
	//for menus only								//
	//******************************************************************************//
	function LoadODOMenus() {
		
		//menu Object Selection
		//->New Object
		//->List Objects
		//
		
		
		//menu Object Permissions
		//->Add/Remove Pages
		//->Add/Remove Groups
		//
		
		//object menu 
		//->Edit Object
		//->List function
		//->Edit Functions
		//->New Function
		
		if($this->ObjectSel != 0)
		{
			
			$this->Content = $this->Content . "<br><table border=1><TR><TH colspan=\"2\"><B>Selected Object</B></TH></TR><TR><TD>Object Name: " . $this->ObjectName . "</TD><TD>ObjectID: " . $this->ObjectSel . "</td></tr>\n";
			$this->Content = $this->Content . "<TR><TD>ModID: </TD><TD>" . $this->ModID . "</td></TR></table>\n";

			if($this->CodeSegSel != 0) {
				$this->Content = $this->Content . "<BR><TABLE border=1><TR><TH><B>Selected function</B></TH></TR><TR><TD>Function Name:</TD><TD>" . $this->CodeSegName . "</TD></TR><TR><TD>Function ID:</TD><TD>" . $this->CodeSegSel . "</TD></TR></TABLE>\n";
			}

		}

	}

	function Select($ObjID) {
		if($this->ObjectSel != 0 ) {
			$this->ClearSelected();
		}

		if($ObjID < 1) {
			return false;
		}

		$query = "SELECT * FROM ObjectNames WHERE ObjID=" . $ObjID;
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		if($row = mysqli_fetch_assoc($TempRecords)) {
			$this->ObjectSel=$row["ObjID"];
			$this->ObjectName=$row["Name"];
			$this->ObjectDesc=$row["Description"];
			if($row["IsWhole"]==1) {
				$this->ObjectIsWhole=true;
			} else {
				$this->ObjectIsWhole=false;
			}

			if($row["IsAdmin"]==1) {
				$this->ObjectIsAdmin=true;
			} else {
				$this->ObjectIsAdmin=false;
			}

			$this->ModID=$row["ModID"];
			
			return true;
		} else {
			trigger_error("Object ID not found!", E_USER_ERROR);
			return false;
		}


	}

	function ClearSelected() {
		
		$this->ObjectSel=0;
		$this->ObjectName="";
		$this->ObjectDesc="";
		$this->ObjectIsWhole=false;
		$this->ObjectIsAdmin=false;
		$this->ModID=0;
		$this->ClearCode();
	}

	function ClearCode() {
		$this->CodeSegSel=0;
		$this->CodeSegName="";
		$this->IsHeader=false;
		$this->IsFooter=false;
		$this->CodeContent="";
	}

	function SelectCode($CodeID) {
		$this->ClearCode();

		if($CodeID < 1) {
			return false;
		}

		$query = "SELECT * FROM Objects WHERE CodeSegID=" . $CodeID;
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		if(!($row = mysqli_fetch_assoc($TempRecords))) {
			return false;
		} 

		$this->CodeSegSel=$row["CodeSegID"];
		$this->CodeSegName=$row["fnName"];
		$this->IsHeader=$row["IsHeader"];
		$this->IsFooter=$row["IsFooter"];
		$this->CodeContent=$row["Code"];

		return true;
	}
//end header


	function ListObjects() {
	
		if($this->ObjectSel != 0 ) {
			$this->ClearSelected();
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
		}

		$query = "SELECT * FROM ObjectNames ORDER BY Name ASC";
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

			$this->Content = $this->Content . "<center><br>\n<table border=1><TR><TH>Object ID</TH><TH>Object Name</TH><TH>Object Description</TH><TH>Edit Object</TH><TH>Delete Object</TH></TR>";
			while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
				$this->Content = $this->Content . "\n<TR><TD>" . $row["ObjID"] . "</TD><TD>" . $row["Name"] . "</TD><TD>" . $row["Description"] . "</TD>";
	
				$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"EditObject\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $row["ObjID"] . "\"><TD align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></FORM><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteObject\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $row["ObjID"] . "\"><TD align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></TD></FORM></TR>";

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
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSObjectsO&fn=ListObjects&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
			} else {
				$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSObjectsO&fn=ListObjects&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if($i > $NumofPages) {
				$this->Content = $this->Content . "&nbsp;Next->";
			} else {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSObjectsO&fn=ListObjects&PageNum=" . $i . "\">Next-></a>";
			}

			$this->Content = $this->Content . "</center>";
		}


	}

	function NewObject() {

		
		if($this->ObjectSel != 0 ) {
			$this->ClearSelected();
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["CreateNew"])) {
			if((!isset($_SESSION["ODOSessionO"]->EscapedVars["OName"]))&&(strlen($_SESSION["ODOSessionO"]->EscapedVars["OName"])<1)) {
				$this->Content = $this->Content . "<BR>\n<BR><CENTER><H3>You need to enter a name for the Object!</H3></center>";
				return;
			}

			$query = "SELECT * FROM ObjectNames WHERE Name='" . $_SESSION["ODOSessionO"]->EscapedVars["OName"] . "'";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<BR>\n<BR><CENTER><H3>Object Name <I>" . $_SESSION["ODOSessionO"]->EscapedVars["OName"] . "</I> already exists! Please choose a new name.</H3></center>";
				return;
			}
			
			
			$query = "INSERT INTO ObjectNames (Name, IsWhole, Description, IsAdmin, ModID) values('" . $_SESSION["ODOSessionO"]->EscapedVars["OName"] . "',";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsWhole"])) {
				$query = $query . "1,'";
			} else {
				$query = $query . "0,'";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["ODesc"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["ODesc"] . "',";
			} else {
				$query = $query . "NO DESCRIPTION',";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"])) {
				$query = $query . "1,";
			} else {
				$query = $query . "0,";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["MODS"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["MODS"] . ")";
			} else {
				$query = $query . "NULL)";
			}
			
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT * FROM ObjectNames WHERE Name='" . $_SESSION["ODOSessionO"]->EscapedVars["OName"] . "'";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(!($row = mysqli_fetch_assoc($TempRecords))) {
				trigger_error("Error adding object!", E_USER_ERROR);
			} else {
				$this->ObjectSel=$row["ObjID"];
				$this->ObjectName=$row["Name"];
				$this->ModID=$row["ModID"];
				$this->ObjectDesc=$row["Description"];
				if($row["IsWhole"] == 1) {
					$this->ObjectIsWhole=true;
				} else {
					$this->ObjectIsWhole=false;
				}
				
				if($row["IsAdmin"] == 1) {
					$this->ObjectIsAdmin=true;
				} else {
					$this->ObjectIsAdmin=false;
				}
	
				
				$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

				$this->LoadODOMenus();
		
				$this->Content = $this->Content . "<BR><BR><H3><B>Object Added!</B></H3><BR><a href=\"index.php?pg=ODOCMS&ob=ODOCMSObjectsO&fn=EditObject&ObjID=" . $this->ObjectSel . "\">Edit Object</a></CENTER>";

			}

		} else {

			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

			$this->LoadODOMenus();
		
			$this->Content = $this->Content . "</center>";

			$this->Content = $this->Content . "<BR><BR><CENTER><TABLE id=\"NewObjTable\" border=0><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"NewObject\"><input type=\"hidden\" name=\"CreateNew\" value=\"true\"><TD>Object Name: </TD><TD><input type=\"text\" name=\"OName\" size=\"20\" maxlength=\"254\"></TD></TR><TR><TD>Object Description: </TD><TD><textarea rows=\"4\" cols=\"40\" maxlength=\"254\" name=\"ODesc\"></textarea></TD></TR><TR><TD>Is Whole?</TD><TD><input type=\"checkbox\" name=\"IsWhole\" value=\"true\"></TD></TR><TR><TD>Is Admin Object?</TD><TD><input type=\"checkbox\" name=\"IsAdmin\" value=\"true\"></TD></TR><TR><TD>Part of Module:</TD><TD><SELECT name=\"MODS\">";

			$query = "SELECT ModID, ModName FROM Modules";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<option value=\"" . $row["ModID"] . "\"> " . $row["ModName"] . "</option>\n";

			}
			
			$this->Content = $this->Content . "</SELECT></TD></TR></TABLE><BR><BR><input type=\"submit\" name=\"Create Object\" value=\"Create Object\"></FORM></CENTER>";
		}


	}

	function AddRemovePages() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";
	
		if($this->ObjectSel == 0) {
			$this->Content = $this->Content . "<center><BR><H3><B>Please select an Object first.</B></H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["UpdateByMod"])) {
			$query = "SELECT PageID, ModID FROM ODOPages";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				if($row["ModID"] == $this->ModID) {
					$query = "insert into ODOPageObject(ObjID,PageID) values(" . $this->ObjectSel . "," . $row["PageID"] . ")";
					
					$TempRecords2 = $GLOBALS['globalref'][1]->Query($query);
				}
			
			}

			$this->Content = $this->Content . "<CENTER><H3>All Pages updated.</H3></center>";

			return;

		} elseif(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			$query = "DELETE FROM ODOPageObject WHERE ObjID=" . $this->ObjectSel;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT PageID FROM ODOPages";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["read" . $row["PageID"]])) {
					$query = "insert into ODOPageObject(ObjID,PageID) values(" . $this->ObjectSel . "," . $row["PageID"] . ")";
					
					$TempRecords2 = $GLOBALS['globalref'][1]->Query($query);
				}
			
			}

			$this->Content = $this->Content . "<CENTER><H3>All Pages updated.</H3></center>";

			return;

		} else {

			//add all with ModuleID
			//use checkbox
			$this->Content = $this->Content . "<center><BR><H2>Update Pages</H2><br><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Update\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"AddRemovePages\"><br>Add Objects to all Pages in Module?:&nbsp;<input id=\"chkModUpdate\" type=\"checkbox\" name=\"UpdateByMod\" value=\"true\" ><br><br><table border=1 id=\"tblpages\"><TR><TH>PageID</TH><TH>Page Name</TH><TH>Page Description</TH><TH>Is Admin</TH><TH>Is Dynamic</TH><TH>ModID</TH><TH>Add to Page?</TH></TR>";

			$query = "SELECT ODOPages.PageID, ODOPages.PageName, ODOPages.PageDesc, ODOPages.IsDynamic, ODOPages.IsAdmin, ODOPages.ModID, ODOPageObject.ObjID FROM ODOPages LEFT JOIN ODOPageObject on ODOPages.PageID = ODOPageObject.PageID AND ODOPageObject.ObjID=" . $this->ObjectSel . " ORDER BY PageName";

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
		
				$this->Content = $this->Content . "><TD>" . $row["PageID"] . "</TD><TD>" . $row["PageName"] . "</TD><TD>" . $row["PageDesc"] . "</TD><TD>" . $row["IsAdmin"] . "</TD><TD>" . $row["IsDynamic"] . "</TD><TD>" . $row["ModID"] . "</TD><TD><input id=\"read" . $i . "\" type=\"checkbox\" name=\"read" . $row["PageID"] . "\" value=\"true\" ";
				if($row["ObjID"] == $this->ObjectSel) {
					$this->Content = $this->Content . "checked";
				} 
				$i = $i + 1;
				$this->Content = $this->Content . "></TD></TR>";
			}

			$this->Content = $this->Content . "</TABLE><BR><BR><input type=\"button\" name=\"ClearMe\" value=\"Uncheck All\" onclick=\"ClearChecksinTable(" . $RecordCount . ")\">&nbsp;&nbsp;<input type=\"submit\" name=\"Update Objects In Pages\" value=\"Update Objects In Pages\"></FORM></CENTER>";

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

	function AddRemoveGroups() {
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if($this->ObjectSel == 0) {
			$this->Content = $this->Content . "<center><BR><H3><B>Please select an Object first.</B></H3></center>";
			return;
		}

		if($this->CodeSegSel == 0) {
			$this->Content = $this->Content . "<center><BR><H3><B>Please select a Function first.</B></H3></center>";
			return;
		}

		//add flag to change for all functions in object.
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["AllFunctions"])) {
				$MyArray = array();

				$query = "Select CodeSegID FROM Objects WHERE ObjID=" . $this->ObjectSel;
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);

				while($row = mysqli_fetch_assoc($TempRecords)) {
					$MyArray[$row["CodeSegID"]] = 1;
					$query = "DELETE FROM ODOObjectACL WHERE CodeSegID=" . $row["CodeSegID"];
					$TempRecords2 = $GLOBALS['globalref'][1]->Query($query);
				}
				if(isset($_POST['Groups'])) {
					$Groups = $_POST['Groups'];
					//go through each code seg and update groups
					foreach($MyArray as $ID=>$Value) {
						if ($Groups){
	 						foreach ( $Groups as $newg)
							{
					
								$query = "insert into ODOObjectACL (GID, CodeSegID) values(" . $newg . "," . $ID . ")";
								$TempRecords = $GLOBALS['globalref'][1]->Query($query);
							}
						}
					}
				}

				$this->Content = $this->Content . "<BR><center><H3><B>All Groups Updated for <I>ALL</I> Functions in this object.</B></H3></center>";
			

			} else {
				$query = "DELETE FROM ODOObjectACL WHERE CodeSegID=" . $this->CodeSegSel;
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);

				if(isset($_POST['Groups'])) {
					$Groups = $_POST['Groups'];
					foreach ( $Groups as $newg)
					{
					
						$query = "insert into ODOObjectACL (GID, CodeSegID) values(" . $newg . "," . $this->CodeSegSel . ")";
						$TempRecords = $GLOBALS['globalref'][1]->Query($query);
					}
				}
		
				$this->Content = $this->Content . "<BR><center><H3><B>All Groups Updated for Function.</B></H3></center>";
			
			}
			
		} else {

			$query = "SELECT ODOGroups.GID, ODOGroups.GroupName, ODOObjectACL.CodeSegID FROM ODOGroups LEFT JOIN ODOObjectACL on ODOGroups.GID=ODOObjectACL.GID AND ODOObjectACL.CodeSegID=" . $this->CodeSegSel;

			$this->Content = $this->Content . "<center><BR><BR><H3>Select Groups for Function</H3><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Update\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"AddRemoveGroups\"><SELECT name=\"Groups[]\" multiple>";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "\n<option value=\"" . $row["GID"] . "\"";
				
				if($row["CodeSegID"] == $this->CodeSegSel) {
					$this->Content = $this->Content . " selected";
				}
				$this->Content = $this->Content . ">" . $row["GroupName"] . "</option>";
		
			}

			
			$this->Content = $this->Content . "\n</SELECT><br>Make change for all Functions in group? <input type=\"checkbox\" name=\"AllFunctions\"><BR>\n<input type=\"submit\" name=\"ChgCat\" value=\"Update\"></form></center>";
		}
		
	}

	function EditObject() {

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["ObjID"])) {
			if($this->ObjectSel != $_SESSION["ODOSessionO"]->EscapedVars["ObjID"]) {
				if(!($this->Select($_SESSION["ODOSessionO"]->EscapedVars["ObjID"]))) {
					$this->Content = "<center><H3>Object ID not found!</H3></center>";
				}
			}
		} else {
			if($this->ObjectSel == 0) {
				$this->Content = "<center><H3>You must select an Object to edit first!</H3></center>";
				return;
			} 
			//else we use the currently selected Object
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			if((!isset($_SESSION["ODOSessionO"]->EscapedVars["OName"]))&&(strlen($_SESSION["ODOSessionO"]->EscapedVars["OName"])<1)) {
				$this->Content = $this->Content . "<BR>\n<BR><CENTER><H3>You need to enter a name for the Object!</H3></center>";
				return;
			}


			$query = "SELECT * FROM ObjectNames WHERE Name='" . $_SESSION["ODOSessionO"]->EscapedVars["OName"] . "' AND ObjID !=" . $this->ObjectSel;

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<BR>\n<BR><CENTER><H3>Object Name <I>" . $_SESSION["ODOSessionO"]->EscapedVars["OName"] . "</I> already exists! Please choose a new name.</H3></center>";
				return;
			}
			
			
			$query = "UPDATE ObjectNames SET Name='" . $_SESSION["ODOSessionO"]->EscapedVars["OName"] . "', IsWhole=";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsWhole"])) {
				$query = $query . "1,";
			} else {
				$query = $query . "0,";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["ODesc"])) {
				$query = $query . " Description='" . $_SESSION["ODOSessionO"]->EscapedVars["ODesc"] . "',";
			} else {
				$query = $query . " Description='NO DESCRIPTION',";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"])) {
				$query = $query . " IsAdmin=1,";
			} else {
				$query = $query . " IsAdmin=0,";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["MODS"])) {
				$query = $query . " ModID=" . $_SESSION["ODOSessionO"]->EscapedVars["MODS"];
			} else {
				$query = $query . " ModID=NULL";
			}
			
			$query = $query . " WHERE ObjID=" . $this->ObjectSel;
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT * FROM ObjectNames WHERE Name='" . $_SESSION["ODOSessionO"]->EscapedVars["OName"] . "'";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(!($row = mysqli_fetch_assoc($TempRecords))) {
				trigger_error("Error updating object!", E_USER_ERROR);
			} else {
				$this->ObjectSel=$row["ObjID"];
				$this->ObjectName=$row["Name"];
				$this->ModID=$row["ModID"];
				$this->ObjectDesc=$row["Description"];
				if($row["IsWhole"]==1) {
					$this->ObjectIsWhole=true;
				} else {
					$this->ObjectIsWhole=false;
				}

				if($row["IsAdmin"]==1) {
					$this->ObjectIsAdmin=true;
				} else {
					$this->ObjectIsAdmin=false;
				}

				$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

				$this->LoadODOMenus();
		
				$this->Content = $this->Content . "<BR><BR><H3><B>Object Updated!</B></H3></CENTER>";

			}

		} else {

			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

			$this->LoadODOMenus();
		
			$this->Content = $this->Content . "</center>";

			$this->Content = $this->Content . "<BR><BR><CENTER><TABLE border=0><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["ObjID"] . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"EditObject\"><input type=\"hidden\" name=\"Update\" value=\"true\"><TD>Object Name: </TD><TD>";

			$this->Content = $this->Content . "<input type=\"text\" name=\"OName\" size=\"20\" maxlength=\"254\" value=\"" . $this->ObjectName . "\"></TD></TR><TR><TD>Object Description: </TD><TD><textarea rows=\"4\" cols=\"40\" maxlength=\"254\" name=\"ODesc\">" . $this->ObjectDesc . "</textarea></TD></TR><TR><TD>Is Whole?</TD><TD><input type=\"checkbox\" name=\"IsWhole\" value=\"true\" ";

			if($this->ObjectIsWhole) {
				$this->Content = $this->Content . "checked";
			}

			$this->Content = $this->Content . "></TD></TR><TR><TD>Is Admin Object?</TD><TD><input type=\"checkbox\" name=\"IsAdmin\" value=\"true\" ";

			if($this->ObjectIsAdmin) {
				$this->Content = $this->Content . "checked";
			}

			$this->Content = $this->Content . "></TD></TR><TR><TD>Part of Module:</TD><TD><SELECT name=\"MODS\">";

			$query = "SELECT ModID, ModName FROM Modules";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<option value=\"" . $row["ModID"] . "\" ";
	
				if($this->ModID==$row["ModID"]) {
					$this->Content = $this->Content . "selected";
				}

				$this->Content = $this->Content . "> " . $row["ModName"] . "</option>\n";
			}
			
			$this->Content = $this->Content . "</SELECT></TD></TR></TABLE><BR><BR><input type=\"submit\" name=\"Update Object\" value=\"Update Object\"></FORM></CENTER>";
		}
	}

	function DeleteObject() {
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["ObjID"])) && ($_SESSION["ODOSessionO"]->EscapedVars["ObjID"] != $this->ObjectSel)) {
			if(!($this->Select($_SESSION["ODOSessionO"]->EscapedVars["ObjID"]))) {
				$this->Content = "<center><H3>Error selecting Object!</H3></center>";
				return;
			}
		}

		if($this->ObjectSel == 0) {
			$this->Content = "<center><H3>You must select an Object to delete!</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Confirm"])) {
			$query = "DELETE FROM ObjectNames WHERE ObjID=" . $this->ObjectSel;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT CodeSegID FROM Objects WHERE ObjID=" . $this->ObjectSel;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($TempRecords) > 0) {
				while($row = mysqli_fetch_assoc($TempRecords)) {
					$query = "DELETE FROM ODOObjectACL WHERE CodeSegID=" . $row["CodeSegID"];
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				}
			}

			$query = "DELETE FROM ODOPageObject WHERE ObjID=" . $this->ObjectSel;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "DELETE FROM Objects WHERE ObjID=" . $this->ObjectSel;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->ClearSelected();
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

			$this->LoadODOMenus();
		
			$this->Content = $this->Content . "</center>";

			$this->Content = $this->Content . "<BR><BR><Center><H3><B>Object Deleted!</B></H3></center>";

		} else {
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

			$this->LoadODOMenus();
		
			$this->Content = $this->Content . "</center>";

			$this->Content = $this->Content . "<BR><BR><CENTER><Table border=0><TR><TD>Are you sure you want to delete Object?&nbsp;</TD><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteObject\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["ObjID"] . "\"><TD align=\"center\"><input type=\"submit\" name=\"Confirm\" value=\"Delete\"></TD></FORM></TR></TABLE></CENTER>";
		}
	}

	function ListFunctions() {
		if($this->ObjectSel == 0) {
			$this->Content = "<CENTER><H3>You must select an object before viewing the objects functions!</H3></center>";
			return;	
		}

		if($this->CodeSegSel != 0) {
			$this->ClearCode();
		}

		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		$query = "SELECT CodeSegID, fnName, IsHeader, IsFooter FROM Objects WHERE ObjID=" . $this->ObjectSel;
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		$this->Content = $this->Content . "<center><br>\n<table border=1><TR><TH>Function ID</TH><TH>Function Name</TH><TH>Is Header</TH><TH>Is Footer</TH><TH>Edit Function</TH><TH>Delete Object</TH></TR>";
		while($row = mysqli_fetch_assoc($TempRecords)) {
			$this->Content = $this->Content . "\n<TR><TD>" . $row["CodeSegID"] . "</TD><TD>" . $row["fnName"] . "</TD><TD>" . $row["IsHeader"] . "</TD><TD>" . $row["IsFooter"] . "</TD>";
	
			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"EditFunctions\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $this->ObjectSel . "\"><input type=\"hidden\" name=\"fnid\" value=\"" . $row["CodeSegID"] . "\"><TD align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></FORM><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteFunction\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $this->ObjectSel . "\"><input type=\"hidden\" name=\"fnid\" value=\"" . $row["CodeSegID"] . "\"><TD align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></TD></FORM></TR>";

		}

		$this->Content = $this->Content . "</table><br><br></CENTER>";

	}

	function DeleteFunction() {
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["fnid"])) && ($_SESSION["ODOSessionO"]->EscapedVars["fnid"] != $this->CodeSegSel)) {
			if(!($this->SelectCode($_SESSION["ODOSessionO"]->EscapedVars["fnid"]))) {
				$this->Content = "<center><H3>Error selecting Code!</H3></center>";
				return;
			}
		}

		if($this->CodeSegSel == 0) {
			$this->Content = "<center><H3>You must select a function to delete!</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Confirm"])) {
			$query = "DELETE FROM Objects WHERE CodeSegID=" . $this->CodeSegSel;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "DELETE FROM ODOObjectACL WHERE CodeSegID=" . $this->CodeSegSel;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->ClearCode();
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

			$this->LoadODOMenus();
		
			$this->Content = $this->Content . "</center>";

			$this->Content = $this->Content . "<BR><BR><Center><H3>Function Deleted!</H3></center>";

		} else {
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

			$this->LoadODOMenus();
		
			$this->Content = $this->Content . "</center>";

			$this->Content = $this->Content . "<BR><BR><CENTER><Table border=0><TR><TD>Are you sure you want to delete function?&nbsp;</TD><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Confirm\" value=\"true\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $this->ObjectSel . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteFunction\"><input type=\"hidden\" name=\"fnid\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["fnid"] . "\"><TD align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></TD></FORM></TR></TABLE></CENTER>";
		}
	}

	function NewFunction() {

		if($this->ObjectSel == 0) {
			$this->Content = $this->Content . "<CENTER><H3>You must select an Object to edit first!</H3></center>";
			return;
		}

		if($this->CodeSegSel != 0) {
			$this->ClearCode();
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["NewFun"])) {

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["FName"])) {
				if((strlen($_SESSION["ODOSessionO"]->EscapedVars["FName"])<1)) {
					$this->Content = $this->Content . "<BR>\n<BR><CENTER><H3>You need to enter a name for the Function!</H3></center>";
					return;
				}
			}

			$query = "SELECT * FROM Objects WHERE fnName='" . $_SESSION["ODOSessionO"]->EscapedVars["FName"] . "' AND ObjID=" . $this->ObjectSel;

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<BR>\n<BR><CENTER><H3>Function Name <I>" . $_SESSION["ODOSessionO"]->EscapedVars["FName"] . "</I> already exists! Please choose a new name.</H3></center>";
				return;
			}
			
			$query = "INSERT INTO Objects (fnName, Code, IsHeader, IsFooter, ObjID) values('" . $_SESSION["ODOSessionO"]->EscapedVars["FName"] . "',";

			if((isset($_FILES['userfile']))&&($_FILES['userfile']['size']>0)) {
				if($_FILES['userfile']['error'] != UPLOAD_ERR_OK) {
					trigger_error("File Upload Error: " . $_FILES['userfile']['error']);
					exit();
				} else {
					if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
						$fileoutput = file_get_contents($_FILES['userfile']['tmp_name']);
						$query = $query . "'" . $GLOBALS['globalref'][1]->EscapeMe($fileoutput) . "'";
					} else {
						trigger_error("File Upload Name not valid!: " . $_FILES['userfile']['tmp_name'], E_USER_ERROR);
					}	
				}
					
			} else {
				if((isset($_SESSION["ODOSessionO"]->EscapedVars["pagecontent"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["pagecontent"]) > 0)) {
					$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["pagecontent"] . "'";
				} else {
					$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["FName"] . "(){\n//place holder\n}\n'";
				}
			}
			
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsHeader"])) {
				$query = $query . ",1,";
			} else {
				$query = $query . ",0,";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsFooter"])) {
				$query = $query . "1," . $this->ObjectSel . ")";
			} else {
				$query = $query . "0," . $this->ObjectSel . ")";
			}
				
			$Result = $GLOBALS['globalref'][1]->Query($query);

			$query = "Select CodeSegID FROM Objects WHERE fnName='" . $_SESSION["ODOSessionO"]->EscapedVars["FName"] . "'";
			$Result = $GLOBALS['globalref'][1]->Query($query);

			if(!($row = mysqli_fetch_assoc($Result))) {
				trigger_error("Error adding function!", E_USER_ERROR);
			} else {
				$this->ClearCode();
				$this->SelectCode($row["CodeSegID"]);
				$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

				$this->LoadODOMenus();

				$this->Content = $this->Content . "<br><H3><B>Function has been created.</B></H3></center>";
			}	

		} else {
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

			$this->LoadODOMenus();
		
			$this->Content = $this->Content . "</center>";

			if($this->ObjectIsWhole) {
				//check if function already exists
				$query = "SELECT fnName FROM Objects WHERE ObjID=" . $this->ObjectSel;
				
				$Result = $GLOBALS['globalref'][1]->Query($query);

				if(mysqli_num_rows($Result) > 0) {
						$this->Content = $this->Content . "<BR><BR><Center><B>You already have a function for this object and the object is set to be a whole object. You may place your entire class definition in your previously created function or uncheck the IsWhole option for the object.</B></CENTER>";
						return;
				}
			} 	
			
			$this->Content = $this->Content . "<BR><BR><CENTER><form action=\"index.php\" enctype=\"multipart/form-data\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"NewFunction\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $this->ObjectSel . "\"><input type=\"hidden\" name=\"NewFun\" value=\"true\"><TABLE border=0><TR><TD>Function Name: </TD><TD><input type=\"text\" name=\"FName\" size=\"20\" maxlength=\"254\"></TD></TR>";

			if($this->ObjectIsWhole) {
				$this->Content = $this->Content . "<TR><TD>Header of Object:</TD><TD><B>Object is Whole!</B></TD></TR><TR><TD>Footer of Object:</TD><TD><B>Object is Whole!</B></TD></TR>";
			} else {
				$this->Content = $this->Content . "<TR><TD>Is Header?:</TD><TD><input id=\"imaheader\" onclick=\"HeadFootClick()\" type=\"checkbox\" name=\"IsHeader\" value=\"true\"></TD></TR><TR><TD>Is Footer?:</TD><TD><input id=\"imafooter\" onclick=\"HeadFootClick()\" type=\"checkbox\" name=\"IsFooter\" value=\"true\"></TD></TR>";
			}

			$this->Content = $this->Content . "</TD></TR>\n</Table><BR><BR>\nUpload File?<input type=\"checkbox\" value=\"false\" name=\"UploadFile\" onclick=\"ShowUpload()\"><br>\n<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5000000\" />File To Upload (Max 5MB): <input id=\"imafile\" name=\"userfile\" type=\"file\" disabled/><br><textarea id=\"imnotafile\" NAME=\"pagecontent\" COLS=120 ROWS=40></textarea><br><br><input type=\"submit\" name=\"Update\" value=\"Update\"></form></center>";

		}

	}

	function EditFunctions() {

		if($this->ObjectSel == 0) {
			$this->Content = $this->Content . "<CENTER><H3>You must select an Object to edit first!</H3></center>";
			return;
		}

		if((isset($_SESSION["ODOSessionO"]->EscapedVars["fnid"])) && ($_SESSION["ODOSessionO"]->EscapedVars["fnid"]!=$this->CodeSegSel)) {
			if(!($this->SelectCode($_SESSION["ODOSessionO"]->EscapedVars["fnid"]))) {
				$this->Content = $this->Content . "<CENTER><H3>Error selecting function!</H3></center>";
				return;
			}
		}


		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if($this->CodeSegSel==0) {
			//not selected - list
			//new
			//delete function
			$this->Content = $this->Content . "<CENTER><H3>You must select a Function to edit first!</H3></center>";
			return;
		} else {
			
			
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
				//update only, don't loose the CodeSegID
				
				$query = "UPDATE Objects SET fnName='";

				if((isset($_SESSION["ODOSessionO"]->EscapedVars["FunctionName"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["FunctionName"]) > 0)) {
					$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["FunctionName"] . "', IsHeader=";
				} else {
					$this->Content = $this->Content . "<CENTER><H3>You can not have a function with no name!</H3></center>";
					return;
				}

				if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsHeader"])) {
					$query = $query . "1, IsFooter=";
				} else {
					$query = $query . "0, IsFooter=";
				}

				if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsFooter"])) {
					$query = $query . "1, Code=";
				} else {
					$query = $query . "0, Code=";
				}

				
			if((isset($_FILES['userfile']))&&($_FILES['userfile']['size']>0)) {
				if($_FILES['userfile']['error'] != UPLOAD_ERR_OK) {
					trigger_error("File Upload Error: " . $_FILES['userfile']['error']);
					exit();
				} else {
					if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
						$fileoutput = file_get_contents($_FILES['userfile']['tmp_name']);
						$query = $query . "'" . $GLOBALS['globalref'][1]->EscapeMe($fileoutput) . "'";
					} else {
						trigger_error("File Upload Name not valid!: " . $_FILES['userfile']['tmp_name'], E_USER_ERROR);
					}	
				}
					
			} else {
				if((isset($_SESSION["ODOSessionO"]->EscapedVars["pagecontent"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["pagecontent"]) > 0)) {
					$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["pagecontent"] . "'";
				} else {
					$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["FName"] . "(){\n//place holder\n}\n'";
				}
			}
			
				
				$query = $query . " WHERE CodeSegID=" . $_SESSION["ODOSessionO"]->EscapedVars["fnid"];
				$Result = $GLOBALS['globalref'][1]->Query($query);

				$this->ClearCode();
				$this->SelectCode($_SESSION["ODOSessionO"]->EscapedVars["fnid"]);

				$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSObjects");

				$this->LoadODOMenus();

				$this->Content = $this->Content . "<br><H3><B>Function has been updated.</B></H3></center>";
				
			} else {
				$this->Content = $this->Content . "<BR><BR><CENTER><TABLE border=0><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSObjectsO\"><input type=\"hidden\" name=\"fn\" value=\"EditFunctions\"><input type=\"hidden\" name=\"ObjID\" value=\"" . $this->ObjectSel . "\"><input type=\"hidden\" name=\"Update\" value=\"true\"><input type=\"hidden\" name=\"fnid\" value=\"" . $this->CodeSegSel . "\"><TD>Function Name: </TD><TD>";

				$this->Content = $this->Content . "<input type=\"text\" name=\"FunctionName\" size=\"20\" maxlength=\"254\" value=\"" . $this->CodeSegName . "\"></TD></TR>\n";

				if($this->ObjectIsWhole) {
					$this->Content = $this->Content . "<TR><TD>Is Header?</TD><TD><B>Object is Whole!</B></TD></TR><TR><TD>Is Footer?</TD><TD><B>Object is Whole!</B>";
			
				} else {

					$this->Content = $this->Content . "<TR><TD>Is Header?</TD><TD><input type=\"checkbox\" name=\"IsHeader\" value=\"true\" id=\"imaheader\" onclick=\"HeadFootClick()\"";

					if($this->IsHeader) {
						$this->Content = $this->Content . " checked></TD></TR>\n<TR><TD>Is Footer?</TD><TD><input type=\"checkbox\" name=\"IsFooter\" id=\"imafooter\" value=\"true\" onclick=\"HeadFootClick()\" disabled>";
					} elseif($this->IsFooter) {
						$this->Content = $this->Content . " disabled></TD></TR>\n<TR><TD>Is Footer?</TD><TD><input type=\"checkbox\" name=\"IsFooter\" id=\"imafooter\" value=\"true\" onclick=\"HeadFootClick()\" checked>";
					} else {
						$this->Content = $this->Content . "></TD></TR>\n<TR><TD>Is Footer?</TD><TD><input type=\"checkbox\" name=\"IsFooter\" id=\"imafooter\" value=\"true\" onclick=\"HeadFootClick()\">";
					}

				}

				$this->Content = $this->Content . "</TD></TR>\n</Table><BR><BR>\nUpload File?<input type=\"checkbox\" value=\"false\" name=\"UploadFile\" onclick=\"ShowUpload()\"><br>\n<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5000000\" />File To Upload (Max 5MB): <input id=\"imafile\" name=\"userfile\" type=\"file\" disabled/><br><textarea id=\"imnotafile\" NAME=\"pagecontent\" COLS=120 ROWS=40>" . htmlspecialchars($this->CodeContent) . "</textarea><br><br><input type=\"submit\" name=\"Update\" value=\"Update\"></form></center>";
			}
		}
	}


//begin footer
	function __wakeup() {
		$this->Content = "";
	}

}




?>