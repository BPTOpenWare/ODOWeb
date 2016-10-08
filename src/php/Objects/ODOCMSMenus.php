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

class ODOCMSMenus {
	
	var $Position;
	var $NameArray;
	var $URLArray;
	var $ChildrenArray;
	var $ParentArray;
	var $IsHeadArray;
	var $Content;
	var $SelectedHead;
	var $SelectedMenuItem;

	function __construct() {
		//check if UID is set
		$this->SelectedHead = "";
		$this->SelectedMenuItem = 0;
		$this->Position = 0;
		$this->NameArray = array();
		$this->URLArray = array();
		$this->ChildrenArray = array(array());
		$this->IsHeadArray = array();
		$this->ParentArray = array();
		$this->Content = "";
		$this->LoadMenus();

	}

	
	//******************************************************************************//
	//Function: ODOCMSPages::LoadODOMenus($MenuType)				//
	//Parameters: MenuType-integer: simple select statement choses the menu 	//
	//type to output								//
	//Description: Used by every ODO Object to create content to be displayed	//
	//for menus only								//
	//******************************************************************************//
	private function LoadODOMenus() {
		
		//menu MENU Selection
		//->List Menus
		//->New Head
		//->Edit Tree
		//->Clone Tree
		
		
		//menu Permissions
		//->Build Group Menus
		
		
		
		

	}

	private function ResetArrays() {

		//Clear NameArray
		$MyRValue = "Nothing";

		while(!is_null($MyRValue)) {
			$MyRValue = array_pop($this->NameArray);
		}

		$MyRValue = "Nothing";

		while(!is_null($MyRValue)) {
			$MyRValue = array_pop($this->URLArray);
		}

		$MyRValue = "Nothing";
	
		while(!is_null($MyRValue)) {
			$MyRValue = array_pop($this->IsHeadArray);
		}
		
		$MyRValue = "Nothing";
	
		while(!is_null($MyRValue)) {
			$MyRValue = array_pop($this->ParentArray);
		}

		$MyRValue = array();

		while(!is_null($MyRValue)) {
			$MyRValue = array_pop($this->ChildrenArray);
			$MyInnerValue = "Nothing";
			while((!is_null($MyInnerValue))&&(!is_null($MyRValue))) {
				$MyInnerValue = array_pop($MyRValue);
			}
		}

		$this->NameArray = array();
		$this->URLArray = array();
		$this->ChildrenArray = array(array());
		$this->IsHeadArray = array();
		$this->ParentArray = array();

		$this->LoadMenus();

	}

	private function LoadMenus() {
		
		$query = "SELECT * FROM ODOTree";

		$result = $GLOBALS['globalref'][1]->Query($query);
	
		while($row = mysqli_fetch_assoc($result)) 
		{
			if(strlen($row["HeadTag"]) > 0) {
				$this->IsHeadArray[$row["HeadTag"]] = $row["PriKey"];
			}
			
			$this->NameArray[$row["PriKey"]] = $row["LinkName"];
			$this->URLArray[$row["PriKey"]] = $row["URL"];
			
			if($row["ParentID"] != 0) {
				$this->ChildrenArray[$row["ParentID"]][$row["ChildPos"]] = $row["PriKey"];
				$this->ParentArray[$row["PriKey"]] = $row["ParentID"];
			}
			
		}

		//sort all children arrays
		foreach($this->NameArray as $ID=>$Value) {
			if(isset($this->ChildrenArray[$ID])) {
				//we are sorting in reverse because we push and pop below.
				krsort($this->ChildrenArray[$ID], SORT_NUMERIC);
			}
		}
		
		
	}


	function FullMenu($HeadTag, $SelectedID = 0) {
		$RValue = "";
		//get position
		$Pos = 0;
		$tempArray = array();
		$LevelCount = 0;
		$LevelArray = array();

		if(isset($this->IsHeadArray[$HeadTag])) {
			$RValue = "<UL>";
			$Pos = $this->IsHeadArray[$HeadTag];
			array_push($tempArray, $Pos);
			$LevelArray[$LevelCount] = 0;

			while(count($tempArray) > 0) {
				$Pos = array_pop($tempArray);
				$RValue = $RValue . "\n<li>";
				if($Pos == $SelectedID) {
					if(isset($this->NameArray[$Pos])) {
						$RValue = $RValue . "<B>" . $this->NameArray[$Pos] . "</B>\n";
					} else {
						$RValue = $RValue . "<B>NONAME</B>\n";
					}

				} elseif(strlen($this->URLArray[$Pos]) > 0) {
					$RValue = $RValue . "<a href=\"javascript:SelectMenuItem(" . $Pos . ")\" id=\"" . $Pos . "\">" . $this->NameArray[$Pos] . "</a>\n";
				} elseif(strlen($this->NameArray[$Pos]) > 0) {
					$RValue = $RValue . "<a href=\"javascript:SelectMenuItem(" . $Pos . ")\" id=\"" . $Pos . "\" style=\"text-decoration : none\">" . $this->NameArray[$Pos] . "</a>\n";
				} else {
					$RValue = $RValue . "<a href=\"javascript:SelectMenuItem(" . $Pos . ")\" id=\"" . $Pos . "\" style=\"text-decoration : none\">" . $HeadTag . "</a>\n";
				}
			
				if(isset($this->ChildrenArray[$Pos])) {
					$RValue = $RValue . "\n<UL>";
					$LevelCount = $LevelCount + 1;
					foreach($this->ChildrenArray[$Pos] as $ID=>$Value){
						array_push($tempArray, $Value);
						if(isset($LevelArray[$LevelCount])) {
							$LevelArray[$LevelCount] = $LevelArray[$LevelCount] + 1;
						} else {
							$LevelArray[$LevelCount] = 1;
						}
					}

				} else {
					if($LevelArray[$LevelCount] > 1) {
						$RValue = $RValue . "\n</LI>";
						$LevelArray[$LevelCount] = $LevelArray[$LevelCount] - 1;
					} else {
						$LevelArray[$LevelCount] = $LevelArray[$LevelCount] - 1;
						$LevelCount = $LevelCount - 1;
						if($LevelCount >= 0) {
							$LevelArray[$LevelCount] = $LevelArray[$LevelCount] - 1;
							$RValue = $RValue . "\n</LI></UL></LI>";
							if($LevelArray[$LevelCount] == 0) {
								$RValue = $RValue . "\n</UL></LI>\n";
							}
						}
					}
				}

			}
			$RValue = $RValue . "\n</UL>";
			return $RValue;

		} else {
			$RValue = "No Head Found!";
			return $RValue;
		}
		
	}

