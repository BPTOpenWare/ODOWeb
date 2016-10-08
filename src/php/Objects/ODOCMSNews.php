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

class ODOCMSNews {

	var $Content;
	var $PostID;
	var $CatID;
	var $CatName;
	var $Topic;

	function __construct() {
		$this->Content = "";
		$this->PostID = 0;
		$this->CatID = 0;
		$this->Topic = "";
		$this->CatName = "";
	}

	private function ClearPost() {
		$this->PostID = 0;
		$this->Topic = "";
	}

	private function ClearCat() {
		$this->CatID = 0;
		$this->CatName = "";
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
		//Posts
		//->New Post
		//->List Postings
		//->Edit Post
		//->Delete Post
		
		//Categories
		//Add Remove Groups
		//New Category
		//List Categories
		$this->Content = $this->Content . "<BR><BR>";
		
		if($this->PostID != 0) {
			$this->Content = $this->Content . "<table border=1><TR><TD>PostID:</TD><TD>" . $this->PostID . "</TD></TR><TR><TD>Title:</TD><TD>" . $this->Topic . "</TD></TR></TABLE><BR><BR>";
		}
		
		if($this->CatID !=0 ) {
			$this->Content = $this->Content . "<TABLE border=1><TR><TD>CatID:</TD><TD>" . $this->CatID . "</TD></TR><TR><TD>Category Name:</TD><TD>" . $this->CatName . "</TD></TR></TABLE><BR><BR>";

		}

	}

