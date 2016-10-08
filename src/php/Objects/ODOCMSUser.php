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

class ODOCMSUsers {

	private $CurUser;
	private $CurUserLogCount;
	private $PrevUser;
	var $Content;
	var $UserName;
	var $email;
	var $FName;
	var $LName;
	var $iv;
	

	function __construct() {
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["userid"])) {
			$this->CurUser = $_SESSION["ODOSessionO"]->EscapedVars["userid"];
			$this->PrevUser = $this->CurUser;
		} else {
			$this->CurUser = 0;
			$this->PrevUser = 0;
		}
		$this->Content = "";
		$this->UserName = "";
		$this->email = "";
		$this->FName = "";
		$this->LName = "";
		$this->iv = "";
		$this->CurUserLogCount=0;
	}

	function LoadUserMenus() {

		if($this->CurUser != 0) {
			//then we load user drop down menus
			//Menu Item Selected User
		
			
			if($this->CurUser != $this->PrevUser) {
				$query = "select IsEncrypted, emailadd, rnameFirst, rnameLast, EncIV, user from ODOUsers where UID=" . $this->CurUser;
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);

				if($row = mysqli_fetch_assoc($TempRecords)){
					//update local vars here
					$rnameL = "";
					$rnameF = "";
					$email = "";
					$uname = $row["user"];
					$iv = "";
					if(($row["IsEncrypted"] == 1)&&($_SESSION["ODOUtil"]->isMcryptAvailable())) {
						$encData = new EncryptedData();
					
						$encData->encArray["rnameLast"] = $row["rnameLast"];
						$encData->encArray["rnameFirst"] = $row["rnameFirst"];
						$encData->encArray["emailadd"] = $row["emailadd"];
						$encData->iv = $row["EncIV"];

						$encData = $_SESSION["ODOUtil"]->ODODecrypt($encData);
						
						$rnameL = $encData->decArray["rnameLast"];
						$rnameF = $encData->decArray["rnameFirst"];
						$email = $encData->decArray["emailadd"];
						$iv = $encData->iv;
						
					} else {
						$rnameL = $row["rnameLast"];
						$rnameF = $row["rnameFirst"];
						$email = $row["emailadd"];
					}
			

					$this->email = $email;
					$this->UserName = $uname;
					$this->FName = $rnameF;
					$this->LName = $rnameL;
					$this->iv = $iv;
					$this->PrevUser = $this->CurUser;
						
					
				}
			}
			
			//insert selected user code here
			$this->Content = $this->Content . "\n<br><h4>Selected User</h4><table border=1><tr><td><B>Username: </b></td><td><B>" . $this->UserName . "</B></td></tr>\n<tr><td><B>UID: </B></td><td><B>" . $this->CurUser . "</B></td></tr>\n<tr><td><B>Last Name, First Name</B></td><td><B>" . $this->LName . ",&nbsp;" . $this->FName . "</B></td></tr>\n<tr><td><B>E-Mail: </B></td><td><B>" . $this->email . "</B></td></tr></table>";

		} else {
				$this->UserName = "";
				$this->email = "";
				$this->FName = "";
				$this->LName = "";
				$this->CurUserLogCount=0;
				$this->PrevUser = 0;
				
		}
	}


	function Search() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Search"])) {
			
			//build query
			$FirstFlag = false;

			//link url is built if Search was set so when we reach the page links at the bottom
			//everything will be filled in already that needs to be.
			$LinkURL = "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=Search&Search=true";

			$query = "select * from ODOUsers WHERE ";

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["uid"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["uid"]) > 0)) {
				//Do additional check for isalpha
				if(!(is_numeric($_SESSION["ODOSessionO"]->EscapedVars["uid"]))) {
					trigger_error("Invalid userid! UID must be numeric!");
					return;
				}

				$query = $query . "UID=" . $_SESSION["ODOSessionO"]->EscapedVars["uid"] . " ";
				$LinkURL = $LinkURL . "&uid=" . $_SESSION["ODOSessionO"]->EscapedVars["uid"];
				$FirstFlag = true;
			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["username"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["username"]) > 0)) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "user LIKE '" . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "%' ";
				$LinkURL = $LinkURL . "&username=" . $_SESSION["ODOSessionO"]->EscapedVars["username"];
			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["fname"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["fname"]) > 0)) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "rnameFirst LIKE '" . $_SESSION["ODOSessionO"]->EscapedVars["fname"] . "%' ";

				$LinkURL = $LinkURL . "&fname=" . $_SESSION["ODOSessionO"]->EscapedVars["fname"];
			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["lname"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["lname"]) > 0)) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "rnameLast LIKE '" . $_SESSION["ODOSessionO"]->EscapedVars["lname"] . "%' ";
				$LinkURL = $LinkURL . "&lname=" . $_SESSION["ODOSessionO"]->EscapedVars["lname"];
			}

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["email"])) && (strlen($_SESSION["ODOSessionO"]->EscapedVars["email"]) > 0)) {
				if($FirstFlag) {
					$query = $query . "AND ";
				} else { 
					$FirstFlag = true;
				}
				$query = $query . "emailadd LIKE '" . $_SESSION["ODOSessionO"]->EscapedVars["email"] . "%' ";
				$LinkURL = $LinkURL . "&email=" . $_SESSION["ODOSessionO"]->EscapedVars["email"];
			}

			$query = $query . " ORDER BY user";
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

			$this->Content = $this->Content . "<br><br><table border=1><TH>User ID</TH><th>User Name</th><th>User's Real Name</th><th>User's e-mail address</th><th>Select User</th><th>Delete User</th>";

			if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				$this->Content = "<center><h4>Page number is out of bounds!</h4><br><br></center>";
			} else {
				$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
				mysqli_data_seek($TempRecords, $ChangeToRow);
				while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
					$rnameL = "";
					$rnameF = "";
					$email = "";
					
					if(($row["IsEncrypted"] == 1)&&($_SESSION["ODOUtil"]->isMcryptAvailable())) {
						$encData = new EncryptedData();
					
						$encData->encArray["rnameLast"] = $row["rnameLast"];
						$encData->encArray["rnameFirst"] = $row["rnameFirst"];
						$encData->encArray["emailadd"] = $row["emailadd"];
						$encData->iv = $row["EncIV"];

						$encData = $_SESSION["ODOUtil"]->ODODecrypt($encData);
						
						$rnameL = $encData->decArray["rnameLast"];
						$rnameF = $encData->decArray["rnameFirst"];
						$email = $encData->decArray["emailadd"];
						
					} else {
						$rnameL = $row["rnameLast"];
						$rnameF = $row["rnameFirst"];
						$email = $row["emailadd"];
					}
					
					$this->Content = $this->Content . "<tr><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"EditUser\"><input type=\"hidden\" name=\"userid\" value=\"" . $row["UID"] . "\"><input type=\"hidden\" name=\"user\" value=\"" . $row["user"] . "\"><td>" . $row["UID"] . "</td><td>" . $row["user"] . "</td><td>" . $rnameL . ", " . $rnameF . "</td><td>" . $email . "</td><td align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></form>";

					$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteUser\"><input type=\"hidden\" name=\"userid\" value=\"" . $row["UID"] . "\"><input type=\"hidden\" name=\"user\" value=\"" . $row["user"] . "\"><td align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></td></tr></form>";
			
			
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
			}
	
			$this->Content = $this->Content . "</center>";

		} else {
			$this->Content = $this->Content . "<br><H4>Search For User</H4><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Search\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"Search\"><table border=1><TR><TD>By UID: </td><td><input type=\"text\" name=\"uid\" maxsize=\"10\" size=\"5\"></td></tr><tr><TD>User Name:</td><td><input type=\"text\" name=\"username\" maxsize=\"10\" size=\"10\"></td></tr><tr><td>First Name:</td><td><input type=\"text\" name=\"fname\" maxsize=\"100\" size=\"25\"></td></tr>";

			$this->Content = $this->Content . "<tr><td>Last Name:</td><td><input type=\"text\" name=\"lname\" maxsize=\"100\" size=\"25\"></td></tr><tr><td>E-mail Addr:</td><td><input type=\"text\" name=\"email\" maxsize=\"100\" size=\"50\"></td></tr></table><br><br><input type=\"submit\" name=\"Submit\" value=\"Search\"></form></center>";


		}

	}

	function ShowUserLogs() {
	
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		if($this->CurUser != 0){
			
			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
				$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			}
			
			if($this->CurUserLogCount==0) {
				$query = "SELECT count(*) as ULogs FROM ODOLogs WHERE UID=" . $this->CurUser;
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				if(mysqli_num_rows($TempRecords) > 0) {
					$row = mysqli_fetch_assoc($TempRecords);
					$this->CurUserLogCount = $row["ULogs"];
				} else {
					trigger_error("Error: NO LOGS FOR USER!", E_USER_NOTICE);
					$this->Content = $this->Content . "<B>NO USER LOGS!</B>";
					return;
				}
			}
			
			$LimitStart = 0;
			
			if($_SESSION["ODOSessionO"]->EscapedVars["PageNum"]  > 1) {
				$LimitStart = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"]*20;
			}				
			
			//check bounds first
			$NumofPages = $this->CurUserLogCount / 20;
			$Count = 0;

			if( ($this->CurUserLogCount % 20 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				
				$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
			
			} else {
			
				$query = "select * from ODOLogs where UID=" . $this->CurUser . " order by TimeStamp DESC LIMIT " . $LimitStart . ",20";
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);

				//simple table format
				$this->Content = $this->Content . "<table border=1><th>Time Stamp</th><th>Action</th><th>Comment</th><th>Severity</th><th>IP</th>";

				while(($Count < 20) && ($row = mysqli_fetch_assoc($TempRecords))) {
				
					$this->Content = $this->Content . "<tr><td>" . $row["TimeStamp"] . "</td><td>" . $row["ActionDone"] . "</td><td>" . $row["Comment"] . "</td><td>" . $row["Severity"] . "</td><td>" . $row["IP"] . "</td></tr>";
				
					$Count = $Count + 1;
				}


				$this->Content = $this->Content . "</table><BR><BR>";
				
				
				$CurPage = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"];
		
				//we want to count down the Pages
				$NumberofLinks = 1;
				//$i is the current position
				$i = $CurPage - 4;
				if($i < 1) {
					$i = 1;
				}

				if(($CurPage > 5)) {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=ShowUserLogs&userid=" . $this->CurUser . "&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=ShowUserLogs&userid=" . $this->CurUser . "&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

				if($i > $NumofPages) {
					$this->Content = $this->Content . "&nbsp;Next->";
				} else {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=ShowUserLogs&userid=" . $this->CurUser . "&PageNum=" . $i . "\">Next-></a>";
				}

			
			}
			
		}
	}

	function EditGroups() {

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		if($this->CurUser == 0) {
			$this->Content = $this->Content . "<br><br><h3><b>You must select a user before you can change their groups!</b></h3></center>";	
			return;
		}
		
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) && (isset($_POST["GROUPS"]))) {
			//Now update groups
			$glist = $_POST["GROUPS"];
						
			if ($glist){
				//clear out groups first
				$query = "DELETE FROM ODOUserGID WHERE UID=" . $this->CurUser;
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);

	 			foreach ( $glist as $newgroup)
				{
					$query = "insert into ODOUserGID (GID, UID) values(" . $GLOBALS['globalref'][1]->EscapeMe($newgroup) . ", " . $this->CurUser . ")";
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				}
				$this->Content = $this->Content . "<br><br><H3><b>User groups updated.</b></H3></center>";
			}
		} else {

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
				//update is true but no groups defined. So we just delete the current groups.
				//clear out groups first
				$query = "DELETE FROM ODOUserGID WHERE UID=" . $this->CurUser;
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				$this->Content = $this->Content . "<br><br><H3><b>User groups updated.</b></H3></center>";
				return;
			}

			//output form.
			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"Update\" value=\"true\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"EditGroups\"><input type=\"hidden\" name=\"userid\" value=\"" . $this->CurUser . "\"><BR><BR><H4>Select your groups for user:</H4><br><sub>Ctrl+click to unselect</sub><br><br><SELECT name=\"GROUPS[]\" multiple>";

			
			//load full group list
			$query = "select ODOGroups.GID, ODOGroups.GroupName, ODOUserGID.UID from ODOGroups left join ODOUserGID on ODOGroups.GID = ODOUserGID.GID and ODOUserGID.UID=" . $this->CurUser;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
		
			while ($row = mysqli_fetch_assoc($TempRecords)) {
	
				$this->Content = $this->Content . "<option value=\"" . $row["GID"] . "\" ";
				if($row["UID"] == $this->CurUser) {
					$this->Content = $this->Content . "selected";
				}
				$this->Content = $this->Content . ">" . $row["GroupName"] . "</option>";
			}
		
			$this->Content = $this->Content . "</SELECT><Br><br><input type=\"submit\" name=\"Change Groups\" value=\"Change Groups\"></form></center>";

		}

	}

	function ChangeUserPassword() {
		
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		if($this->CurUser == 0) {
			$this->Content = $this->Content . "<br><br><h3><b>You must select a user before you can change their password!</b></h3></center>";	
			return;
		}
		
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) && (isset($_SESSION["ODOSessionO"]->EscapedVars["newpass"])) && (isset($_SESSION["ODOSessionO"]->EscapedVars["confirmpass"])) && ($_SESSION["ODOSessionO"]->EscapedVars["newpass"] == $_SESSION["ODOSessionO"]->EscapedVars["confirmpass"])) {
			//confirm uid exists then update
			$query = "select * from ODOUsers where UID=" . $this->CurUser;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($TempRecords) < 1) { 
				$this->Content = $this->Content . "<br><H3>User not found in database!</H3></center>";
				return;
			}

			$query = "UPDATE ODOUsers SET pass=\"" . $GLOBALS['globalref'][1]->EscapeMe($_SESSION["ODOUserO"]->ODOHash($_SESSION["ODOSessionO"]->EscapedVars["newpass"], $_SESSION["ODOUserO"]->GetHashMode())) . "\", Pistemp=1, IsLocked=0, FailedLogins=0, HashedMode=" . $_SESSION["ODOUserO"]->GetHashMode() . " where UID=" . $this->CurUser;

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) { 
				$this->Content = $this->Content . "<br><H3><b>User password updated.</b></h3>";
			} else {
				$this->Content = $this->Content . "<br><H3><b>User password could not be updated!</b></H3></center>";
				return;
			}
	
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["sendmail"])) {

				$message = "Your account password has been updated at " . SERVERNAME . " for userid: " . $this->UserName . "\nYour new password to login is: " . $_SESSION["ODOSessionO"]->EscapedVars["newpass"];

				if((strlen($this->email) < 3) || (!mail($this->email, "Password Reset", $message))) {
					$this->Content = $this->Content . "<B>E-MAIL FAILED TO BE SENT!!!!</B><br>Temp password is: " . $_SESSION["ODOSessionO"]->EscapedVars["newpass"];
				} else {
					$this->Content = $this->Content . "<br>The password has been e-mailed to: " . $this->email;
				}
			}

			$this->Content = $this->Content . "</center>";

		} else {
		
			$this->Content = $this->Content . "<br><br><H3><B>Change user password:</B></H3><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"userid\" value=\"" . $this->CurUser . "\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"ChangeUserPassword\"><input type=\"hidden\" name=\"Update\" value=\"true\">New Password:<input type=\"text\" name=\"newpass\" value=\"";

			$newpass = $_SESSION["ODOUtil"]->randomstring(10);

			$this->Content = $this->Content . $newpass . "\" size=12 maxlength=10><br>Confirm New Password:<input type=\"text\" name=\"confirmpass\" value=\"" . $newpass . "\" size=12 maxlength=10><br><INPUT TYPE=CHECKBOX NAME=\"sendmail\">Mail user temp password?<br><br><input type=\"submit\" name=\"Change\" value=\"Change\"></form></center>";

		}

	}

	function EditUser() {
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["userid"])) {
			$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");
			
			$this->CurUser = 0;
			
			$this->LoadUserMenus();
			$this->Content = $this->Content . "<br><H3>User not selected?</H3></center>";
			return;
		}

		
		$this->CurUser = $_SESSION["ODOSessionO"]->EscapedVars["userid"];
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {

			//check if username has changed
			$query = "select * from ODOUsers where UID=" . $this->CurUser;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($TempRecords) < 1) { 
				$this->Content = $this->Content . "<br><H3>User not found in database!</H3></center>";
				return;
			}

			$row = mysqli_fetch_assoc($TempRecords);
	
			if($row["user"] != $_SESSION["ODOSessionO"]->EscapedVars["username"]) {
				//check if new username is already used. 
				$query = "select * from ODOUsers where user=\"" . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "\"";
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);

				if(mysqli_num_rows($TempRecords) > 0) { 
					$this->Content = $this->Content . "<br><H3>Username is already defined!</H3></center>";
					return;
				}
			}

			//update database
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["Encrypted"]))&&(($_SESSION["ODOSessionO"]->EscapedVars["Encrypted"] == "On")||($_SESSION["ODOSessionO"]->EscapedVars["Encrypted"] == "OnKeep"))&&($_SESSION["ODOUtil"]->isMcryptAvailable())) {
				
				//Create EncryptedData container
				//@TODO add reset question
				$encData = new EncryptedData();
				$encData->decArray["rnameLast"] = $_SESSION["ODOSessionO"]->EscapedVars["rnameLast"];
				$encData->decArray["rnameFirst"] = $_SESSION["ODOSessionO"]->EscapedVars["rnameFirst"];
				$encData->decArray["emailadd"] = $_SESSION["ODOSessionO"]->EscapedVars["emailadd"];
				
				if($_SESSION["ODOSessionO"]->EscapedVars["Encrypted"] == "OnKeep") {
					$encData->iv = $this->iv;
				} 
				
				$encData = $_SESSION["ODOUtil"]->ODOEncrypt($encData);
				//@TODO update Question encryption
				$query = "UPDATE ODOUsers SET user=\"" . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "\", rnameLast=\"" . $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["rnameLast"]) . "\", rnameFirst=\"" . $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["rnameFirst"]) . "\", emailadd=\"" . $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["emailadd"]) . "\", IsEncrypted=1, EncIV='" . $GLOBALS['globalref'][1]->EscapeMe($encData->iv) . "', Pistemp=";
			
			} else {
				
				$query = "UPDATE ODOUsers SET user=\"" . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "\", rnameLast=\"" . $_SESSION["ODOSessionO"]->EscapedVars["rnameLast"] . "\", rnameFirst=\"" . $_SESSION["ODOSessionO"]->EscapedVars["rnameFirst"] . "\", emailadd=\"" . $_SESSION["ODOSessionO"]->EscapedVars["emailadd"] . "\", IsEncrypted=0, EncIV=NULL, Pistemp=";
			
			}
			
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["pistemp"]) && ($_SESSION["ODOSessionO"]->EscapedVars["pistemp"] == "Yes")) {
				$query = $query . "1, ";
			} else {
				$query = $query . "0, ";
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["IsLocked"]) && ($_SESSION["ODOSessionO"]->EscapedVars["IsLocked"] == "Yes")) {
				$query = $query . "IsLocked=1, ";
			} else {
				$query = $query . "IsLocked=0, ";
			}

			if(is_numeric($_SESSION["ODOSessionO"]->EscapedVars["FailedLogins"])) {
				$query = $query . "FailedLogins=" . $_SESSION["ODOSessionO"]->EscapedVars["FailedLogins"];
			} else {
				$query = $query . "FailedLogins=0";
			}

			$query = $query . " WHERE UID=" . $this->CurUser;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) { 
				$this->Content = $this->Content . "<H3><b>User has been updated.</b></H3></center>";
			} else {
				$this->Content = $this->Content . "<H3><B>Unspecified error trying to update user!</B></h3></center>";
			}

		} else {
			$query = "select IsEncrypted, Pistemp, IsLocked, FailedLogins from ODOUsers WHERE UID=" . $this->CurUser;
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
		
			if(mysqli_num_rows($TempRecords) < 1) { 
				$this->Content = $this->Content . "<br><H3>User not found in database!</H3></center>";
				return;
			}
		
			$row = mysqli_fetch_assoc($TempRecords);
		
			$this->Content = $this->Content . "<br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"userid\" value=\"" . $this->CurUser . "\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"EditUser\"><input type=\"hidden\" name=\"Update\" value=\"true\">\n\n<table border=1>\n<tr><td>UserID:</td><td>" . $this->CurUser . "</td></tr>\n<tr><td>User Name:</td>";

			$this->Content = $this->Content . "<td><input type=\"text\" name=\"username\" value=\"" . $this->UserName . "\" maxsize=\"20\" size=\"12\"></td></tr>\n<tr><td>First Name:</td><td><input type=\"text\" name=\"rnameFirst\" value=\"" . $this->FName . "\" maxsize=\"100\" size=\"25\"></td></tr>\n<tr><td>Last Name:</td><td><input type=\"text\" name=\"rnameLast\" value=\"" . $this->LName . "\" maxsize=\"100\" size=\"25\"></td></tr>\n<tr><td>E-Mail:</td><td><input type=\"text\" name=\"emailadd\" value=\"" . $this->email . "\" maxsize=\"100\" size=\"25\"></td></tr>\n<tr><td>Password is Temp?</td><td><select name=\"pistemp\"><option value=\"Yes\"";

			if($row["Pistemp"]) {
				$this->Content = $this->Content . " selected>Yes</option><option value=\"No\">No</option></select>";
			} else {
				$this->Content = $this->Content . ">Yes</option><option value=\"No\" selected>No</option></select>";
			}
	
			$this->Content = $this->Content . "</td></tr>\n<tr><td>Is Locked</td><td><select name=\"IsLocked\"><option value=\"Yes\"";

			if($row["IsLocked"]) {
				$this->Content = $this->Content . " selected>Yes</option><option value=\"No\">No</option></select>";
			} else {
				$this->Content = $this->Content . ">Yes</option><option value=\"No\" selected>No</option></select>";
			}

			$this->Content = $this->Content . "</TD></TR>\n<TR><TD>Encryption enabled for user?</TD><TD>";
			
			
			if($_SESSION["ODOUtil"]->isMcryptAvailable()) {
				
				$this->Content = $this->Content . "<SELECT name=\"Encrypted\">\n<option value=\"OnKeep\"";
				
				$selectOption = 0;
				
				if(isset($row["IsEncrypted"])&&($row["IsEncrypted"]==1)) {
					
					if(strlen($this->iv)>0) {
						$selectOption = 1;
					} else {
						$selectOption = 2;
					}
										
				} else {
					$selectOption = 3;
				}
				
				switch($selectOption) {
					case 1:
						$this->Content = $this->Content . " selected";
						break;
					case 2:
						$this->Content = $this->Content . " disabled=\"disabled\"";
						break;
				}
				
				$this->Content = $this->Content . ">On (Keep current IV)</option>\n<option value=\"On\"";
				
				switch($selectOption) {
					case 2:
						$this->Content = $this->Content . " selected";
						break;
				}
				
				$this->Content = $this->Content . ">On (Generate new IV)</option>\n<option value=\"Off\"";
				
				switch($selectOption) {
					case 3:
						$this->Content = $this->Content . " selected";
						break;
				}
				
				$this->Content = $this->Content . ">Off</option>\n</select>\n";
				
			} else {
				
				$this->Content = $this->Content . "Encryption is not available on server. <BR><sub>(install mcrypt on server and/or set ENCRYPTODOUSER constant)</sub>";
				
			}
			
			$this->Content = $this->Content . "</td></tr><tr><td>Failed Logins</td><td><input type=\"text\" name=\"FailedLogins\" value=\"" . $row["FailedLogins"] . "\"></td></tr></table><br><br><input type=\"submit\" name=\"Update\" value=\"Update\"></form></center>";
		}
	}

	function NewUser() {
		
		$this->CurUser = 0;
		$this->CurUserLogCount = 0;
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["username"])) {
			$this->Content = $this->Content . "<H3><B>Creating User..." . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "</B></H3><br>";
			//generate password.
			$tempPass = $_SESSION["ODOUtil"]->randomstring(10);
			
			//check if username already exists.
			$query = "select * from ODOUsers where user='" . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "'";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
		
			if(mysqli_num_rows($TempRecords) > 0) { 
				$this->Content = $this->Content . "<br><B>ERROR CREATING USER!: Username already exists!</b></center>";
				return;
			}

			//create user
			$query = "insert into ODOUsers (user, pass, rnameLast, rnameFirst, emailadd, Pistemp, ResetQID, ResetA, IsLocked, FailedLogins, IsEncrypted, HashedMode, EncIV) values ('" . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "', '" . $GLOBALS['globalref'][1]->EscapeMe($_SESSION["ODOUserO"]->ODOHash($tempPass, $_SESSION["ODOUserO"]->GetHashMode())) . "',";
			
			$fname = $_SESSION["ODOSessionO"]->EscapedVars["fname"];
			$lname = $_SESSION["ODOSessionO"]->EscapedVars["lname"];
			$email = $_SESSION["ODOSessionO"]->EscapedVars["email"];
			
			$IsEncrypted = 0;
			$EncIV = "";
			
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["Encrypted"]))&&($_SESSION["ODOSessionO"]->EscapedVars["Encrypted"]=="On")&&($_SESSION["ODOUtil"]->isMcryptAvailable())) {
				//Create EncryptedData container
				$encData = new EncryptedData();
				
				if(strlen($_SESSION["ODOSessionO"]->EscapedVars["lname"]) > 0) {
					$encData->decArray["rnameLast"] = $_SESSION["ODOSessionO"]->EscapedVars["lname"];
				}
				
				if(strlen($_SESSION["ODOSessionO"]->EscapedVars["fname"]) > 0) {
					$encData->decArray["rnameFirst"] = $_SESSION["ODOSessionO"]->EscapedVars["fname"];
				}
				
				if(strlen($_SESSION["ODOSessionO"]->EscapedVars["email"]) > 0) {
					$encData->decArray["emailadd"] = $_SESSION["ODOSessionO"]->EscapedVars["email"];
				}
				
				$encData = $_SESSION["ODOUtil"]->ODOEncrypt($encData);
			
				if(isset($encData->encArray["rnameFirst"])) {
					$fname = $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["rnameFirst"]);
				}
				
				if(isset($encData->encArray["rnameLast"])) {
					$lname = $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["rnameLast"]);
				}
				
				if(isset($encData->encArray["emailadd"])) {
					$email = $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["emailadd"]);
				}
					
				$EncIV = $GLOBALS['globalref'][1]->EscapeMe($encData->iv);
				
			} 
			
			if(strlen($lname) > 0) {
				$query = $query . "'" . $lname . "',";
			} else {
				$query = $query . "NULL,";
			}
			
			if(strlen($fname) > 0) {
				$query = $query . "'" . $fname . "',";
			} else {
				$query = $query . "NULL,";
			}
			
			if(strlen($email)>0) {
				$query = $query . "'" . $email . "',";
			} else {
				$query = $query . "NULL,";
			}
			
			//Password is temporary on new
			$query = $query . "1,";
			
			//confirm rquestion ID
			
			$questionquery = "SELECT * FROM ResetQuestions WHERE ResetQID=" . $_SESSION["ODOSessionO"]->EscapedVars["RQuestion"];
			$questionresult = $GLOBALS['globalref'][1]->Query($questionquery);

			if(mysqli_num_rows($questionresult)>0) {
				$query .= $_SESSION["ODOSessionO"]->EscapedVars["RQuestion"] . ",";
			} else {
				$query .= "NULL,";
			}
			

			
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["ResetA"])&&(strlen($_SESSION["ODOSessionO"]->EscapedVars["ResetA"])>0)) {
				$query = $query . "'" . $GLOBALS['globalref'][1]->EscapeMe($_SESSION["ODOUserO"]->ODOHash($_SESSION["ODOSessionO"]->EscapedVars["ResetA"], $_SESSION["ODOUserO"]->GetHashMode())) . "',";
			} else {
				$query = $query . "NULL,";
			}
			
			//Locked turned off by default on new
			//Failed logins set to 0 by default
			$query =  $query . "0, 0,";
			
			//set IV for user
			if(strlen($EncIV)>0) {
				//encryption is on so set it to on
				$query = $query . "1," . $_SESSION["ODOUserO"]->GetHashMode() . ",'" . $EncIV . "')";
			} else {
				$query = $query . "0," . $_SESSION["ODOUserO"]->GetHashMode() . ",NULL)";
			}
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			//get last UID created
			$query = "select * from ODOUsers where user='" . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "'";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			if($row = mysqli_fetch_assoc($TempRecords)) {
				$this->CurUser = $row["UID"];
				$Comment = "User: " . $row["user"] . " has been created by Admin: " . $_SESSION["ODOUserO"]->getUID();
				$GLOBALS['globalref'][4]->LogEvent("USERCREATED", $Comment, 1);
			} else {
				$this->Content = $this->Content . "<br><B>ERROR CREATING USER!</b></center>";
				return;
			}

			//Now update groups
			
			
						
			if (isset($_POST["GROUPS"])) {
				
				$glist = $_POST["GROUPS"];
				
	 			foreach ( $glist as $newgroup)
				{
					$query = "insert into ODOUserGID (GID, UID) values(" . $GLOBALS['globalref'][1]->EscapeMe($newgroup) . ", " . $this->CurUser . ")";
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				}
			}
					
			//email user
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["sendmail"])) {

				$message = "Your account has been created at " . SERVERNAME . " for userid: " . $_SESSION["ODOSessionO"]->EscapedVars["username"] . "\nYour new password to login is: " . $tempPass;

				if((strlen($_SESSION["ODOSessionO"]->EscapedVars["email"]) < 3) || (!mail($_SESSION["ODOSessionO"]->EscapedVars["email"], "Password Reset", $message))) {
					$this->Content = $this->Content . "<B>E-MAIL FAILED TO BE SENT!!!!</B><br>Temp password is: " . $tempPass;
				} else {
					$this->Content = $this->Content . "<br>The password has been e-mailed to: " . $_SESSION["ODOSessionO"]->EscapedVars["email"];
				}
			} else {
				
				$this->Content = $this->Content . "<br>No e-mail sent. Temporary password is: " . $tempPass;
			}

			$this->Content = $this->Content . "<br><B>User Created.</B></center>";

		} else {
			$this->Content= $this->Content . "<H3><b>Create New User</B></H3><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"NewUser\">\n<table border=1>\n<tr><td>UserName: </td><td><input type=\"text\" name=\"username\" size=\"12\" maxlength=\"20\"></td></tr>\n";

			$this->Content = $this->Content . "<tr><td>First Name: </td><td><input type=\"text\" name=\"fname\" size=\"15\" maxlength=\"100\"></td></tr>\n<tr><td>Last Name: </td><td><input type=\"text\" name=\"lname\" size=\"15\" maxlength=\"100\"></td></tr>\n<tr><td>E-Mail: </td><td><input type=\"text\" name=\"email\" size=\"25\" maxlength=\"100\"></td></tr>";
			
			$this->Content = $this->Content . "</TD></TR>\n<TR><TD>Encryption enabled for user?</TD><TD>";
			
			
			if($_SESSION["ODOUtil"]->isMcryptAvailable()) {
				
				$this->Content = $this->Content . "<SELECT name=\"Encrypted\">\n<option value=\"OnKeep\" disabled=\"disabled\">On (Keep current IV)</option>\n<option value=\"On\" selected>On (Generate new IV)</option>\n<option value=\"Off\">Off</option>\n</select>\n</TD></TR>\n";
	
			} else {
				
				$this->Content = $this->Content . "Encryption is not available on server. <BR><sub>(install mcrypt on server and/or set ENCRYPTODOUSER constant)</sub></TD></TR>\n";
				
			}
			
			
			//get reset questions available
			$query = "SELECT * FROM ResetQuestions";
			
			$result = $GLOBALS['globalref'][1]->Query($query);
						
			$this->Content = $this->Content . "<TR><TD>Password Reset Question:</TD><TD>\n<SELECT NAME=\"RQuestion\">\n<OPTION value=\"0\" selected></OPTION>\n";
			
			while($row = mysqli_fetch_assoc($result)) {
				
				$this->Content = $this->Content . "<OPTION value=\"" . $row["ResetQID"] . "\">" . $row["ResetQ"] . "</OPTION>";

			}

			$this->Content = $this->Content . "</SELECT></TD></TR>\n<tr><td>Reset Answer:</td><td><input type=\"text\" name=\"ResetA\" size=\"25\" maxlength=\"100\"></td></tr></table><br><br><SELECT name=\"GROUPS[]\" multiple>";

			//load full group list
			$query = "select * from ODOGroups";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
		
			while ($row = mysqli_fetch_assoc($TempRecords)) {
	
				$this->Content = $this->Content . "<option value=\"" . $row["GID"] . "\"" . ">" . $row["GroupName"] . "</option>";
			}
		
			$this->Content = $this->Content . "</SELECT><br><INPUT TYPE=CHECKBOX NAME=\"sendmail\">Mail user temp password?<br><br><input type=\"submit\" name=\"Create\" value=\"Create\"></form></center>";	
		}
	}

	function DeleteUser() {
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["DeleteMe"])) {
			
			$query = "select * from ODOUsers where UID=" . $_SESSION["ODOSessionO"]->EscapedVars["userid"];
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			if(mysqli_num_rows($TempRecords) < 1) {
			
				$this->Content = $this->Content . "<H3>Username does not exist!</H3><br></center>";

			} else {
				
				//remove user from ODOUsers
				
				//delete from user/group table and user table
				$this->Content = $this->Content . "<h3>Removing User. Please wait...</h3><br><br>";

				$query = "delete from ODOUsers where UID=" . $_SESSION["ODOSessionO"]->EscapedVars["userid"];
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				if(!($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0)) {
					$this->Content = $this->Content . "<B>Error removing user!</B></center>";
					return;
				}
				
				$this->Content = $this->Content . "<B>User removed! Removing groups please wait....</B><br><br>";
				$query = "delete from ODOUserGID where UID=" . $_SESSION["ODOSessionO"]->EscapedVars["userid"];
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);
				if(!($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0)) {
					$this->Content = $this->Content . "<B>Error removing groups!</B></center>";
					return;
				}
	
				$this->Content = $this->Content . "<B>Groups removed from user.</B><br><br><H3>USER REMOVED!</H3></center>";
				$Comment = "User removed from system: " . $_SESSION["ODOSessionO"]->EscapedVars["user"];
				$GLOBALS['globalref'][4]->LogEvent("USERDELETED", $Comment, 1);
				$this->CurUserLogCount = 0;
				$this->CurUser = 0;
			}

		} else {
			//Confirm the deleting...
			$this->Content = $this->Content . "<H3>Are you sure you want to delete user: <BR><B>" . $_SESSION["ODOSessionO"]->EscapedVars["user"] . "</B></H3><br><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteUser\"><input type=\"hidden\" name=\"userid\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["userid"] . "\"><input type=\"hidden\" name=\"user\" value=\"" . $_SESSION["ODOSessionO"]->EscapedVars["user"] . "\"><input type=\"hidden\" name=\"DeleteMe\" value=\"true\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></form>&nbsp;&nbsp;&nbsp;&nbsp;";

			$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"lstUsers\"><input type=\"submit\" name=\"Cancel\" value=\"Cancel\"></form></center>";
		}

	}

	function lstByGroups() {
		
		$grpid = 0;
		$this->CurUser = 0;
		$this->CurUserLogCount = 0;
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		$query = "select * from ODOGroups";
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);
		
		$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"lstByGroups\"><H4><B>Select Group: </B></H4><select name=\"grpid\">\n";

		while($row = mysqli_fetch_assoc($TempRecords)) {
			$this->Content = $this->Content . "<option value=\"" . $row["GID"] . "\"";
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["grpid"]))&&($row["GID"] == $_SESSION["ODOSessionO"]->EscapedVars["grpid"])) {
				$this->Content = $this->Content . " selected ";
				$grpid = $row["GID"];
			}
			$this->Content = $this->Content . ">" . $row["GroupName"] . "</option>\n";
			
		}

		$this->Content = $this->Content . "</select><br>\n<input type=\"submit\" name=\"ChangeToGroup\" value=\"Change View To Group\"></form><br><br>";

		//Next Load Page
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
		}

		//load UserRecords
		$query = "select ODOUsers.UID, ODOUsers.user, ODOUsers.rnameFirst, ODOUsers.rnameLast, ODOUsers.emailadd, ODOUsers.IsEncrypted, ODOUsers.EncIV from ODOUsers, ODOUserGID WHERE ODOUserGID.GID = " . $grpid . " AND ODOUsers.UID = ODOUserGID.UID ORDER BY user";

		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		$RecordCount = mysqli_num_rows($TempRecords);
		$NumofPages = $RecordCount / 15;
		$Count = 0;

		if( ($RecordCount % 15 > 0) ) {
			$NumofPages = intval($NumofPages) + 1;
		}

		if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
			$this->Content = $this->Content . "<center><h4>Select a group of users to view.</h4><br><br></center>";
		} else {
			
			$this->Content = $this->Content . "<br><br><table border=1><TH>User ID</TH><th>User Name</th><th>User's Real Name</th><th>User's e-mail address</th><th>Select User</th><th>Delete User</th>";

			$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
			mysqli_data_seek($TempRecords, $ChangeToRow);
			while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
				
				$rnameL = "";
				$rnameF = "";
				$email = "";
					
				if(($row["IsEncrypted"] == 1)&&($_SESSION["ODOUtil"]->isMcryptAvailable())) {
					$encData = new EncryptedData();
					
					$encData->encArray["rnameLast"] = $row["rnameLast"];
					$encData->encArray["rnameFirst"] = $row["rnameFirst"];
					$encData->encArray["emailadd"] = $row["emailadd"];
					$encData->iv = $row["EncIV"];

					$encData = $_SESSION["ODOUtil"]->ODODecrypt($encData);
						
					$rnameL = $encData->decArray["rnameLast"];
					$rnameF = $encData->decArray["rnameFirst"];
					$email = $encData->decArray["emailadd"];
						
				} else {
					$rnameL = $row["rnameLast"];
					$rnameF = $row["rnameFirst"];
					$email = $row["emailadd"];
				}
					
					
				$this->Content = $this->Content . "<tr><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"EditUser\"><input type=\"hidden\" name=\"userid\" value=\"" . $row["UID"] . "\"><input type=\"hidden\" name=\"user\" value=\"" . $row["user"] . "\"><td>" . $row["UID"] . "</td><td>" . $row["user"] . "</td><td>" . $rnameL . ", " . $rnameF . "</td><td>" . $email . "</td><td align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></form>";

				$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteUser\"><input type=\"hidden\" name=\"userid\" value=\"" . $row["UID"] . "\"><input type=\"hidden\" name=\"user\" value=\"" . $row["user"] . "\"><td align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></td></tr></form>";
			
				$Count = $Count + 1;
			}
			
			$this->Content = $this->Content . "</table><br><br>";

			$CurPage = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"];
		
			//we want to count down the Pages so we only have up to
			//10 links between the previous and next links
			$NumberofLinks = 1;
			//$i is the current link we are generating
			$i = $CurPage - 4; //at max we are going to generate back 4 links from current
			if($i < 1) { //we should never be generating a 0 or negative page number
				$i = 1;
			}

			if(($CurPage > 5)) {
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=lstByGroups&PageNum=" . ($CurPage - 5) . "&grpid=" . $_SESSION["ODOSessionO"]->EscapedVars["grpid"] . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
			} else {
				$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=lstByGroups&PageNum=" . $i . "&grpid=" . $_SESSION["ODOSessionO"]->EscapedVars["grpid"] . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if($NumofPages >= ($i+5)) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=lstByGroups&PageNum=" . ($i+5) . "&grpid=" . $_SESSION["ODOSessionO"]->EscapedVars["grpid"] . "\">Next-></a>";
			} elseif($NumofPages > $i) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=lstByGroups&PageNum=" . $NumofPages . "&grpid=" . $_SESSION["ODOSessionO"]->EscapedVars["grpid"] . "\">Next-></a>";
				
			} else {
				$this->Content = $this->Content . "&nbsp;Next->";
			}

		}
	}

	function lstUsers() {
		//check count and starting point
		$this->CurUser = 0;
		$this->CurUserLogCount = 0;

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
		}

		//load UserRecords
		$query = "select * from ODOUsers ORDER BY user";
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		$RecordCount = mysqli_num_rows($TempRecords);
		$NumofPages = $RecordCount / 15;
		$Count = 0;

		if( ($RecordCount % 15 > 0) ) {
			$NumofPages = intval($NumofPages) + 1;
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSUser");

		$this->LoadUserMenus();

		$this->Content = $this->Content . "<br><br><table border=1><TH>User ID</TH><th>User Name</th><th>User's Real Name</th><th>User's e-mail address</th><th>Select User</th><th>Delete User</th>";

		if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
			$this->Content = "<center><h4>Page number is out of bounds!</h4><br><br></center>";
		} else {
			$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
			mysqli_data_seek($TempRecords, $ChangeToRow);
			while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
				
				
				$rnameL = "";
				$rnameF = "";
				$email = "";
					
				if(($row["IsEncrypted"] == 1)&&($_SESSION["ODOUtil"]->isMcryptAvailable())) {
					$encData = new EncryptedData();
					
					$encData->encArray["rnameLast"] = $row["rnameLast"];
					$encData->encArray["rnameFirst"] = $row["rnameFirst"];
					$encData->encArray["emailadd"] = $row["emailadd"];
					$encData->iv = $row["EncIV"];

					$encData = $_SESSION["ODOUtil"]->ODODecrypt($encData);
						
					$rnameL = $encData->decArray["rnameLast"];
					$rnameF = $encData->decArray["rnameFirst"];
					$email = $encData->decArray["emailadd"];
						
				} else {
					$rnameL = $row["rnameLast"];
					$rnameF = $row["rnameFirst"];
					$email = $row["emailadd"];
				}
					
				$this->Content = $this->Content . "<tr><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"EditUser\"><input type=\"hidden\" name=\"userid\" value=\"" . $row["UID"] . "\"><input type=\"hidden\" name=\"user\" value=\"" . $row["user"] . "\"><td>" . $row["UID"] . "</td><td>" . $row["user"] . "</td><td>" . $rnameL . ", " . $rnameF . "</td><td>" . $email . "</td><td align=\"center\"><input type=\"submit\" name=\"Edit\" value=\"Edit\"></td></form>";

				$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteUser\"><input type=\"hidden\" name=\"userid\" value=\"" . $row["UID"] . "\"><input type=\"hidden\" name=\"user\" value=\"" . $row["user"] . "\"><td align=\"center\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></td></tr></form>";
			
			
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
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=lstUsers&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
			} else {
				$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=lstUsers&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if($NumofPages >= ($i+5)) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=lstUsers&PageNum=" . ($i+5) . "\">Next-></a>";
			} elseif($NumofPages > $i) {
				$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=lstUsers&PageNum=" . $NumofPages . "\">Next-></a>";
				
			} else {
				$this->Content = $this->Content . "&nbsp;Next->";
			}

		}
	
	}

	function __sleep() {
		$this->Content = "";
		return( array_keys( get_object_vars( $this ) ) );
	}
	
} 


?>