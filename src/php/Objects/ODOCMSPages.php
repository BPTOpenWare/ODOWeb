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

//******************************************************//
//Class: ODOCMSPages					//
//******************************************************//

class ODOCMSPages {
	var $Content;
	var $SelectedPage;
	var $PageName;
	var $Author;
	var $CreateDate;
	var $LastMod;
	var $LastModDate;
	var $IsDynamic;
	var $IsAdmin;
	var $ModID;
	
	
	function __construct() {
		$this->Content = "";
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedPage"])) {
			$this->SelectedPage = $_SESSION["ODOSessionO"]->EscapedVars["SelectedPage"];
			$this->SelectPage($this->SelectedPage); 
			
		} else {
			$this->ClearPage();
		}
	
	}

	private function ClearPage() {
		$this->SelectedPage = 0;
		$this->PageName = "";
		$this->Author = "";
		$this->CreateDate = "";
		$this->LastMod = "";
		$this->LastModDate = "";
		$this->IsDynamic = 0;
		$this->IsAdmin = 0;
		$this->ModID = 0;
		
	}

	private function SelectPage($PageID) {
		//sets info for this object
		$query = "select * from ODOPages where PageID=" . $GLOBALS['globalref'][1]->EscapeMe($PageID);
		$result = $GLOBALS['globalref'][1]->Query($query);

		if(!mysqli_num_rows($result)) {
			ClearPage();
			return false;
		} else {

			$row = mysqli_fetch_assoc($result);
			
			$this->SelectedPage = $PageID;
			$this->PageName = $row["PageName"];
			$this->Author = $row["Author"];
			$this->CreateDate = $row["CreatedDate"];
			$this->LastMod = $row["LastModBy"];
			$this->LastModDate = $row["LastModDate"];
			$this->IsDynamic = $row["IsDynamic"];
			$this->IsAdmin = $row["IsAdmin"];
			$this->ModID = $row["ModID"];
			return true;
		}

	}

	//******************************************************************************//
	//Function: ODOCMSPages::LoadODOMenus($MenuType)				//
	//Parameters: MenuType-integer: simple select statement choses the menu 	//
	//type to output								//
	//Description: Used by every ODO Object to create content to be displayed	//
	//for menus only								//
	//******************************************************************************//
	function LoadODOMenus() {
		
		//menu Page Selection
		//New Page
		//->Edit Page
		//->List Pages
		//->Pages By Category
		//->Search
		
		//menu Categories
		//Categories
		//->New Category
		//->Delete a Category
		//->List All Categories
		
		//Permissions
		//Menu Placement
		if($this->SelectedPage != 0)
		{
			
			$this->Content = $this->Content . "<br><table border=1><TR><TH><B>Selected Page</B></TR></TH><TR><TD>Page Name: " . $this->PageName . "</TD><TD>PageID: " . $this->SelectedPage . "</td></tr>\n";
			$this->Content = $this->Content . "<TR><TD>Author: " . $this->Author . "</td><td>Created Date: " . $this->CreateDate . "</td></tr>\n";
			$this->Content = $this->Content . "<TR><TD>Last Modified By (UID): " . $this->LastMod . "</td><td>Last Mod Date: " . $this->LastModDate . "</td></tr>\n</table>\n";

		}

		

	}