	Private function MoveMenuItem($MoveItem, $NewParent) {
		//change parentID and ChildPosition
		//first find current child max
	
		
		$query = "SELECT MAX(ChildPos) FROM ODOTree WHERE ParentID=" . $NewParent;
			
		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!$result) {
			return false;
		}

		if(!($row = mysqli_fetch_row($result))) {
			return false;
		}

		$MyChildPos = $row[0] + 1;

		$query = "UPDATE ODOTree SET ChildPos=" . $MyChildPos . ", ParentID=" . $NewParent . " WHERE PriKey=" . $MoveItem;
		$result = $GLOBALS['globalref'][1]->Query($query);

		if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) {
			$this->ResetArrays();
			return true;
		} else {
			return false;
		}

		

	}

	//returns an array of all Primary keys of all Children under Parent
	function GetAllChildren($Parent) {
		$MyArray = array();
		$MyinnerArray = array();

		if(isset($this->ChildrenArray[$Parent])) {
			foreach($this->ChildrenArray[$Parent] as $ID=>$Value){
				array_push($MyArray, $Value);
				if(isset($this->ChildrenArray[$Value])) {
					$MyinnerArray = $this->GetAllChildren($Value);
					while(count($MyinnerArray) > 0) {
						array_push($MyArray, array_pop($MyinnerArray));
					}
				}
			}

		}

		return $MyArray;
	}

	function GetGroupsForObjects($Objects) {

		$MyArray = array();

		foreach($Objects as $ID=>$Value){
			
			$query = "SELECT DISTINCT ODOGroups.GID, ODOGroups.GroupName FROM Objects, ODOObjectACL, ODOGroups WHERE Objects.ObjID=" . $ID . " AND Objects.CodeSegID=ODOObjectACL.CodeSegID AND ODOObjectACL.GID=ODOGroups.GID";

			$result = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($result)) 
			{
				$MyArray[$row["GID"]] = $row["GroupName"];
			}
			
		}

		return $MyArray;

	}

	function GetGroupsInPage($PageID) {
		$MyArray = array();

		$query = "SELECT ODOGroups.GID, ODOGroups.GroupName FROM ODOGroups, ODOGACL WHERE ODOGACL.PageID=" . $PageID . " AND ODOGACL.GID=ODOGroups.GID";

		$result = $GLOBALS['globalref'][1]->Query($query);

		while($row = mysqli_fetch_assoc($result)) 
		{
			$MyArray[$row["GID"]] = $row["GroupName"];
		}

		return $MyArray;
	}

	function GetObjectsInPage($PageID) {
		
		$MyArray = array();

		$query = "SELECT ObjectNames.ObjID, ObjectNames.Name FROM ODOPageObject, ObjectNames WHERE ODOPageObject.PageID=" . $PageID. " AND ODOPageObject.ObjID=ObjectNames.ObjID";

		$result = $GLOBALS['globalref'][1]->Query($query);

		while($row = mysqli_fetch_assoc($result)) 
		{
			$MyArray[$row["ObjID"]] = $row["Name"];
		}

		return $MyArray;

	}