	function DeteletCategory() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();
		
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["CatID"])) {
			if($this->CatID != 0) {
				$_SESSION["ODOSessionO"]->EscapedVars["CatID"] = $this->CatID;
			} else {
				$this->Content = $this->Content . "<BR><BR><H3>Please select a Category to delete.</H3></center>";
				return;
			}
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Confirm"])) {
			$query = "DELETE FROM newsCats WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->Content = $this->Content . "<BR><BR><H3>Category Deleted.</H3></center>";

		} else {
	
			$this->Content = $this->Content . "<BR><BR><H3>Are you sure you want to delete this Category?</H3><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteCategory\"><input type=\"hidden\" name=\"CatID\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["CatID"] . "\"><input type=\"hidden\" name=\"Confirm\" value=\"Confirm\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></form></center>";


		}


	}

	function AddRemoveGroups() {
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();
		
		if($this->CatID == 0) {
			$this->Content = $this->Content . "<BR><BR><H3>You must select a Category to update!</H3></center>";
			return;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["UpdateGroups"])) {

			$this->Content = $this->Content . "<BR><BR><H3>Updating groups...</H3><BR>";

			$query = "DELETE FROM newsGACL WHERE NewsGID=" . $this->CatID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			$this->Content = $this->Content . "<B>Cleared previous groups asigned</B><BR><BR>";

			if(isset($_POST['GROUPS'])) {
				$Groups = $_POST['GROUPS'];
				foreach ( $Groups as $newg)
				{
					
					$query = "insert into newsGACL (NewsGID, GID) values(" . $this->CatID . "," . $newg . ")";
					$this->Content = $this->Content . "<BR>" . $query . "<BR>";
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				
				}
			}
	
			$this->Content = $this->Content . "<B>Groups Updated</B></center>";
		
			
		} else {

			$query = "SELECT ODOGroups.GID, ODOGroups.GroupName, ODOGroups.GroupDesc, newsGACL.NewsGID FROM ODOGroups LEFT JOIN newsGACL on ODOGroups.GID=newsGACL.GID and newsGACL.NewsGID=" . $this->CatID;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->Content = $this->Content . "<BR><BR><H3>Please select groups.</H3><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"AddRemoveGroups\"><input type=\"hidden\" name=\"UpdateGroups\" value=\"UpdateGroups\"><SELECT name=\"GROUPS[]\" multiple>";

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "\n<option value=\"" . $row["GID"] . "\"";
				
				if($row["NewsGID"] == $this->CatID) {
					$this->Content = $this->Content . " selected";
				}
				$this->Content = $this->Content . ">" . $row["GroupName"] . "</option>";
		
			}
			$this->Content = $this->Content . "</SELECT>\n<BR><input type=\"submit\" name=\"Update\" value=\"Update\"></form>";
			
		}
	
	}

	function EditCategory() {

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();
		
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["CatID"])) {
			if($this->CatID != 0) {
				$_SESSION["ODOSessionO"]->EscapedVars["CatID"] = $this->CatID;
			} else {
				$this->Content = $this->Content . "<BR><BR><H3>Please select a Category to edit.</H3></center>";
				return;
			}
		} 
		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["UpdateCat"])) {

			if((!isset($_SESSION["ODOSessionO"]->EscapedVars["Name"])) || (strlen($_SESSION["ODOSessionO"]->EscapedVars["Name"]) < 1)) {
				$this->Content = $this->Content . "<BR><BR><H3>You need to select a Name to update the Category to!</H3></center>";
				return;
			}

			$query = "UPDATE newsCats SET catName='" . $_SESSION["ODOSessionO"]->EscapedVars["Name"] . "' WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->CatID = $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
			$this->CatName = $_SESSION["ODOSessionO"]->EscapedVars["Name"];

			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

			$this->LoadODOMenus();
			
			$this->Content = $this->Content . "<BR><BR><H3>Category updated</H3></center>";
	
		} else {

			$query = "SELECT * FROM newsCats WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			if($row = mysqli_fetch_assoc($TempRecords)) {
			
				$this->CatID = $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
				$this->CatName = $row["CatName"];

				$this->Content = $this->Content . "<H3>Edit Category</H3><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"EditCategory\"><input type=\"hidden\" name=\"UpdateCat\" value=\"UpdateCat\"><input type=\"hidden\" name=\"CatID\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["CatID"] . "\"><input type=\"text\" name=\"Name\" value=\"" . $row["CatName"] . "\" size=\"50\" maxlength=\"50\"><BR><input type=\"submit\" name=\"Update\" value=\"Update\"></form>";
			} else {
				$this->Content = $this->Content . "<H3>CatID not found!</H3></center>";
			}
		}
		
	}

	function NewCategory() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();
		

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["CreateNew"])) {
			$this->Content = $this->Content . "<BR><h3>Please wait while we create Category...</h3><br><br>";

			//check if exists first
			$query = "select * from newsCats WHERE CatName='" . $_SESSION["ODOSessionO"]->EscapedVars["Name"] . "'";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($TempRecords) != 0) {
				$this->Content = $this->Content . "<H3>Category name already exists!</H3></center>";
				return;
			}

			$query = "insert into newsCats (CatName) values ('" . $_SESSION["ODOSessionO"]->EscapedVars["Name"] . "')";
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$query = "select * from newsCats WHERE CatName='" . $_SESSION["ODOSessionO"]->EscapedVars["Name"] . "'";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($TempRecords) != 0) {
				$this->Content = $this->Content . "<H3>Category could not be created</H3></center>";
				return;
			}

			$row = mysqli_fetch_assoc($TempRecords);
			$this->CatID = $row["CatID"];
			$this->CatName = $row["CatName"];

			$this->Content = $this->Content . "<H4>Created Category</H4><br><br></center>";

		} else {
			
			$this->Content = $this->Content . "<BR><H3>Add New Category</H3><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"NewCategory\">";
		
			$this->Content = $this->Content . "Name:&nbsp;<INPUT type=\"text\" name=\"Name\" size=\"50\" maxlength=\"50\" value=\"\"><br>";

			$this->Content = $this->Content . "<input type=\"submit\" name=\"CreateNew\" value=\"Create New\"></form></center>";

		}
	}

	function ListCategories() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();

		if($this->CatID != 0) {
			$this->ClearCat();
		}

		$this->Content = $this->Content . "<center><table border=1><TR><TH>CatID</TH><th>Category Name</th><th>Edit</th><th>Remove</th></TR>";

		$query = "select * from newsCats";

		$TempRecords = $GLOBALS['globalref'][1]->Query($query);
		$i = 0;
		$RecordCount = mysqli_num_rows($TempRecords);
		$NumofPages = $RecordCount / 20;
		$NumofPages = intval($NumofPages);

		if( ($RecordCount % 20 > 0) ) {
			$NumofPages = $NumofPages + 1;
		}

		if(!mysqli_num_rows($TempRecords))
		{
			//do nothing since no news arts are available
		} else {
			
			while ($row = mysqli_fetch_assoc($TempRecords)) {

				$this->Content = $this->Content . "<TR id=\"rownum" . $i . "\"";

				if($i > 19) {
					$this->Content = $this->Content . " style=display:none";
				}

				$this->Content = $this->Content . "><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"EditCategory\"><input type=\"hidden\" name=\"CatID\" value=\"" . $row["PriKey"] . "\"><td>" . $row["PriKey"] . "</td><td>" . $row["CatName"] . "</td><td><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteCategory\"><input type=\"hidden\" name=\"CatID\" value=\"" . $row["PriKey"] . "\"><td><input type=\"submit\" name=\"Delete\" value=\"Delete\"></TD></form></tr>";

				$i = $i + 1;
			}
			
		}

		$this->Content = $this->Content . "</table><br>";
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

	function DeletePost() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();
		
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PostID"])) {
			if($this->PostID != 0) {
				$_SESSION["ODOSessionO"]->EscapedVars["PostID"] = $this->PostID;
			} else {
				$this->Content = $this->Content . "<BR><BR><H3>Please select a Post to delete.</H3></center>";
				return;
			}
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Confirm"])) {
			$this->Content = $this->Content . "<BR><BR><H3>Deleting...</H3>";

			$query = "DELETE FROM news WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"];
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->Content = $this->Content . "<BR><BR>Deleted</center>";

		} else {
			$this->Content = $this->Content . "<BR><BR><H3>Are you sure you want to Delete this post?</H3><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"DeletePost\"><input type=\"hidden\" name=\"Confirm\" value=\"Confirm\"><input type=\"hidden\" name=\"PostID\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"] . "\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></form>";
		}

	}

	function EditPost() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();
			
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PostID"])) {
			if($this->PostID != 0) {
				$_SESSION["ODOSessionO"]->EscapedVars["PostID"] = $this->PostID;
			} else {
				$this->Content = $this->Content . "<BR><BR><H3>Please select a Post to edit.</H3></center>";
				return;
			}
		}
		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			$this->Content = $this->Content . "<BR><h3>Please wait while we update post...</h3><br><br>";

			$query = "UPDATE news SET Title='";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["Title"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["Title"] . "',";
			} else {
				$this->Content = $this->Content . "<H3>Error. No Title!</H3></center>";
				return;
			}

			$query = $query . " Article='" . $_SESSION["ODOSessionO"]->EscapedVars["article"] . "', CatID=";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["CATS"])) {
				$query = $query . $_SESSION["ODOSessionO"]->EscapedVars["CATS"] . ", AllowGuest=";
			} else {
				$this->Content = $this->Content . "<H3>Error. No Category!</H3></center>";
				return;
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["allowguest"])) {
				$query = $query . "1";
			} else {
				$query = $query . "0";
				
			}

			$query = $query . " WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"];
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->PostID = $_SESSION["ODOSessionO"]->EscapedVars["PostID"];
			$this->Topic = $_SESSION["ODOSessionO"]->EscapedVars["Title"];

			$this->Content = $this->Content . "<H4>Updated Post</H4><br><br></center>";

		} else {
			
			$query = "Select * from news WHERE PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"];
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$row = mysqli_fetch_assoc($TempRecords);

			$this->Content = $this->Content . "<BR><H3>Update Article</H3><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"EditPost\"><input type=\"hidden\" name=\"PostID\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"] . "\">";
		
			$this->Content = $this->Content . "Title:&nbsp;<INPUT type=\"text\" name=\"Title\" size=\"50\" maxlength=\"254\" value=\"" . $row["Title"] . "\"><br>Allow Guests to view?&nbsp;<input type=\"checkbox\" name=\"allowguest\" value=\"true\" ";

			if($row["AllowGuest"] == 1) {
				$this->Content = $this->Content . " checked><BR>";
			} else {
				$this->Content = $this->Content . " value=\"false\"><BR>";
			} 

			$query2 = "select * from newsCats";
			$TempRecords2 = $GLOBALS['globalref'][1]->Query($query2);

			$this->Content = $this->Content . "News Category:<br><SELECT name=\"CATS\">";

			while ($row2 = mysqli_fetch_assoc($TempRecords2)) {
	
				$this->Content = $this->Content . "<option value=\"" . $row2["PriKey"] . "\"";
				if($row["CatID"] == $row2["PriKey"]) {
					$this->Content = $this->Content . " selected";
				}
				$this->Content = $this->Content . ">" . $row2["CatName"] . "</option>";
			}
		
			$this->Content = $this->Content . "</SELECT><br>Article:<br><textarea NAME=\"article\" id=\"article\" COLS=80 ROWS=35>" . htmlspecialchars($row["Article"]) . "</textarea><br>";
			$this->Content = $this->Content . "<input type=\"submit\" name=\"Update\" value=\"Update\"></form></center>";

		}

	}

	function ListPosts() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();
		
		if($this->PostID != 0) {
			$this->ClearPost();
		}

		$this->Content = $this->Content . "<center><table border=1><TR><TH>Art ID</TH><th>Date Posted</th><th>Article Title</th><th>Edit</th><th>Remove</th></TR>";

		$query = "select * from news";

		$TempRecords = $GLOBALS['globalref'][1]->Query($query);
		$i = 0;
		$RecordCount = mysqli_num_rows($TempRecords);
		$NumofPages = $RecordCount / 20;
		$NumofPages = intval($NumofPages);

		if( ($RecordCount % 20 > 0) ) {
			$NumofPages = $NumofPages + 1;
		}

		if(!mysqli_num_rows($TempRecords))
		{
			//do nothing since no news arts are available
		} else {
			
			while ($row = mysqli_fetch_assoc($TempRecords)) {

				$this->Content = $this->Content . "<TR id=\"rownum" . $i . "\"";

				if($i > 19) {
					$this->Content = $this->Content . " style=display:none";
				}

				$this->Content = $this->Content . "><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"EditPost\"><input type=\"hidden\" name=\"PostID\" value=\"" . $row["PriKey"] . "\"><td>" . $row["PriKey"] . "</td><td>" . $row["Date"] . "</td><TD>" . $row["Title"] . "</td><td><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"DeletePost\"><input type=\"hidden\" name=\"PostID\" value=\"" . $row["PriKey"] . "\"><td><input type=\"submit\" name=\"Delete\" value=\"Delete\"></TD></form></tr>";

				$i = $i + 1;
			}
			
		}

		$this->Content = $this->Content . "</table><br>";
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

	function NewPost() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSNews");

		$this->LoadODOMenus();
		

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["CreateNew"])) {
			$this->Content = $this->Content . "<BR><h3>Please wait while we add posting...</h3><br><br>";

			$query = "insert into news (Title, Article, CatID, AllowGuest) values ('" . $_SESSION["ODOSessionO"]->EscapedVars["Title"] . "', '" . $_SESSION["ODOSessionO"]->EscapedVars["article"] . "', '" . $_SESSION["ODOSessionO"]->EscapedVars["CATS"] . "',";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["allowguest"])) {
				$query = $query . "1)";
			} else {
				$query = $query . "0)";
			}
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			$this->PostID = $GLOBALS['globalref'][1]->LastInsertID();
			$this->Topic = $_SESSION["ODOSessionO"]->EscapedVars["Title"];
			$this->Content = $this->Content . "<H4>Created Post</H4><br><br></center>";

		} else {
			
			$this->Content = $this->Content . "<BR><H3>Add New Article</H3><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"CMSNews\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSNewsO\"><input type=\"hidden\" name=\"fn\" value=\"NewPost\">";
		
			$this->Content = $this->Content . "Title:&nbsp;<INPUT type=\"text\" name=\"Title\" size=\"50\" maxlength=\"254\" value=\"\"><br>Allow Guests to view?&nbsp;<input type=\"checkbox\" name=\"allowguest\" value=\"true\"><br>";

			$query = "select * from newsCats";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->Content = $this->Content . "News Category:<br><SELECT name=\"CATS\">";

			while ($row = mysqli_fetch_assoc($TempRecords)) {
	
				$this->Content = $this->Content . "<option value=\"" . $row["PriKey"] . "\">" . $row["CatName"] . "</option>";
			}
		
			$this->Content = $this->Content . "</SELECT><br>Article:<br><textarea NAME=\"article\" id=\"article\" COLS=80 ROWS=35></textarea><br>";
			$this->Content = $this->Content . "<input type=\"submit\" name=\"CreateNew\" value=\"Post Article\"></form></center>";

		}

	}

//begin footer
	function __wakeup() {
		$this->Content = "";
	}


}




?>