//end header

	function AddRemoveObjects() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(!$this->IsDynamic) {
			$this->Content = $this->Content . "<BR><BR><center><H3>This page is not dynamic. Please Choose a dynamic page to add objects to.</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {

			//first delete
			$query = "DELETE FROM ODOPageObject WHERE PageID=" . $this->SelectedPage;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			$query="SELECT * FROM ObjectNames";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			while($row = mysqli_fetch_assoc($TempRecords)) {
				
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["Object" . $row["ObjID"]])) {
					$query="INSERT INTO ODOPageObject(PageID, ObjID) values(" . $this->SelectedPage . "," . $row["ObjID"] . ")";
					$TempRecords2 = $GLOBALS['globalref'][1]->Query($query);
			
				}
			}
				
			$this->Content = $this->Content . "<CENTER><H3>All objects updated.</H3></center>";

		} else {
			$ModArray = array();
			$query = "select * from Modules";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			while($row = mysqli_fetch_assoc($TempRecords)) {
				$ModArray[$row["ModID"]] = $row["ModName"];
			}

			$this->Content = $this->Content . "<center><BR><H2>Object Access Control for Page</H2><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Update\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"AddRemoveObjects\"><input type=\"hidden\" name=\"SelectedPage\" value=\"" . $this->SelectedPage . "\"><table border=1><TR><TH>Object ID</TH><TH>Object Name</TH><TH>Object Description</TH><TH>Is Administrative Object</TH><TH>Part of Module</TH><TH>Loaded For Page</TH></TR>";

			$query = "SELECT ObjectNames.ObjID, ObjectNames.Name, ObjectNames.Description, ObjectNames.IsAdmin, ObjectNames.ModID, ODOPageObject.PageID FROM ObjectNames LEFT JOIN ODOPageObject on ObjectNames.ObjID = ODOPageObject.ObjID AND ODOPageObject.PageID=" . $this->SelectedPage . " group by ObjID";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<TR><TD>" . $row["ObjID"] . "</TD><TD>" . $row["Name"] . "</TD><TD>" . $row["Description"] . "</TD><TD>" . $row["IsAdmin"] . "</TD><TD>" . $ModArray[$row["ModID"]] . "</TD><TD><input type=\"checkbox\" name=\"Object" . $row["ObjID"] . "\" value=\"" . $row["ObjID"] . "\" ";
				if($row["PageID"] == $this->SelectedPage) {
					$this->Content = $this->Content . "checked></TD></TR>";
				} else {
					$this->Content = $this->Content . "></TD></TR>";
				}
			}

			$this->Content = $this->Content . "</TABLE><BR><BR><input type=\"submit\" name=\"Update Objects\" value=\"Update Objects\"></FORM></CENTER>";
		}

	}

	function AddRemoveGroups() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			$query = "DELETE FROM ODOGACL WHERE PageID=" . $this->SelectedPage;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT * FROM ODOGroups";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["read" . $row["GID"]])) {
					$query = "insert into ODOGACL(GID,PageID,WriteOK) values(" . $row["GID"] . "," . $this->SelectedPage . ",";
					if(isset($_SESSION["ODOSessionO"]->EscapedVars["write" . $row["GID"]])) {
						$query = $query . "1)";
					} else {
						$query = $query . "0)";
					}
					$TempRecords2 = $GLOBALS['globalref'][1]->Query($query);
				}
			
			}

			$this->Content = $this->Content . "<CENTER><H3>All groups updated.</H3></center>";

		} else {
			$this->Content = $this->Content . "<center><BR><H2>Groups Access Control for Page</H2><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Update\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"AddRemoveGroups\"><input type=\"hidden\" name=\"SelectedPage\" value=\"" . $this->SelectedPage . "\"><table border=1><TR><TH>Group Name</TH><TH>Read</TH><TH>Write</TH></TR>";

			$query = "SELECT ODOGroups.GID, ODOGroups.GroupName, ODOGACL.PageID, ODOGACL.WriteOK FROM ODOGroups LEFT JOIN ODOGACL on ODOGroups.GID = ODOGACL.GID AND ODOGACL.PageID=" . $this->SelectedPage;

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			$i = 0;
			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<TR id=\"rownum" . $i . "\"><TD>" . $row["GroupName"] . "</TD><TD><input id=\"read" . $row["GID"] . "\" type=\"checkbox\" name=\"read" . $row["GID"] . "\" value=\"true\" onclick=\"GroupReadClick(" . $row["GID"] . ")\" ";
				if($row["PageID"] == $this->SelectedPage) {
					$this->Content = $this->Content . "checked></TD><TD><input id=\"write" . $row["GID"] . "\" type=\"checkbox\" value=\"true\" name=\"write" . $row["GID"] . "\" ";
					if($row["WriteOK"] == 1) {
						$this->Content = $this->Content . "checked";
					}
					$this->Content = $this->Content . ">";
				} else {
					$this->Content = $this->Content . "></TD><TD><input id=\"write" . $row["GID"] . "\" type=\"checkbox\" value=\"true\" name=\"write" . $row["GID"] . "\" disabled>";
					
				}
				$i = $i + 1;
				$this->Content = $this->Content . "</TD></TR>";
			}

			$this->Content = $this->Content . "</TABLE><BR><BR><input type=\"submit\" name=\"Update Group Rights\" value=\"Update Group Rights\"></FORM></CENTER>";
		}

	}

	//*******************************************************
	//Name: ListCat
	//Desc: This will allow a user to add pages to a Category or remove
	//a page from a Category. 
	function ListCat() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if($this->SelectedPage == 0) {
			$this->Content = $this->Content . "<Center><H3>You need to select a page first!</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			
			$glist = $_POST['Cats'];
						
			$query = "DELETE FROM PagesIsOfCats WHERE PageID=" . $this->SelectedPage;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			
			if ($glist){
	 			foreach ( $glist as $newcat)
				{
					
					$query = "insert into PagesIsOfCats (CatID, PageID) values(" . $newcat . "," . $this->SelectedPage . ")";
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				}
			}
						
			$this->Content = $this->Content . "<center><H3>Categories for page updated!</H3></center>";

		} else {
			$this->Content = $this->Content . "<center><br>\n<H3>Categories Page Is In</H3><br>\n<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Update\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"SelectedPage\" value=\"" . $this->SelectedPage . "\"><input type=\"hidden\" name=\"fn\" value=\"ListCat\"><SELECT name=\"Cats[]\" multiple>";

			$query = "SELECT PageCategories.CatID, PageCategories.Name, PagesIsOfCats.PageID FROM PageCategories LEFT JOIN PagesIsOfCats ON PageCategories.CatID=PagesIsOfCats.CatID AND PagesIsOfCats.PageID=" . $this->SelectedPage;

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "\n<option value=\"" . $row["CatID"] . "\"";
				if($row["PageID"] == $this->SelectedPage) {
					$this->Content = $this->Content . " selected";
				}
				$this->Content = $this->Content . ">" . $row["Name"] . "</option>";
			}

			$this->Content = $this->Content . "\n</SELECT><br>\n<input type=\"submit\" name=\"ChgCat\" value=\"Update\"></form></center>";
		}
		
	}

	function DeleteCat() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();
		
		$CatID = 0;
		if (isset($_SESSION["ODOSessionO"]->EscapedVars["CatID"])) {
			$CatID = $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
		} 

		//if no cat is selected then show a list of cats and number of pages in.
		if($CatID == 0) {
			//output cats in pages.
			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
				$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			}

			//load UserRecords
			$query = "SELECT PageCategories.CatID, PageCategories.Name, PageCategories.Description, count(*) from PageCategories LEFT JOIN PagesIsOfCats ON PageCategories.CatID = PagesIsOfCats.CatID group by PageCategories.CatID";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			$RecordCount = mysqli_num_rows($TempRecords);
			if($RecordCount == 0) {
				$this->Content = $this->Content . "<BR><H3>No Categories Found!</H3></center>";
				return;
			}

			$NumofPages = $RecordCount / 15;
			$Count = 0;

			if( ($RecordCount % 15 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
			} else {
				$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
				mysqli_data_seek($TempRecords, $ChangeToRow);

				$this->Content = $this->Content . "<br>\n<H2>Page Category List</H2><br><br><table border=1><TH>Category Name</TH><TH>Category Description</TH><TH>Number of Pages</TH><TH>Delete Category</TH>";
				while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
					$this->Content = $this->Content . "\n<TR><td>" . $row["Name"]. "</td><td>" . $row["Description"] . "</td><td>" . $row["count(*)"] . "</td><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteCat\"><input type=\"hidden\" name=\"CatID\" value=\"" . $row["CatID"] . "\"><td><input type=\"submit\" name=\"Delete\" value=\"Delete\"></td></form></TR>";

					$Count = $Count + 1;
				}
				
				$this->Content = $this->Content . "</table>\n\n<br><br>";
				$CurPage = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"];
		
				//we want to count down the Pages
				$NumberofLinks = 1;
				//$i is the current position
				$i = $CurPage - 4;
				if($i < 1) {
					$i = 1;
				}

				if(($CurPage > 5)) {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=DeleteCat&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=DeleteCat&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

				if($i > $NumofPages) {
					$this->Content = $this->Content . "&nbsp;Next->";
				} else {
					$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=DeleteCat&PageNum=" . $i . "\">Next-></a>";
				}

				$this->Content = $this->Content . "</center>";
			}

		} else {
			//data validation
			$query = "SELECT * FROM PageCategories WHERE CatID=" . $CatID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($TempRecords) > 0) {
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["ConfirmDel"])) {
					$query = "DELETE FROM PageCategories WHERE CatID=" . $CatID;
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
		
					$query = "DELETE FROM PagesIsOfCats WHERE CatID=" . $CatID;
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
					$this->Content = $this->Content . "<BR><H3>Category has been deleted!</H3></center>";
				} else {
					$row = mysqli_fetch_assoc($TempRecords);
					$this->Content = $this->Content . "<BR><H3>Are you sure you want to delete Category <B>" . $row["Name"] . "</B></H3><br><br><table border=0><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteCat\"><input type=\"hidden\" name=\"ConfirmDel\" value=\"1\"><input type=\"hidden\" name=\"CatID\" value=\"" . $row["CatID"] . "\"><td><input type=\"submit\" name=\"Delete\" value=\"Delete\"></td></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteCat\"><TD><input type=\"submit\" name=\"No\" value=\"No\"></TD></form></TR></table></center>";

				}
			} else {
				$this->Content = $this->Content . "<H3>Category ID does not exist!</H3></center>";
			}

		}

	}

	function NewCat() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["CreateNew"])) {
			//check to make sure Cat Doesn't exist.
			$query = "SELECT * FROM PageCategories WHERE Name='" . $_SESSION["ODOSessionO"]->EscapedVars["CatName"] . "'";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($TempRecords) > 0) {
				$this->Content = $this->Content . "<br>\n<H3>Category already exists.</H3>\n<br>";
				return;
			} 

			$query = "INSERT INTO PageCategories (Name, Description) values ('" . $_SESSION["ODOSessionO"]->EscapedVars["CatName"] . "', '" . $_SESSION["ODOSessionO"]->EscapedVars["CatDesc"] . "')";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) {
				$this->Content = $this->Content . "<center><br>\n<B>Category added</B></center>";
				
				$query = "SELECT * FROM PageCategories WHERE Name='" . $_SESSION["ODOSessionO"]->EscapedVars["CatName"] . "'";
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);

				$row = mysqli_fetch_assoc($TempRecords);
						
			
				
			} else {
				trigger_error("There was an error adding the new Category...", E_USER_ERROR);
			}
			

		} else {
			$this->Content = $this->Content . "<center><br>\n<H3>Create New Category</H3><br>\n<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"CreateNew\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"NewCat\"><table border=1>\n<TR><TD>Category Name:</TD><TD><input type=\"text\" name=\"CatName\" maxlength=\"254\" size=\"10\"></TD></TR>\n<TR><TD>Category Description: </TD><TD><input type=\"text\" name=\"CatDesc\" maxlength=\"254\" size=\"20\"></TD></TR>\n</table><br>\n<input type=\"submit\" name=\"Create\" value=\"Create\"></form></center>";

		}

	}

	function SearchPages() {

		$this->ClearPage();
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();
		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Search"])) {
			
			//build query
			$FirstFlag = false;

			//link url is built if Search was set so when we reach the page links at the bottom
			//everything will be filled in already that needs to be.
			$LinkURL = "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=SearchPages&Search=true";

			$query = "select * from ODOPages WHERE ";

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["PageID"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["PageID"]) > 0)) {
				$query = $query . "PageID=" . $_SESSION["ODOSessionO"]->EscapedVars["PageID"] . " ";
				$LinkURL = $LinkURL . "&PageID=" . $_SESSION["ODOSessionO"]->EscapedVars["PageID"];
				$FirstFlag = true;
			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["PageName"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["PageName"]) > 0)) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "PageName LIKE '%" . $_SESSION["ODOSessionO"]->EscapedVars["PageName"] . "%' ";
				$LinkURL = $LinkURL . "&PageName=" . $_SESSION["ODOSessionO"]->EscapedVars["PageName"];
			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["PageDesc"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["PageDesc"]) > 0)) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "PageDesc LIKE '%" . $_SESSION["ODOSessionO"]->EscapedVars["PageDesc"] . "%' ";
				$LinkURL = $LinkURL . "&PageDesc=" . $_SESSION["ODOSessionO"]->EscapedVars["PageDesc"];
			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["IsDynamic"])) && ($_SESSION["ODOSessionO"]->EscapedVars["IsDynamic"] != "DoNotCheck")) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "IsDynamic=";
				$LinkURL = $LinkURL . "&IsDynamic=" . $_SESSION["ODOSessionO"]->EscapedVars["IsDynamic"];
				//we're doing a string comparison here because the variable should not contain more
				//than one type int/string. The variable has to be string because of the DoNotCheck value.
				if($_SESSION["ODOSessionO"]->EscapedVars["IsDynamic"] == "1") {
					$query = $query . "1 ";
					
				} else {
					$query = $query . "0 ";
					
				}

			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"])) && ($_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"] != "DoNotCheck")) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "IsAdmin=";
				$LinkURL = $LinkURL . "&IsAdmin=" . $_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"];
				//we're doing a string comparison here because the variable should not contain more
				//than one type int/string. The variable has to be string because of the DoNotCheck value.
				if($_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"] == "1") {
					$query = $query . "1 ";
					
				} else {
					$query = $query . "0 ";
					
				}

			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["ModID"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["ModID"]) > 0)) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "ModID=" . $_SESSION["ODOSessionO"]->EscapedVars["ModID"] . " ";
				$LinkURL = $LinkURL . "&ModID=" . $_SESSION["ODOSessionO"]->EscapedVars["ModID"];
			} else {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "ModID<>100 ";
			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["Author"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["Author"]) > 0)) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "Author=" . $_SESSION["ODOSessionO"]->EscapedVars["Author"] . " ";
				$LinkURL = $LinkURL . "&Author=" . $_SESSION["ODOSessionO"]->EscapedVars["Author"];
			}

			if(!$FirstFlag) {
				$this->Content = $this->Content . "<B>You must select a search value to search by! Please try again!</B></center>";
				
				return;	
			}

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			
			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
				$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			}
			
			$RecordCount = mysqli_num_rows($TempRecords);

			if($RecordCount < 1) {
				$this->Content = $this->Content . "<br><H3>No records match search query...</H3></center>";
				return;
			}

			$NumofPages = $RecordCount / 15;
			$Count = 0;

			if( ($RecordCount % 15 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			
			if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
			} else {
				$this->Content = $this->Content . "<br><br><table border=1><TH>Page ID</TH><th>Page Name</th><th>Page Description</th><TH>Mod ID</TH><th>Page Author ID</th><th>Created Date</th><TH>Edit Page</TH><th>Delete Page</th>";

				$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
				mysqli_data_seek($TempRecords, $ChangeToRow);
				while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
					$this->Content = $this->Content . "<tr><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"EditPage\"><input type=\"hidden\" name=\"PageID\" value=\"" . $row["PageID"] . "\"><TD>" . $row["PageID"] . "</TD><td>" . $row["PageName"] . "</td><td>" . $row["PageDesc"] . "</td><td>" . $row["ModID"] . "</td><td>" . $row["Author"] . "</td><td>" . $row["CreatedDate"] . "</TD><td align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></form>";

					$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"DeletePage\"><input type=\"hidden\" name=\"PageID\" value=\"" . $row["PageID"] . "\"><td align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></td></tr></form>";
			
			
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
					$this->Content = $this->Content . $LinkURL . "&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
						//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . $LinkURL . "&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

				if($i > $NumofPages) {
					$this->Content = $this->Content . "&nbsp;Next->";
				} else {
					$this->Content = $this->Content . "&nbsp;" . $LinkURL . "&PageNum=" . $i . "\">Next-></a>";
				}

				$this->Content = $this->Content . "</center>";
			}
			

		} else {
			$this->Content = $this->Content . "<br><H3>Search For Pages</H3><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Search\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"SearchPages\"><table border=1><TR><TD>By PageID: </td><td><input type=\"text\" name=\"PageID\" maxsize=\"10\" size=\"5\"></td></tr><tr><TD>Page Name:</td><td><input type=\"text\" name=\"PageName\" maxsize=\"254\" size=\"10\"></td></tr><tr><td>Page Description Has:</td><td><input type=\"text\" name=\"PageDesc\" maxsize=\"254\" size=\"25\"></td></tr><tr><td>Dynamic flag:</td><td><SELECT name=\"IsDynamic\"><option value=\"1\">Yes</option>&nbsp;<option value=\"0\">No</option><option value=\"DoNotCheck\" selected>Do Not Check</option></SELECT></td></tr>";

			$this->Content = $this->Content . "<tr><td>Admin flag:</td><td><SELECT name=\"IsAdmin\"><option value=\"1\">Yes</option>&nbsp;<option value=\"0\">No</option><option value=\"DoNotCheck\" selected>Do Not Check</option></SELECT></td></tr><tr><td>Module ID:</td><td><input type=\"text\" name=\"ModID\" maxsize=\"10\" size=\"5\"></td></tr><tr><td>Author ID:</td><td><input type=\"text\" name=\"Author\" maxsize=\"10\" size=\"5\"></td></tr></table><br><br><input type=\"submit\" name=\"Submit\" value=\"Search\"></form></center>";


		}
	}
	
	//************************************************************
	//ob: ODOMCSPages
	//fn: CatView
	//Description: This will list all Pages in the system grouped
	//by their categories. 
	//*************************************************************
	function CatView() {

		$this->ClearPage();
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();
		
		$CatID = 0;

		if (isset($_SESSION["ODOSessionO"]->EscapedVars["CatID"])) {
			$CatID = $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
		} 

		//if no cat is selected then show a list of cats and number of pages in.
		if($CatID == 0) {
			//output cats in pages.
			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
				$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			}

			//load UserRecords
			$query = "SELECT PageCategories.CatID, PageCategories.Name, PageCategories.Description, count(*) from PageCategories, PagesIsOfCats where PageCategories.CatID = PagesIsOfCats.CatID group by PageCategories.CatID";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$RecordCount = mysqli_num_rows($TempRecords);
			$NumofPages = $RecordCount / 15;
			$Count = 0;

			if( ($RecordCount % 15 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
			} else {
				$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
				mysqli_data_seek($TempRecords, $ChangeToRow);

				$this->Content = $this->Content . "<BR><H2>Category View</H2><br>\n<table border=1><TR><TH>Category Name</TH><TH>Category Description</TH><TH>Number of Pages</TH><TH>View List</TH></TR>";
				while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
					$this->Content = $this->Content . "\n<TR><td>" . $row["Name"]. "</td><td>" . $row["Description"] . "</td><td>" . $row["count(*)"] . "</td><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"CatView\"><input type=\"hidden\" name=\"CatID\" value=\"" . $row["CatID"] . "\"><td><input type=\"submit\" name=\"View\" value=\"View\"></td></form></TR>";

					$Count = $Count + 1;
				}
				
				$this->Content = $this->Content . "</table>\n\n<br><br>";
				$CurPage = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"];
		
				//we want to count down the Pages
				$NumberofLinks = 1;
				//$i is the current position
				$i = $CurPage - 4;
				if($i < 1) {
					$i = 1;
				}

				if(($CurPage > 5)) {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

			if($NumofPages >= ($i+5)) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&PageNum=" . ($i+5) . "\">Next-></a>";
			} elseif($NumofPages > $i) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&PageNum=" . $NumofPages . "\">Next-></a>";
				
			} else {
				$this->Content = $this->Content . "&nbsp;Next->";
			}

				$this->Content = $this->Content . "</center>";
			}

		} else {
			//provide a link at the top for listing all cats again
			$this->Content = $this->Content . "<br>\n<center><a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&ClearCat=True\">Go Back To Category List</a><br><br>\n\n";

			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
				$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			}

			//load UserRecords
			$query = "SELECT * FROM ODOPages, PagesIsOfCats WHERE PagesIsOfCats.CatID = " . $CatID . " AND PagesIsOfCats.PageID = ODOPages.PageID";
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

				$this->Content = $this->Content . "<BR>\n<H2>Page List</H2>\n<br><table border=1><TH>Page ID</TH><th>Page Name</th><th>Page Description</th><TH>Mod ID</TH><th>Page Author ID</th><th>Created Date</th><TH>Edit Page</TH><th>Delete Page</th>";
				while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {

					$this->Content = $this->Content . "<tr><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"EditPage\"><input type=\"hidden\" name=\"SelectedPage\" value=\"" . $row["PageID"] . "\"><td>" . $row["PageID"] . "</td><td>" . $row["PageName"] . "</td><td>" . $row["PageDesc"] . "</td><td>" . $row["ModID"] . "</td><td>" . $row["Author"] . "</td><td>" . $row["CreatedDate"] . "<td align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></form>";

					$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"DeletePage\"><input type=\"hidden\" name=\"SelectedPage\" value=\"" . $row["PageID"] . "\"><td align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></td></tr></form>";
			
			
					$Count = $Count + 1;
				}
				
				$this->Content = $this->Content . "</table>\n\n<br><br>";
				$CurPage = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"];
		
				//we want to count down the Pages
				$NumberofLinks = 1;
				//$i is the current position
				$i = $CurPage - 4;
				if($i < 1) {
					$i = 1;
				}

				if(($CurPage > 5)) {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

				if($NumofPages >= ($i+5)) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&PageNum=" . ($i+5) . "\">Next-></a>";
			} elseif($NumofPages > $i) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=CatView&PageNum=" . $NumofPages . "\">Next-></a>";
				
			} else {
				$this->Content = $this->Content . "&nbsp;Next->";
			}

				$this->Content = $this->Content . "</center>";
			}
			
		}
		

	}

	function DeletePage() {
		
		if($this->SelectedPage == 0) {
			$this->Content = $this->Content . "<Center><H3>You need to select a page first!</H3></center>";
			return;
		}


		if((isset($_SESSION["ODOSessionO"]->EscapedVars["validate"])) && ($_SESSION["ODOSessionO"]->EscapedVars["validate"]=="Yes")) {
			$PageID = $this->SelectedPage;
			$this->ClearPage();
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

			$this->LoadODOMenus();
			
			//First log delete.
			$Comment = "User has deleted a page in the system.";
			$GLOBALS['globalref'][4]->LogEvent("DELETEPAGE", $Comment, 1);
			
			$query = "DELETE FROM ODOPages WHERE PageID=" . $PageID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "DELETE FROM ODOGACL WHERE PageID=" . $PageID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "DELETE FROM ODOPageObject WHERE PageID=" . $PageID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "DELETE FROM PagesIsOfCats WHERE PageID=" . $PageID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "SELECT * FROM ODOTree WHERE PageID=" . $PageID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
	
			while($row = mysqli_fetch_assoc($TempRecords)) {
				$query = "DELETE FROM ODOMenus WHERE StaticLinkID=" . $row["PriKey"];
				$TempRecords2 = $GLOBALS['globalref'][1]->Query($query);
			}

			$query = "DELETE FROM ODOTree WHERE PageID=" . $PageID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->Content = $this->Content . "<br><br><H3><B>Page Deleted!</B></H3></center>";

		} else {

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedPage"])) && ($_SESSION["ODOSessionO"]->EscapedVars["SelectedPage"] != $this->SelectedPage)) {
				$this->SelectPage($_SESSION["ODOSessionO"]->EscapedVars["PageID"]);
			}

			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

			$this->LoadODOMenus();

			$this->Content = $this->Content . "<h3><B>Are you sure you want to delete this page?</B></H3><BR>\n<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"DeletePage\"><input type=\"hidden\" name=\"validate\" value=\"Yes\"><table><tr><td><input type=\"submit\" name=\"Yes\" value=\"Yes\">&nbsp;&nbsp;</td><input type=\"hidden\" name=\"SelectedPage\" value=\"". $this->SelectedPage . "\"></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"SelectedPage\" value=\"" . $this->SelectedPage . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"EditPage\"><td>&nbsp;&nbsp;<input type=\"submit\" name=\"No\" value=\"No\"></td></tr></table></form></center>";
		}

	}


	function ListPages() {
		//check count and starting point
		$this->ClearPage();

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
		}

		//load UserRecords
		$query = "select * from ODOPages where ModID != 100 ORDER BY PageName ASC";
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		$RecordCount = mysqli_num_rows($TempRecords);
		$NumofPages = $RecordCount / 15;
		$Count = 0;

		if( ($RecordCount % 15 > 0) ) {
			$NumofPages = $NumofPages + 1;
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();

		
		if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
			$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
		} else {
			$this->Content = $this->Content . "<br><B><H2>Page List</H2></B><br><table border=1><TH>Page ID</TH><th>Page Name</th><th>Page Description</th><TH>Mod ID</TH><th>Page Author ID</th><th>Created Date</th><TH>Edit Page</TH><th>Delete Page</th>";

			$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
			mysqli_data_seek($TempRecords, $ChangeToRow);
			while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
				$this->Content = $this->Content . "<tr><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"EditPage\"><input type=\"hidden\" name=\"SelectedPage\" value=\"" . $row["PageID"] . "\"><td>" . $row["PageID"] . "</td><td>" . $row["PageName"] . "</td><td>" . $row["PageDesc"] . "</td><td>" . $row["ModID"] . "</td><td>" . $row["Author"] . "</td><td>" . $row["CreatedDate"] . "<td align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></form>";

				$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"DeletePage\"><input type=\"hidden\" name=\"SelectedPage\" value=\"" . $row["PageID"] . "\"><td align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></td></tr></form>";
			
			
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
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=ListPages&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
			} else {
				$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=ListPages&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if($NumofPages >= ($i+5)) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=ListPages&PageNum=" . ($i+5) . "\">Next-></a>";
			} elseif($NumofPages > $i) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSPagesO&fn=ListPages&PageNum=" . $NumofPages . "\">Next-></a>";
				
			} else {
				$this->Content = $this->Content . "&nbsp;Next->";
			}

			$this->Content = $this->Content . "</center>";
		}

	}


	//Function: ODOCMSPages::NewPage()
	//Desc: This will setup and create a new entry for a page. It will not create the content
	//of the page. Depending on what the user selected it will place a call to EditPage to create
	//or insert the data for the content.
	function NewPage() {
		$this->Content = "";
		$this->ClearPage();

		

		//check for flag. We need to call the EditPage after 
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["NewFlag"])) {
			//We have data so insert and update local vars then call Edit function
			$query = "insert into ODOPages (PageName,PageDesc,PageContent,IsDynamic,IsAdmin,ModID,Author,CreatedDate,LastModBy,LastModDate) values ('";
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["PageName"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["PageName"]) > 0)) {
				$query2 = "select * from ODOPages where PageName='" . $_SESSION["ODOSessionO"]->EscapedVars["PageName"] . "'";
				$Result = $GLOBALS['globalref'][1]->Query($query2);
				if(mysqli_num_rows($Result) > 0) {
					trigger_error("Page Name already defined!");
					exit();
				}

				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["PageName"] . "','";	
			} else {
				//throw error
				trigger_error("No Page Name Passed!");
				exit();
			}
		
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["PageDesc"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["PageDesc"]) > 0)) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["PageDesc"] . "',";	
			} else {
				//throw error
				trigger_error("No Page Description Passed!");
				$query = $query . "',";
			}

			
			if((isset($_FILES['userfile']))&&($_FILES['userfile']['size']>0)) {
				if($_FILES['userfile']['error'] != UPLOAD_ERR_OK) {
					trigger_error("File Upload Error: " . $_FILES['userfile']['error']);
					exit();
				} else {
					if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
						$fileoutput = file_get_contents($_FILES['userfile']['tmp_name']);
						$query = $query . "'" . $GLOBALS['globalref'][1]->EscapeMe($fileoutput) . "',";
					} else {
						trigger_error("File Upload Name not valid!: " . $_FILES['userfile']['tmp_name'], E_USER_ERROR);
					}	
				}
				
			} else {
				if((isset($_SESSION["ODOSessionO"]->EscapedVars["pagecontent"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["pagecontent"]) > 0)) {
					$query = $query . "'" . $_SESSION["ODOSessionO"]->EscapedVars["pagecontent"] . "',";
				} else {
					$query = $query . "'Placeholder',";
				}

			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsDynamic"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["IsDynamic"] . ",";	
			} else {
				//throw error
				trigger_error("No Page type selected!");
				exit();
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"] . ",";	
			} else {
				//throw error
				trigger_error("No Admin value selected!");
				exit();
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["ModID"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["ModID"] . ",";	
			} else {
				//throw error
				trigger_error("No Module type selected!");
				exit();
			}
	
			$query = $query . $_SESSION["ODOUserO"]->getUID() . ",'" . date( 'Y-m-d H:i:s', time() ) . "',";
			$query = $query . $_SESSION["ODOUserO"]->getUID() . ",'" . date( 'Y-m-d H:i:s', time() ) . "')";
			$Result = $GLOBALS['globalref'][1]->Query($query);
			
			$query = "select * from ODOPages where PageName='" . $_SESSION["ODOSessionO"]->EscapedVars["PageName"] . "'";
			$Result = $GLOBALS['globalref'][1]->Query($query);
			$row = mysqli_fetch_assoc($Result);
			$this->SelectedPage = $row["PageID"];
			$this->PageName = $row["PageName"];
			$this->Author = $row["Author"];
			$this->CreateDate = $row["CreatedDate"];
			$this->LastMod = $row["LastModBy"];
			$this->LastModDate = $row["LastModDate"];
	
			//before we call to load menus we must update our escaped vars
			$_SESSION["ODOSessionO"]->EscapedVars["SelectedPage"] = $this->SelectedPage;
			$_POST["SelectedPage"] = $this->SelectedPage;
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

			$this->LoadODOMenus();
			$this->Content = $this->Content . "<br><B>Page Created</B></center>";
			
			
		} else {
			$this->Content = "<Center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

			$this->LoadODOMenus();

			$this->Content = $this->Content . "<H2><B>New Page</B></H2>\n<br><form action=\"index.php\" enctype=\"multipart/form-data\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"NewPage\"><input type=\"hidden\" name=\"NewFlag\" value=\"1\"><table border=1><TR><TD><B>Page Name:</B></TD><TD><input type=\"text\" name=\"PageName\"></TD></TR><TR>\n<TD><B>Page Description:</B></TD><TD><input type=\"text\" name=\"PageDesc\"></TD></TR>\n<TR><TD><B>Page is Dynamic:</B></TD><TD><select name=\"IsDynamic\"><option value=\"0\" selected>Static</option><option value=\"1\">Dynamic</option></select></TD></TR>\n";

			$this->Content = $this->Content . "<TR><TD><B>Page is Administrative:</B></TD><TD><select name=\"IsAdmin\"><option value=\"1\">Yes</option><option value=\"0\" selected>No</option></select></TD></TR>\n<TR><TD><B>Page is in Module:</B></TD><TD><select name=\"ModID\">";

			//Load ModId's
			$query = "select * from Modules";
			$Result = $GLOBALS['globalref'][1]->Query($query);
			
			while($row = mysqli_fetch_assoc($Result)){
				$this->Content = $this->Content . "<option value=\"" . $row["ModID"] . "\">" . $row["ModName"] . "</option>\n";
			}
			
			$this->Content = $this->Content . "</select></TD></TR></TABLE><br>Upload File?<input type=\"checkbox\" value=\"false\" name=\"UploadFile\" onclick=\"ShowUpload()\"><br>\n<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5000000\" />File To Upload (Max 5MB): <input id=\"imafile\" name=\"userfile\" type=\"file\" disabled/><br><textarea id=\"imnotafile\" NAME=\"pagecontent\" COLS=120 ROWS=40></textarea><br>\n<input type=\"submit\" name=\"AddNew\" value=\"Add New\"></center>";

		}

	}

	//Function: ODOCMSPages::EditPage()
	//Params: Type= 0-We are creating a dynamic PHP page, 1-We are creating a static PHP page,
	//2-We are uploading a file
	//Description: This function uploads files to the database and edits pages of the system
	//it can not create new entries.
	function EditPage() {
		


		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

		$this->LoadODOMenus();

		if($this->SelectedPage == 0) {
			$this->Content = $this->Content . "<br><H3>You must select a page to edit a page!</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["EditFlag"])) {
			$query = "UPDATE ODOPages SET PageName='";
			//,PageDesc,PageContent,IsDynamic,IsAdmin,ModID,Author,CreatedDate,LastModBy,LastModDate
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["PageName"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["PageName"]) > 0)) {
				if( $this->PageName != $_SESSION["ODOSessionO"]->EscapedVars["PageName"] ) {
					$query2 = "select * from ODOPages where PageName='" . $_SESSION["ODOSessionO"]->EscapedVars["PageName"] . "'";
					$Result = $GLOBALS['globalref'][1]->Query($query2);
					if(mysqli_num_rows($Result) > 0) {
						trigger_error("Page Name already defined!");
						exit();
					} else {
						$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["PageName"] . "', PageDesc='";	
					}

				} else {

					$query = $query . $this->PageName . "', PageDesc='";	
				}

			} else {
				//throw error
				trigger_error("No Page Name Passed!");
				exit();
			}
			
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["PageDesc"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["PageDesc"]) > 0)) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["PageDesc"] . "', PageContent=";	
			} else {
				//throw error
				trigger_error("No Page Description Passed!");
				$query = $query . "', PageContent=";
			}

			$NewPageContent = "";
			if((isset($_FILES['userfile']))&&($_FILES['userfile']['size']>0)) {
				
				if($_FILES['userfile']['error'] != UPLOAD_ERR_OK) {
					trigger_error("File Upload Error: " . $_FILES['userfile']['error']);
					exit();
				} else {
					if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
						$fileoutput = file_get_contents($_FILES['userfile']['tmp_name']);
						$NewPageContent = $GLOBALS['globalref'][1]->EscapeMe($fileoutput);
						
					} else {
						trigger_error("File Upload Name not valid!: " . $_FILES['userfile']['tmp_name'], E_USER_ERROR);
						exit();
					}	
				}
				
			} else {
				if((isset($_SESSION["ODOSessionO"]->EscapedVars["pagecontent"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["pagecontent"]) > 0)) {
					$NewPageContent = $_SESSION["ODOSessionO"]->EscapedVars["pagecontent"];
				} else {
					$NewPageContent = "PLACE HOLDER";
				}
			}
			
			$query = $query . "'" . $NewPageContent . "', IsDynamic=";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsDynamic"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["IsDynamic"] . ", IsAdmin=";	
			} else {
				//throw error
				trigger_error("No Page type selected!");
				exit();
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["IsAdmin"] . ", ModID=";	
			} else {
				//throw error
				trigger_error("No Admin value selected!");
				exit();
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["ModID"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["ModID"] . ", LastModBy=";	
			} else {
				//throw error
				trigger_error("No Module type selected!");
				exit();
			}
	
			$mysqldate = date( 'Y-m-d H:i:s', time() );
			
			$query = $query . $_SESSION["ODOUserO"]->getUID() . ", LastModDate='" . $mysqldate . "' WHERE PageID=" . $this->SelectedPage;
			$Result = $GLOBALS['globalref'][1]->Query($query);
			$tempPageID = $this->SelectedPage;
			
			$this->ClearPage();
			$this->SelectPage($tempPageID);

			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSPage");

			$this->LoadODOMenus();

			$this->Content = $this->Content . "<br><H3>Page has been updated.</H3></center>";
	

		} else {

			$query = "SELECT * FROM ODOPages WHERE PageID=" . $this->SelectedPage;
			$Result = $GLOBALS['globalref'][1]->Query($query);
			$row = mysqli_fetch_assoc($Result);
			$Description = $row["PageDesc"];
			$MyContent = $row["PageContent"];
			$this->Content = $this->Content . "<center><B><H2>Edit Page</H2></B>\n<br><table border=1><form action=\"index.php\" enctype=\"multipart/form-data\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSPagesO\"><input type=\"hidden\" name=\"fn\" value=\"EditPage\"><input type=\"hidden\" name=\"SelectedPage\" value=\"". $this->SelectedPage . "\"><input type=\"hidden\" name=\"EditFlag\" value=\"1\"><TR><TD><B>Page Name:</B></TD><TD><input type=\"text\" name=\"PageName\" maxlength=\"254\" value=\"" . $this->PageName . "\"></TD></TR>";

			$this->Content = $this->Content . "<TR><TD><B>Page Description:</B></TD><TD><textarea rows=\"4\" cols=\"40\" maxlength=\"254\" name=\"PageDesc\">" . $Description . "</textarea>\n</TD></TR><TR><TD><B>Page is Dynamic:</B></TD><TD><select name=\"IsDynamic\"><option value=\"0\"";

			if($this->IsDynamic) {
				$this->Content = $this->Content . ">Static</option><option value=\"1\" selected>Dynamic</option></select>\n</TD></TR>";
			} else {
				$this->Content = $this->Content . " selected>Static</option><option value=\"1\">Dynamic</option></select>\n</TD></TR>";
			}

			
			$this->Content = $this->Content . "<TR><TD><B>Page is Administrative:</B></TD><TD><select name=\"IsAdmin\"><option value=\"1\"";

			if($this->IsAdmin) {
				$this->Content = $this->Content . " selected>Yes</option><option value=\"0\">No</option></select>\n</TD></TR><TR><TD><B>Module:</B></TD><TD><select name=\"ModID\">";
			} else {
				$this->Content = $this->Content . ">Yes</option><option value=\"0\" selected>No</option></select>\n</TD></TR><TR><TD><B>Module:</B></TD><TD><select name=\"ModID\">";
			}

				

			//Load ModId's
			$query = "select * from Modules";
			$Result = $GLOBALS['globalref'][1]->Query($query);
			
			while($row = mysqli_fetch_assoc($Result)){
				if($this->ModID == $row["ModID"]) {
					$this->Content = $this->Content . "<option value=\"" . $row["ModID"] . "\" selected>" . $row["ModName"] . "</option>\n";
				} else {
					$this->Content = $this->Content . "<option value=\"" . $row["ModID"] . "\">" . $row["ModName"] . "</option>\n";
				}
			}
			
			$this->Content = $this->Content . "</select></TD></TR></table><br>Upload File?<input type=\"checkbox\" value=\"false\" name=\"UploadFile\" onclick=\"ShowUpload()\"><br>\n<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5000000\" />File To Upload (Max 5MB): <input id=\"imafile\" name=\"userfile\" type=\"file\" disabled/><br><textarea id=\"imnotafile\" NAME=\"pagecontent\" COLS=120 ROWS=40>" . htmlspecialchars($MyContent) . "</textarea><br><br><input type=\"submit\" name=\"Update\" value=\"Update\"></center>";
		}

	}



//footer

	function __sleep() {
		$this->Content = "";
		$this->PageName = "";
		$this->Author = "";
		$this->CreateDate = "";
		$this->LastMod = "";
		$this->LastModDate = "";
		$this->IsDynamic = 0;
		$this->IsAdmin = 0;
		$this->ModID = 0;
			
		return( array_keys( get_object_vars( $this ) ) );
	}
	
	function __wakeup() {
		
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedPage"]))&&($_SESSION["ODOSessionO"]->EscapedVars["SelectedPage"] != 0)) {
			$this->SelectedPage = $_SESSION["ODOSessionO"]->EscapedVars["SelectedPage"];
			$this->SelectPage($this->SelectedPage); 
			
		} else {
			$this->ClearPage();
		}
	
	}

}


?> 