//end head

	function ListHeads() {

		if(strlen($this->SelectedHead) > 0 ) {
			$this->SelectedHead = "";
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "<BR><BR><H3>Select Menu Head to Edit</H3><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditTree\"><SELECT name=\"TreeHead\">";

		foreach( $this->IsHeadArray as $ID=>$Value) {
			$this->Content = $this->Content . "\n<option value=\"" . $ID . "\">" . $ID . "</option>";
		}
		
		$this->Content = $this->Content . "\n</select>\n<BR><BR><input type=\"submit\" name=\"Edit Tree\" value=\"Edit Tree\"><BR></form></center>";

	}

	function EditTree() {
		//3 types 1-Head 2-Link no URL 3-Link with URL
		//functions: Move under new location. New Under. Delete. 
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"])) && ($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"] != $this->SelectedHead)) {
			$this->SelectedHead = $_SESSION["ODOSessionO"]->EscapedVars["TreeHead"];
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(strlen($this->SelectedHead) < 1) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a tree to edit first!</H3></center>";
			return;
		}

		$this->Content = $this->Content . "<BR><BR><CENTER><H3><B>Select Menu Item to Edit/Move</B></H3><BR><BR>";

		//check if this is our first load
		

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"])) {
			
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["PreviousMenuItemSel"])) {
				if($this->MoveMenuItem($_SESSION["ODOSessionO"]->EscapedVars["PreviousMenuItemSel"], $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"])) {
					$this->Content = $this->Content . "<H3>Move completed</H3><BR><BR>";
				} else {
					$this->Content = $this->Content . "<H3>Move Failed!</H3><BR><BR>";
				}

				$TempRVal = $this->FullMenu($this->SelectedHead, (int)$_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"]);

				$this->Content = $this->Content . "<TABLE><TR><TD>" . $TempRVal . "</TD></TR></TABLE>";

				return;
			}

			$TempRVal = $this->FullMenu($this->SelectedHead, (int)$_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"]);

			$this->Content = $this->Content . "<TABLE><TR><TD>" . $TempRVal . "</TD></TR></TABLE>";


			//recall edit tree to select what menu item to move it under
			$this->Content = $this->Content . "<BR><BR><B>Please select a location to move the menu item under. All children of this menu item will be moved as well.</B><BR><BR><center><Table border=0><TR>";

			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"MoveMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditTree\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"PreviousMenuItemSel\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"0\">\n<TD><input type=\"submit\" name=\"Move Under New\" value=\"Move Under New\"></td></form></TR></TABLE></center>";

		} else { 
			$TempRVal = $this->FullMenu($this->SelectedHead);

			$this->Content = $this->Content . "<center><TABLE><TR><TD>" . $TempRVal . "</TD></TR></TABLE>";


			//recall edit tree to select what menu item to move it under
			$this->Content = $this->Content . "<BR><BR><table border=0><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"MoveMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditTree\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"0\">\n<TD><input type=\"submit\" name=\"Move Under New\" value=\"Move Under New\"></td></form>";

			
			//New item, on submit check value if selectedmenuitem is 0 then fail
			//if not 0 then create new item under selectedmenuitem calling new function
			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"NewMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"NewMenuItem\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"0\">\n<TD><input type=\"submit\" name=\"New Item\" value=\"New Item\">\n</TD></form>";

		
			//Edit Item
			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"EditMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditMenuItem\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"0\">\n<TD><input type=\"submit\" name=\"Edit Item\" value=\"Edit Item\"></TD></form>";

			//Delete Item 
			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"DeleteMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteMenuItem\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"0\">\n<TD><input type=\"submit\" name=\"Delete Item\" value=\"Delete Item\"></TD></form>";


			//on submit check value then call seperate moveup function
			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"UpMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"MoveUp\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"0\">\n<TD><input type=\"submit\" name=\"Move Up\" value=\"Move Up\">\n</TD></form>";

			//on submit check value then call seperate movedown function
			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"DownMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"MoveDown\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"0\">\n<TD><input type=\"submit\" name=\"Move Down\" value=\"Move Down\">\n</TD></form></TR></TABLE></CENTER>";


		}

		
	}


	function MoveUp() {
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"])) && ($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"] != $this->SelectedHead)) {
			$this->SelectedHead = $_SESSION["ODOSessionO"]->EscapedVars["TreeHead"];
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(strlen($this->SelectedHead) < 1) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a tree to edit first!</H3></center>";
			return;
		}

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"])) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a menu item to edit!</H3></center>";
			return;
		}

		//find current childpos and swap with whatever is above it. 
		//if childpos == 1 then exit
		
		$query = "SELECT ParentID, ChildPos FROM ODOTree WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];
		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!(mysqli_num_rows($result) > 0)) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We could not locate the menu item in the database!</H3></center>";
			return;
		}
		
		$row = mysqli_fetch_assoc($result);

		if($row["ChildPos"] <= 1) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We are already at the first position!</H3></center>";
			return;
		}

		$MyChildPos = $row["ChildPos"];
		$MyNewChildPos = $row["ChildPos"] - 1;
		
		$query = "UPDATE ODOTree SET ChildPos=" . $MyChildPos . " WHERE ParentID=" . $row["ParentID"] . " AND ChildPos=" . $MyNewChildPos;
		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!($GLOBALS['globalref'][1]->GetNumRowsAffected()>0)) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We seam to have a missing Child!!! AMBER ALERT! But we'll pretend it wasn't there anyway.</H3></CENTER>";
		}

		$query = "UPDATE ODOTree SET ChildPos=" . $MyNewChildPos . " WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];

		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!($GLOBALS['globalref'][1]->GetNumRowsAffected()>0)) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>For some reason we could not update the child position of the selected menu item?</H3></center>";
		} else {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We have updated the child position to " . $MyNewChildPos . ".</H3><BR><a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";
		
			$this->ResetArrays();

		}
		

	}


	function MoveDown() {

		if((isset($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"])) && ($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"] != $this->SelectedHead)) {
			$this->SelectedHead = $_SESSION["ODOSessionO"]->EscapedVars["TreeHead"];
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(strlen($this->SelectedHead) < 1) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a tree to edit first!</H3></center>";
			return;
		}

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"])) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a menu item to edit!</H3></center>";
			return;
		}

		//find current childpos and swap with whatever is above it. 
		//if childpos == 1 then exit
		
		$query = "SELECT ParentID, ChildPos FROM ODOTree WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];
		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!(mysqli_num_rows($result) > 0)) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We could not locate the menu item in the database!</H3></center>";
			return;
		}
		
		$row = mysqli_fetch_assoc($result);
		$MyChildPos = $row["ChildPos"];
		$MyParentID = $row["ParentID"];

		$query = "SELECT MAX(ChildPos) as MaxChild FROM ODOTree WHERE ParentID=" . $row["ParentID"];
		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!$result) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We could not get the last child of this menu item's parent!</H3></center>";
			return;
		}

		if(!($row = mysqli_fetch_row($result))) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We could not get the last child of this menu item's parent!</H3></center>";
			return;
		}

		//check max with current
		$ParentMax = $row[0];
		
		if($ParentMax <= $MyChildPos) {
			//do nothing since we are already at the bottom
			$this->Content = $this->Content . "\n<CENTER><BR><H3>The menu item is already at the bottom of the list.</H3></CENTER>";
			return;
		}

		$MyNewChildPos = $MyChildPos + 1;
		
		
		$query = "UPDATE ODOTree SET ChildPos=" . $MyChildPos . " WHERE ParentID=" . $MyParentID . " AND ChildPos=" . $MyNewChildPos;
		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!($GLOBALS['globalref'][1]->GetNumRowsAffected()>0)) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We seam to have a missing Child!!! AMBER ALERT! But we'll pretend it wasn't there anyway.</H3></CENTER>";
		}

		$query = "UPDATE ODOTree SET ChildPos=" . $MyNewChildPos . " WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];

		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!($GLOBALS['globalref'][1]->GetNumRowsAffected()>0)) {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>For some reason we could not update the child position of the selected menu item?</H3></center>";
		} else {
			$this->Content = $this->Content . "\n<CENTER><BR><H3>We have updated the child position to " . $MyNewChildPos . ".</H3><BR><a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";
		
			$this->ResetArrays();

		}

	}


	function DeleteMenuItem() {

		if((isset($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"])) && ($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"] != $this->SelectedHead)) {
			$this->SelectedHead = $_SESSION["ODOSessionO"]->EscapedVars["TreeHead"];
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(strlen($this->SelectedHead) < 1) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a tree to edit first!</H3></center>";
			return;
		}
	
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"])) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a menu item to edit!</H3></center>";
			return;
		}

		$SelectedItem = $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["DeleteMe"])) {
			
			$MyTempArray = array();
			$MyChild = "";
			$DelPermLink = 0;
			$DelMenuItems = 0;
			$CDelPermLink = 0;
			$CDelMenuItems = 0;
			$MyTempArray = $this->GetAllChildren($SelectedItem);

			while(count($MyTempArray)>0) {
				$MyChild = array_pop($MyTempArray);
				$query = "DELETE FROM ODOMenus WHERE StaticLinkID=" . $MyChild;
				$result = $GLOBALS['globalref'][1]->Query($query);
				$CDelPermLink = $CDelPermLink + $GLOBALS['globalref'][1]->GetNumRowsAffected();
				
				$query = "DELETE FROM ODOTree WHERE PriKey=" . $MyChild;
				$result = $GLOBALS['globalref'][1]->Query($query);
				$CDelMenuItems = $CDelMenuItems + $GLOBALS['globalref'][1]->GetNumRowsAffected();
				
			}

			//delete item
			$query = "DELETE FROM ODOMenus WHERE StaticLinkID=" . $SelectedItem;
			$result = $GLOBALS['globalref'][1]->Query($query);
			$DelPermLink = $GLOBALS['globalref'][1]->GetNumRowsAffected();
				
			$query = "DELETE FROM ODOTree WHERE PriKey=" . $SelectedItem;
			$result = $GLOBALS['globalref'][1]->Query($query);
			$DelMenuItems = $GLOBALS['globalref'][1]->GetNumRowsAffected();
				
			$this->ResetArrays();
			$this->Content = $this->Content . "<BR><BR><center><H3>We have deleted " . $DelPermLink . " Permission links and " . $DelMenuItems . " Menu Item. From the children of this menu item we have deleted " . $CDelPermLink . " permission links and " . $CDelMenuItems . " menu items.</H3><BR><a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";

		} else {

			$this->Content = $this->Content . "<BR><center><H3>Are you sure you want to delete this menu item?</H3><BR>";

			//find out if the menu item has children.
			if(isset($this->ChildrenArray[$SelectedItem])) {
				$this->Content = $this->Content . "<BR><H4>This menu item has children. All children will be deleted as well!</H4><BR>";
			}

			$this->Content = $this->Content . "<table border=0><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"DeleteMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"DeleteMe\" value=\"DeleteMe\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteMenuItem\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . "\">\n<TD><input type=\"submit\" name=\"Delete Item\" value=\"Delete Item\"></TD></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditTree\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\">\n<TD><input type=\"submit\" name=\"Cancel\" value=\"Cancel\"></TD></form></TR></table></center>";

		}

	}



	function EditMenuItem() {

		if((isset($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"])) && ($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"] != $this->SelectedHead)) {
			$this->SelectedHead = $_SESSION["ODOSessionO"]->EscapedVars["TreeHead"];
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(strlen($this->SelectedHead) < 1) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a tree to edit first!</H3></center>";
			return;
		}

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"])) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a menu item to edit!</H3></center>";
			return;
		}



		if(isset($_SESSION["ODOSessionO"]->EscapedVars["UpdateMe"])) {

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["HeadTag"])) {
	
				if(strlen($_SESSION["ODOSessionO"]->EscapedVars["HeadTag"]) < 1) {
					$this->Content = $this->Content . "<BR><Center><B>You must enter a valid Tag name!</B><BR><BR>\n\n<a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";
					return;
				}
			
				$query = "SELECT * FROM ODOTree WHERE HeadTag='" . $_SESSION["ODOSessionO"]->EscapedVars["HeadTag"] . "'";
				$result = $GLOBALS['globalref'][1]->Query($query);

				if(mysqli_num_rows($result) > 0) {
					$this->Content = $this->Content . "<BR><Center><B>This Head Tag Name already exists in the system! We can not change the name to this.</B><BR><BR>\n\n<a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";
					return;
				}

				$query = "UPDATE ODOTree SET HeadTag='" . $_SESSION["ODOSessionO"]->EscapedVars["HeadTag"] . "' WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];

				$result = $GLOBALS['globalref'][1]->Query($query);

				if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) {
					$this->ResetArrays();
				
					$this->Content = $this->Content . "<BR><Center><B>Update Completed</B><BR><BR>\n\n<a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";

					return;

				} else {
				
					$this->Content = $this->Content . "<BR><Center><B>Update FAILED!</B><BR><BR>\n\n<a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";
		
					return;
				}

			} else {
				if((!(isset($_SESSION["ODOSessionO"]->EscapedVars["LName"])))||(!(isset($_SESSION["ODOSessionO"]->EscapedVars["IsObject"])))||(!(isset($_SESSION["ODOSessionO"]->EscapedVars["pgid"])))||(!(isset($_SESSION["ODOSessionO"]->EscapedVars["MyURL"])))) {
					$this->Content = $this->Content . "<BR><Center><B>One or more varialbes are missing!</B></center>";
					return;
				}

				$query = "UPDATE ODOTree SET PageID=";
				
				if((isset($_SESSION["ODOSessionO"]->EscapedVars["pgid"]))&&($_SESSION["ODOSessionO"]->EscapedVars["pgid"] != 0)) {
				
					$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["pgid"];
					
				} else {
					
					$query = $query . "NULL";
					
				}
				
				$query = $query . ", IsObject=" . $_SESSION["ODOSessionO"]->EscapedVars["IsObject"] . ", URL='" . $_SESSION["ODOSessionO"]->EscapedVars["MyURL"] . "', LinkName='" . $_SESSION["ODOSessionO"]->EscapedVars["LName"] . "', DependVar=";
				
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["ConVar"])&&strlen($_SESSION["ODOSessionO"]->EscapedVars["ConVar"])) {
					$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["ConVar"] . "'";
				} else {
					$query = $query . "NULL";
				}
				
				$query = $query . " WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];

				$result = $GLOBALS['globalref'][1]->Query($query);

				if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) {
					$this->ResetArrays();
				
					$this->Content = $this->Content . "<BR><Center><B>Update Completed</B><BR><BR>\n\n<a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";

					return;

				} else {
				
					$this->Content = $this->Content . "<BR><Center><B>Update FAILED!</B><BR><BR>\n\n<a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to Tree</a></center>";
		
					return;
				}
			}


		} else {


			$query = "SELECT * FROM ODOTree WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];

			$result = $GLOBALS['globalref'][1]->Query($query);

			if(!($row = mysqli_fetch_assoc($result))) {
				$this->Content = "\n<center><BR><H3>No menu item found!</H3><BR><BR>";
				return;
			}

			$this->Content = $this->Content . "\n<center><BR><H3>Please update the menu item info...</H3><BR><BR><table border=1><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditMenuItem\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . "\">\n<input type=\"hidden\" name=\"UpdateMe\" value=\"true\">";

			if(isset($row["HeadTag"]) && (strlen($row["HeadTag"]) > 0)) {
				$this->Content = $this->Content . "\n<TR><sub>Please note: Changing tag name may break menus that depend on it. Please update your pages before making this change.</sub></TR><TR><TD>Menu Item ID:</TD><TD>" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . "</TD></TR><TR><TD>Head Tag Name:</TD><TD><input type=\"text\" name=\"HeadTag\" vaule=\"" . $row["HeadTag"] . "\"></TD></TR></Table>";

			} else {
			
				

				$this->Content = $this->Content . "<TR><TD>Menu Item ID: </TD><TD>" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . "</TD></TR><TR><TD>Link Name:</TD><TD><input type=\"text\" name=\"LName\" value=\"" . $row["LinkName"] . "\" maxlength=\"254\"></TD></TR><TR><TD>Is Object:</TD><TD><select name=\"IsObject\"><option value=\"1\"";


				if($row["IsObject"] == 1) {
				
					$this->Content = $this->Content . "selected>Yes</option><option value=\"0\" >No</option></select>";

				} else {
				
					$this->Content = $this->Content . ">Yes</option><option value=\"0\" selected>No</option></select>";

				}

				$this->Content = $this->Content . "</TD></TR><TR><TD>PageID:</TD><TD><select name=\"pgid\"><option value=\"0\"";

				if(is_null($row["PageID"])) {
					$this->Content = $this->Content . " selected>NOPAGEID</OPTION>";
				} else {
					$this->Content = $this->Content . ">NOPAGEID</OPTION>";
				}

				//load up page names
				$query = "SELECT PageID, PageName FROM ODOPages";
				$result = $GLOBALS['globalref'][1]->Query($query);
			
				while($row2 = mysqli_fetch_assoc($result)) 
				{
					$this->Content = $this->Content . "<option value=\"" . $row2["PageID"] . "\" ";

					if($row2["PageID"] == $row["PageID"]) {
						$this->Content = $this->Content . "selected";
					}

					$this->Content = $this->Content . ">" . $row2["PageName"] . "</option>";

				}
		

				$this->Content = $this->Content . "</select></TR><TR><TD>URL: </TD><TD><input type=\"text\" name=\"MyURL\" value=\"";

				if((isset($row["URL"]))&&(strlen($row["URL"])>0)) {

					$this->Content = $this->Content . $row["URL"];
				}

				$this->Content = $this->Content . "\" maxlength=\"254\"></TD></TR><TR><TD>Conditional Var:</TD><TD><input type=\"text\" name=\"ConVar\" value=\"";
				
				if((isset($row["DependVar"]))&&(strlen($row["DependVar"])>0)) {
					$this->Content = $this->Content . $row["DependVar"];
					}
				
				$this->Content = $this->Content . "\" maxlength=\"20\"></TD></TR></table>";
			

			}

			$this->Content = $this->Content . "<BR><BR>\n\n<input type=\"submit\" name=\"Update Item\" value=\"Update Item\">\n</form>";

		}

	}

	
	function NewMenuItem() {
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"])) && ($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"] != $this->SelectedHead)) {
			$this->SelectedHead = $_SESSION["ODOSessionO"]->EscapedVars["TreeHead"];
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(strlen($this->SelectedHead) < 1) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a tree to edit first!</H3></center>";
			return;
		}

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"])) {
			$this->Content = $this->Content . "\n<center><BR><H3>You must select a location in the tree to place the new item!</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["CreateNew"])) {

			//find childid number
			$query = "SELECT MAX(ChildPos) FROM ODOTree WHERE ParentID=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];
			
			$result = $GLOBALS['globalref'][1]->Query($query);

			$MyChildPos = 1;

			if(($row = mysqli_fetch_row($result))) {
				$MyChildPos = $row[0] + 1;
			}

			$query = "INSERT INTO ODOTree(PageID, ParentID, IsObject, URL, LinkName, ChildPos, DependVar) values(";

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["pgid"]))&&($_SESSION["ODOSessionO"]->EscapedVars["pgid"] != 0)) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["pgid"] . ", ";
			} else {
				$query = $query . "NULL, ";
			}


			$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . ", ";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsObject"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["IsObject"] . ", ";
			} else {
				$query = $query . "0, ";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["MyURL"])) {
				$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["MyURL"] . "', ";
			} else {
				$query = $query . "NULL, ";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["LName"])) {
				$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["LName"] . "', ";
			} else {
				$query = $query . "NULL, ";
			}	
			
			$query = $query . $MyChildPos . ",";

				
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["ConVar"])&&strlen($_SESSION["ODOSessionO"]->EscapedVars["ConVar"])) {
				$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["ConVar"] . "') ";
		
			} else {
				$query = $query . "NULL)";
			}
		
			$result = $GLOBALS['globalref'][1]->Query($query);

			if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) {
				$this->ResetArrays();

				$this->Content = $this->Content . "\n<center><BR><H3>New Menu Item added...</H3><BR><BR><a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to view tree?</a><BR>";

			} else {
				$this->Content = $this->Content . "\n<center><BR><H3>We could not add new menu item!!!</H3><BR><BR><a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditTree\"><-Go back to view tree?</a><BR>";
			}
			
		} else {
			
			$this->Content = $this->Content . "\n<center><BR><H3>Please enter the new menu item info...</H3><BR><BR><table border=1><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"NewMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"NewMenuItem\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . "\">\n<input type=\"hidden\" name=\"CreateNew\" value=\"true\">";

			$this->Content = $this->Content . "<table border=1><TR><TD>Parent ID: </TD><TD>" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . "</TD></TR><TR><TD>Link Name:</TD><TD><input type=\"text\" name=\"LName\" value=\"Name shown on pages...\" maxlength=\"254\"></TD></TR><TR><TD>Is Object:</TD><TD><select name=\"IsObject\"><option value=\"1\">Yes</option><option value=\"0\" selected>No</option></select></TD></TR><TR><TD>PageID:</TD><TD><select name=\"pgid\"><option value=\"0\">NOPAGEID</OPTION>";

			//load up page names
			$query = "SELECT PageID, PageName FROM ODOPages";
			$result = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($result)) 
			{
				$this->Content = $this->Content . "<option value=\"" . $row["PageID"] . "\">" . $row["PageName"] . "</option>";

			}
		

			$this->Content = $this->Content . "</select></TR><TR><TD>URL: </TD><TD><input type=\"text\" name=\"MyURL\" value=\"index.php?pg=\" maxlength=\"254\"></TD></TR><TR><TD>Conditional Var:</TD><TD><input type=\"text\" name=\"ConVar\" maxlength=\"20\"></TD></TR></table><BR><BR><input type=\"submit\" name=\"Create Item\" value=\"Create Item\">\n</form>";
			
		}


	}

	
	function NewHead() {

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["NewFlag"])) {
		
			if((!(isset($_SESSION["ODOSessionO"]->EscapedVars["HeadName"]))) || (strlen($_SESSION["ODOSessionO"]->EscapedVars["HeadName"]) < 1)) {
				$this->Content = $this->Content . "<H3>You need to enter a longer name!</H3></center>";
				return;
			}

			$query = "SELECT * FROM ODOTree WHERE HeadTag='" . $_SESSION["ODOSessionO"]->EscapedVars["HeadName"] . "'";
			$result = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($result) > 0) {
				$this->Content = $this->Content . "<H3>This head name already exists. We need unique names.</H3></center>";
				return;
			}

			$query = "INSERT INTO ODOTree(PageID, ParentID, IsObject, HeadTag) values(null, null, 0, '" . $_SESSION["ODOSessionO"]->EscapedVars["HeadName"] . "')";
			$result = $GLOBALS['globalref'][1]->Query($query);

			if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) {

				$this->ResetArrays();
				$this->Content = $this->Content . "<H3>Added Head Name!</H3></center>";

			} else {

				$this->Content = $this->Content . "<H3>We had an error adding the head!</H3></center>";

			}

		} else {
			
			$this->Content = $this->Content . "<H3>Enter new head info...</H3><BR><form action=\"index.php\" enctype=\"multipart/form-data\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"NewHead\"><input type=\"hidden\" name=\"NewFlag\" value=\"1\"><input type=\"text\" name=\"HeadName\" maxlength=\"254\"><br><input type=\"submit\" name=\"submit\" value=\"submit\"></form></center>";

		}

	}


	function CloneTree() {

		if((isset($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"])) && ($_SESSION["ODOSessionO"]->EscapedVars["TreeHead"] != $this->SelectedHead)) {
			$this->SelectedHead = $_SESSION["ODOSessionO"]->EscapedVars["TreeHead"];
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();

		if(strlen($this->SelectedHead) < 1) {

			
			$this->Content = $this->Content . "<BR><BR><H3>Select Menu Head to Clone</H3><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"CloneTree\"><SELECT name=\"TreeHead\">";

			foreach( $this->IsHeadArray as $ID=>$Value) {
				$this->Content = $this->Content . "\n<option value=\"" . $ID . "\">" . $ID . "</option>";
			}
		
			$this->Content = $this->Content . "\n</select>\n<BR><BR><input type=\"submit\" name=\"Clone Tree\" value=\"Clone Tree\"><BR></form></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["HeadName"])) {
			
			$query = "SELECT * FROM ODOTree WHERE HeadTag='" . $_SESSION["ODOSessionO"]->EscapedVars["HeadName"] . "'";
			$result = $GLOBALS['globalref'][1]->Query($query);
			if(mysqli_num_rows($result)>0) {
				$this->Content = $this->Content . "<H3>This name already exists! Please select a new name!</H3></center>";
				return;
			}

			$query = "INSERT INTO ODOTree(PageID, ParentID, IsObject, HeadTag) values(NULL, NULL, 0, '" . $_SESSION["ODOSessionO"]->EscapedVars["HeadName"] . "')";
			$result = $GLOBALS['globalref'][1]->Query($query);

			if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) {
				$this->Content . "<H3>We had an error trying to create the new head name!</H3></center>";
				return;
			}
			
			$MyHeadID = 0;
			$MyHeadID = $GLOBALS['globalref'][1]->LastInsertID();

			$ChildList = array();

			$ChildList = $this->GetAllChildren($this->IsHeadArray[$this->SelectedHead]);

			
			
		} else {

			$this->Content = $this->Content . "<H3>Enter new head info for cloned tree...</H3><BR><form action=\"index.php\" enctype=\"multipart/form-data\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"CloneTree\"><input type=\"text\" name=\"HeadName\" maxlength=\"254\"><br><input type=\"submit\" name=\"Clone\" value=\"Clone\"></form></center>";

		}

	}


	function EditPermissions() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();

		if(strlen($this->SelectedHead) < 1) {
			$this->Content = $this->Content . "\n<BR><H3>You must select a tree to edit first!</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"])) {
			

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["UpdateGroups"])) {

				$query = "DELETE FROM ODOMenus WHERE StaticLinkID=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];
				$result = $GLOBALS['globalref'][1]->Query($query);

				$NumDynDel=0;

				$NumDynDel = $GLOBALS['globalref'][1]->GetNumRowsAffected();

				$MyChildren = array();

				if(isset($_SESSION["ODOSessionO"]->EscapedVars["AllChildren"])) {
					$MyChildren = $this->GetAllChildren($_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"]);

					foreach($MyChildren as $Value) {
						
						$query = "DELETE FROM ODOMenus WHERE StaticLinkID=" . $Value;
						$result = $GLOBALS['globalref'][1]->Query($query);

						$NumDynDel = $NumDynDel + $GLOBALS['globalref'][1]->GetNumRowsAffected();
					}
				}

				//now add
				$AddedItems = 0;

				$glist = $_POST['Groups'];

				if ($glist){
	 				foreach ( $glist as $newgroup)
					{
						$query = "INSERT INTO ODOMenus(StaticLinkID, GID) values(" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . ", " . $newgroup . ")";
					
						$result = $GLOBALS['globalref'][1]->Query($query);
					
						$AddedItems = $AddedItems + 1;
					}

					foreach($MyChildren as $Value) {

						foreach($glist as $newgroup) {
							$query = "INSERT INTO ODOMenus(StaticLinkID, GID) values(" . $Value . ", " . $newgroup . ")";
							$result = $GLOBALS['globalref'][1]->Query($query);
					
							$AddedItems = $AddedItems + 1;
						}
					}
				}

				$this->Content = $this->Content . "<BR><H3>We have removed " . $NumDynDel . " dynamic menu links and added " . $AddedItems . " dynamic menu items.</H3><BR><BR><a href=\"index.php?pg=ODOCMS&ob=ODOCMSMenusO&fn=EditPermissions\"><-Go back to Tree</a></center>";

			} else {
		
				$query = "SELECT * FROM ODOTree WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"];

				$result = $GLOBALS['globalref'][1]->Query($query);

				if(!(mysqli_num_rows($result)>0)) {
					$this->Content = $this->Content . "\n<BR><H3>You have somehow selected an invalid menu item. We have reloaded the menu lists to try and avoid this error in the future. Please try and select another menu item.</H3></center>";
					$this->ResetArrays();
					return;
				}

				$row = mysqli_fetch_assoc($result);
				//get objects assigned to page
				$MyObjects = array();

				//get groups assigned to page
				$MyGroups = array();

				if($row["PageID"] != 0) {
					$MyObjects = $this->GetObjectsInPage($row["PageID"]);
					$MyGroups = $this->GetGroupsInPage($row["PageID"]);
				}

				//show groups granted on each object in page
				$MyObjectGroups = array();
				$MyObjectGroups = $this->GetGroupsForObjects($MyObjects);

				$this->Content = $this->Content . "<BR><H3>Select the groups for this menu item...</H3><BR><BR><H4>Groups needed for page to work...</H4><BR><Table border=1><TR>";

				$MyCounter = 0;
				foreach($MyGroups as $ID=>$Value) {
			
					if($MyCounter > 3) {
						$this->Content = $this->Content . "</TR><TR>";
						$MyCounter = 0;
					}

					$this->Content = $this->Content . "<TD>" . $Value . "</TD>";
					$MyCounter = $MyCounter + 1;

				}

				while($MyCounter < 4) {
					$this->Content = $this->Content . "<TD></TD>";
					$MyCounter = $MyCounter + 1;
				}

				$this->Content = $this->Content . "</TR></Table><BR><BR>";


				//show all groups needed to be allowed for menu item
				$this->Content = $this->Content . "<H4>Groups needed for objects on page to work...</H4><BR><Table border=1><TR>";

				$MyCounter = 0;
				foreach($MyObjectGroups as $ID=>$Value) {
			
					if($MyCounter > 3) {
						$this->Content = $this->Content . "</TR><TR>";
						$MyCounter = 0;
					}

					$this->Content = $this->Content . "<TD>" . $Value . "</TD>";
					$MyCounter = $MyCounter + 1;

				}

				while($MyCounter < 4) {
					$this->Content = $this->Content . "<TD></TD>";
					$MyCounter = $MyCounter + 1;
				}
			
				$this->Content = $this->Content . "</TR></Table><BR><BR>";


				//show all groups needed to be allowed for menu item
				$this->Content = $this->Content . "<H4>Select Groups to add.</H4><BR><form action=\"index.php\" enctype=\"multipart/form-data\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditPermissions\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . "\">\n<input type=\"hidden\" name=\"UpdateGroups\" value=\"true\"><SELECT name=\"Groups[]\" multiple>";

				$query = "SELECT ODOGroups.GroupName as GroupName, ODOGroups.GID as GID, t1.StaticLinkID as LinkID FROM ODOGroups LEFT JOIN (SELECT * FROM ODOMenus WHERE StaticLinkID=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedMenuItem"] . ") as t1 on ODOGroups.GID=t1.GID";
				$result = $GLOBALS['globalref'][1]->Query($query);

				while($row = mysqli_fetch_assoc($result)) 
				{
					$this->Content = $this->Content . "<Option value=" . $row["GID"];

					if((isset($row["LinkID"]))&&($row["LinkID"] != null)) {
	
						$this->Content = $this->Content . " selected";

					} 

					$this->Content = $this->Content . " >" . $row["GroupName"] . "</option>\n";
				}

				$this->Content = $this->Content . "</SELECT>\n<BR>Make changes to all children too?:&nbsp;<input type=\"checkbox\" name=\"AllChildren\"><BR><input type=\"submit\" name=\"Update Groups\" value=\"Update Groups\"></form></CENTER>";
				
		
			}
			

		} else {
			
			$TempRVal = $this->FullMenu($this->SelectedHead);

			$this->Content = $this->Content . "<BR><H3>Edit Permissions</H3><BR><BR><TABLE><TR><TD>" . $TempRVal . "</TD></TR></TABLE>";


			//recall edit tree to select what menu item to move it under
			$this->Content = $this->Content . "<BR><BR><table border=0><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" name=\"MoveMenu\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditPermissions\"><input type=\"hidden\" name=\"TreeHead\" value=\"" . $this->SelectedHead . "\"><input type=\"hidden\" name=\"SelectedMenuItem\" value=\"0\">\n<TD><input type=\"submit\" name=\"Edit Permissions\" value=\"Edit Permissions\"></td></form></center>";


		}


	}

	function GeneratePermissions() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();

		

	}

	function DeleteTree() {
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSMenus");

		$this->LoadODOMenus();

		if(strlen($this->SelectedHead) < 1) {
			$this->Content = $this->Content . "\n<BR><H3>You must select a tree to delete first!</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["DeleteMe"])) {

			$MyTempArray = array();
			$MyChild = "";
			$DelPermLink = 0;
			$DelMenuItems = 0;
			$MyTempArray = $this->GetAllChildren($this->IsHeadArray[$this->SelectedHead]);
			array_push($MyTempArray, $this->IsHeadArray[$this->SelectedHead]);

			while(count($MyTempArray)>0) {
				$MyChild = array_pop($MyTempArray);
				$query = "DELETE FROM ODOMenus WHERE StaticLinkID=" . $MyChild;
				$result = $GLOBALS['globalref'][1]->Query($query);
				$DelPermLink = $DelPermLink + $GLOBALS['globalref'][1]->GetNumRowsAffected();
				
				$query = "DELETE FROM ODOTree WHERE PriKey=" . $MyChild;
				$result = $GLOBALS['globalref'][1]->Query($query);
				$DelMenuItems = $DelMenuItems + $GLOBALS['globalref'][1]->GetNumRowsAffected();
				
			}

			$this->Content = $this->Content . "<H3>We have deleted " . $DelPermLink . " Permission links and " . $DelMenuItems . " Menu Items</H3></center>";

		} else {
			
			$this->Content = $this->Content . "<H3>Are you sure you want to delete the Tree: " . $this->SelectedHead . "?</H3><BR><B>All children of this Tree will be deleted and all permission links to this Tree will be removed from the database.</B><BR><BR>";
			
			$this->Content = $this->Content . "<table border=0><TR><form action=\"index.php\" enctype=\"multipart/form-data\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteTree\"><input type=\"hidden\" name=\"DeleteMe\" value=\"1\"><TD><input type=\"submit\" name=\"Delete\" value=\"Delete\"></TD></form><form action=\"index.php\" enctype=\"multipart/form-data\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSMenusO\"><input type=\"hidden\" name=\"fn\" value=\"EditTree\"><TD><input type=\"submit\" name=\"Cancel\" value=\"Cancel\"></TD></form></TR></Table></center>";

		}
	
	}

//begin footer
	function __wakeup() {
		$this->Content = "";
	}


}




?>