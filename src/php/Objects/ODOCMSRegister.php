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

class ODOCMSRegister {

	var $Content;
	var $OurDataLogLength;
	
	function __construct() {
		
		$this->Content = "";
		$this->OurDataLogLength = 0;
		
	}


	function EMailNotice() {

	}

	function PrevVerified() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSRegister");

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			//reset our data length
			$this->OurDataLogLength=0;
		}
			
		if($this->OurDataLogLength==0) {
			$query = "SELECT count(*) as ULogs FROM ODOLogs WHERE ActionDone='USERREGISTEREDADMINV'";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
			if(mysqli_num_rows($TempRecords) > 0) {
				$row = mysqli_fetch_assoc($TempRecords);
				$this->OurDataLogLength = $row["ULogs"];
				
				if($this->OurDataLogLength==0) {
					$this->Content = $this->Content . "<BR><B>NO LOGS WERE FOUND!</B>";
					return;
				}
				
			} else {
				$this->Content = $this->Content . "<BR><B>NO LOGS WERE FOUND!</B>";
				return;
			}
		}
			
		$LimitStart = 0;
			
		if($_SESSION["ODOSessionO"]->EscapedVars["PageNum"]  > 1) {
			$LimitStart = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"]-1)*20;
		}				
			
		//check bounds first
		$NumofPages = $this->OurDataLogLength / 20;
		$Count = 0;

		if( ($this->OurDataLogLength % 20 > 0) ) {
			$NumofPages = $NumofPages + 1;
		}

		if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				
			$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
			
		} else {
			
			$query = "select * from ODOLogs WHERE ActionDone='USERREGISTEREDADMINV' order by TimeStamp DESC LIMIT " . $LimitStart . ",20";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			//simple table format
			$this->Content = $this->Content . "<BR><BR><H3>Logs By TimeStamp</H3><BR><table border=1><TR><TH>TimeStamp</TH><TH>Action</TH><TH>Comment</TH><TH>Severity</TH><TH>IP</TH></TR>";
	
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
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=PrevVerified&PageNum=" . ($CurPage - 5) . "\"><<</a>&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=PrevVerified&PageNum=" . ($CurPage - 1) . "\"><-Previous</a>&nbsp;&nbsp;";
				//Not counted in number of page links
			} elseif($CurPage > 1) {
				$this->Content = $this->Content . "<<&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=PrevVerified&PageNum=" . ($CurPage - 1) . "\"><-Previous</a>&nbsp;&nbsp;";
			} else {
				$this->Content = $this->Content . "<<&nbsp;<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=PrevVerified&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if(($CurPage+5) <= $NumofPages) {
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=PrevVerified&PageNum=" . ($CurPage+1) . "\">Next-></a>&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=PrevVerified&PageNum=" . ($CurPage+5) . "\">>></a>";
			} elseif($CurPage < $NumofPages) {
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=PrevVerified&PageNum=" . ($CurPage+1) . "\">Next-></a>&nbsp;>>";
			} else {
				$this->Content = $this->Content . "Next->&nbsp;>>";
			}
			
		}
		
	}

	function ViewPendingByType() {


	}

	function VerifyReg() {

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSRegister");


		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["VID"])) {
			$this->Content = $this->Content . "<BR><H3><B>Error: You must call the Verify Registration System with a valid verification specified!</B></H3></center>";
			//no error reporting done as this is likely just someone playing with the code
			return;
		} 

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["VERIFYOK"])) {
			$query = "SELECT AdminVerify.UID, AdminVerify.RegID, AdminVerify.RAnswer, ODOUsers.User, ODOUsers.emailadd, ODOUsers.rnameFirst as First, ODOUsers.rnameLast as Last, ODOUsers.IsEncrypted as Encrypted, ODOUsers.EncIV as IV FROM AdminVerify LEFT JOIN ODOUsers on AdminVerify.UID=ODOUsers.UID WHERE VID=" . $_SESSION["ODOSessionO"]->EscapedVars["VID"];
			
			$result = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($result) < 1) {
				$this->Content = $this->Content . "<BR><H3><B>Error: You must call the Verify Registration System with a valid verification specified! VID value passed was not found.</B></H3></center>";
				//no error reporting as this should only happen in the case of a user playing with post/get vars.

				return;
			}

			$row = mysqli_fetch_assoc($result);
			//temp vars needed
			$MyRegID = $row["RegID"];
			$MyID = $row["UID"];
			$MyUName = $row["User"];
			$RAnswer = "FALSE";
			$RegName = "";
			$Myemail = $row["emailadd"];
			$IsEncrypted = $row["Encrypted"];
			$IV = $row["IV"];
						
			$LName = "";
			if((!is_null($row["Last"]))||(strlen(trim($row["Last"])) > 0)) {
				$LName = $row["Last"];
			} 
			
			$FName = "";
			if((!is_null($row["First"]))||(strlen(trim($row["First"])) > 0)) {
				$FName = $row["First"];
			} 
			
			$FullName = $FName . " " . $LName;

			if((isset($row["RAnswer"])) && (!is_null($row["RAnswer"]))) {
				$RAnswer = $row["RAnswer"];
			}
			
			if($IsEncrypted == 1) {
				if($GLOBALS['globalref'][7]->isMcryptAvailable()) {
					$encData = new EncryptedData();
					$encData->iv = $IV;
					$encData->encArray["emailadd"] = $Myemail;
					$encData->encArray["rnameLast"] = $LName;
					$encData->encArray["rnameFirst"] = $FName;
								
					$encData = $GLOBALS['globalref'][7]->ODODecrypt($encData);
					$Myemail = $encData->decArray["emailadd"];
					$LName = $encData->decArray["rnameLast"];
					$FName = $encData->decArray["rnameFirst"];
				} else {
					$Myemail = "";
					$LName = "";
					$FName = "";
					$GLOBALS['globalref'][4]->LogEvent("DECRYPTERROR", "User info for " . $UID . " is encrypted but mcrypt is not available on server.", 3);
				}
			} 
						
			//set groups first
			$query = "SELECT RegistrationTypes.Name, RegAssignedToGroups.GID FROM RegAssignedToGroups LEFT JOIN RegistrationTypes on RegAssignedToGroups.RegID=RegistrationTypes.RegID WHERE RegAssignedToGroups.RegID=" . $MyRegID;
			$result = $GLOBALS['globalref'][1]->Query($query);
			

			while($row = mysqli_fetch_assoc($result)) {
					
				if(strlen($RegName) < 1) {
					$RegName = $row["Name"];
				}
				//check for existing group before doing an insert
				
				$query = "INSERT INTO ODOUserGID(GID,UID) values(" . $row["GID"] . "," . $MyID . ")";
				$result2 = $GLOBALS['globalref'][1]->Query($query);
				
			}

			$MyErrorFlag = false;
			if($MyRegID == 1) {
				//if regid is standard user then move QAnswer over to UID and set IsLocked to 0
				//check if RAnswer was set
				if($RAnswer != "FALSE") {
					$query = "UPDATE ODOUsers SET ResetA='" . $RAnswer . "', IsLocked=0 WHERE UID=" . $MyID;
				} else {
					$query = "UPDATE ODOUsers SET IsLocked=0 WHERE UID=" . $MyID;
				}
				$result = $GLOBALS['globalref'][1]->Query($query);
				
				if($GLOBALS['globalref'][1]->GetNumRowsAffected() < 1) {
					trigger_error("ODOCMSRegister:VerifyReg:UID not found in ODOUsers.", E_USER_WARNING);
					$MyErrorFlag=true;
				} else {

					$Comment = "UserID: " . $MyID . ":" . $MyUName . " was created by Admin Verify Registration.";
					$GLOBALS['globalref'][4]->LogEvent("USERREGISTEREDADMINV", $Comment, 1);

				}

			} else {

				$Comment = "UserID: " . $MyID . ":" . $MyUName . " was registered for " . $RegName . " by Admin Verify.";
					
				$GLOBALS['globalref'][4]->LogEvent("USERREGISTEREDADMINV", $Comment, 1);
				
			}

			//remove from AdminVerify
			if(!$MyErrorFlag) {
				$query = "DELETE FROM AdminVerify WHERE VID=" . $_SESSION["ODOSessionO"]->EscapedVars["VID"];
				$result = $GLOBALS['globalref'][1]->Query($query);
			
				$this->Content = $this->Content . "<BR><H3>We have registered user " . $MyUName . " for registration type ";

				if(strlen($RegName)>0) {
					$this->Content = $this->Content . $RegName . ".";
				} else {
					$this->Content = $this->Content . "[No Name for Reg type!].";
				}
	
				$this->Content = $this->Content . "</H3><BR></CENTER>";

				$query = "SELECT emailMsg FROM RegistrationTypes WHERE RegID=" . $MyRegID;
				$result = $GLOBALS['globalref'][1]->Query($query);
			
				$message = "";
				
					//create custom html here.
				//[[ServerName]] - server name replaced by constant
				//[[CustEmail]] - replaced with customer's e-mail
				//[[CustName]] - replaced with customer's name
				//[[CustFirstName]] - replaced with customer's first name
				//[[CustLastName]] - replaced with customer's last name
				//[[CustUserName]] - replaced with customer's username
				//[[Logo]] - replaced with img tag http://SERVERADDRESS/images/GetImage.php?Name=Logo
				//[[RegName]] - replaced with registration name for given regID
				//[[ServerAddress]] - domain name of server
				//[[LoginInfo]] - replaced with username and password
		
				$row = mysqli_fetch_assoc($result);
				
				if((!is_null($row["emailMsg"]))&&(strlen(trim($row["emailMsg"]))>0)) {
						$message = $row["emailMsg"];
										
				} else {
				
					$message = "<html>\n<head>\n<TITLE>Welcome to [[ServerName]]</TITLE>\n</head>\n<body bgcolor=\"Gray\" text=\"White\">\n[[Logo]]<br>\n<H3>Your Registration at [[ServerName]] has been accepted!</H3><Hr>\n<P>The Administrator at [[ServerName]] has accepted your request to register for [[RegName]]. To access your account please login <a href=\"http://[[ServerAddress]]/login.php\">here</a>. Or simply visit <B>http://[[ServerAddress]]/login.php</B> .</P>\n\n</body>\n</html>";
					
				}
				
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["emailuser"])){
					
					//e-mail the user of their new access
					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
					$headers .= 'From: ' . REGREPLYEMAIL . "\n";
					$headers .= 'Reply-To: ' . REGREPLYEMAIL . "\n";
					$headers .= 'X-Mailer: ODOWeb' . "\n";

					$message = str_replace("[[ServerName]]", SERVERNAME, $message);
					$message = str_replace("[[CustEmail]]", $Myemail, $message);
					$message = str_replace("[[CustName]]", $FullName, $message);
					$message = str_replace("[[CustFirstName]]", $FName, $message);
					$message = str_replace("[[CustLastName]]", $LName, $message);
					$message = str_replace("[[CustUserName]]", $MyUName, $message);
					$LogoURL = "<img src=\"http://" . SERVERADDRESS . "/" . GETIMAGEURLPATH . "?Name=Logo\">";
					$message = str_replace("[[Logo]]", $LogoURL, $message);
					$message = str_replace("[[RegName]]", $RegName, $message);
					$message = str_replace("[[ServerAddress]]", SERVERADDRESS, $message);
				
					if(!mail($Myemail, "Registered at " . SERVERNAME . ".", $message, $headers)) {
						trigger_error("ODOWEBERROR:ODOCMSRegister:VerifyReg:Could not e-mail user:" . $MyID, E_USER_WARNING);
						$this->Content = $this->Content . "<BR><h3>We had an error trying to e-mail the user the update of their Registration status.</H3></center>\n";
					}

				
				}
				

			} else {

				$this->Content = $this->Content . "<BR><H3>We had an error registering the user. Some groups may have been updated for user " . $MyUName . " but there was an error trying to find the userid after adding these groups. We should never get this error, and yet we have.!?</H3></center>";

			}

		} else {

			$query = "SELECT AdminVerify.VID, ODOUsers.UID, ODOUsers.User, ODOUsers.EncIV, ODOUsers.IsEncrypted, ODOUsers.rnameFirst, ODOUsers.rnameLast, ODOUsers.emailadd, RegistrationTypes.Name FROM RegistrationTypes, AdminVerify, ODOUsers WHERE AdminVerify.VID=" . $_SESSION["ODOSessionO"]->EscapedVars["VID"] . " AND AdminVerify.RegID=RegistrationTypes.RegID AND AdminVerify.UID=ODOUsers.UID";

			$result = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($result) < 1) {
				$this->Content = $this->Content . "<BR><H3><B>Error: You must call the Verify Registration System with a valid verification specified! VID value passed was not found.</B></H3></center>";
				//no error reporting as this should only happen in the case of a user playing with post/get vars.
				return;
			}

			$row = mysqli_fetch_assoc($result);
			
			$Myemail = $row["emailadd"];
			
			$LName = "";
			if((!is_null($row["rnameLast"]))||(strlen(trim($row["rnameLast"])) > 0)) {
				$LName = $row["rnameLast"];
			} 
			
			$FName = "";
			if((!is_null($row["rnameFirst"]))||(strlen(trim($row["rnameFirst"])) > 0)) {
				$FName = $row["rnameFirst"];
			} 
			
			if($row["IsEncrypted"] == 1) {
				if($GLOBALS['globalref'][7]->isMcryptAvailable()) {
					$encData = new EncryptedData();
					$encData->iv = $row["EncIV"];
					$encData->encArray["emailadd"] = $Myemail;
					$encData->encArray["rnameLast"] = $LName;
					$encData->encArray["rnameFirst"] = $FName;
								
					$encData = $GLOBALS['globalref'][7]->ODODecrypt($encData);
					$Myemail = $encData->decArray["emailadd"];
					$LName = $encData->decArray["rnameLast"];
					$FName = $encData->decArray["rnameFirst"];
				} else {
					$Myemail = "MCrypt not enabled";
					$LName = "MCrypt not enabled";
					$FName = "";
					$GLOBALS['globalref'][4]->LogEvent("DECRYPTERROR", "User info for " . $UID . " is encrypted but mcrypt is not available on server.", 3);
				}
			} 
			
				//show all register requests for this user. accept all at once and e-mail once for group.
			$this->Content = $this->Content . "<BR><H3><B>Are you sure you want to accept this request?</B></H3><BR>";
			
			$this->Content .= "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\">";
			
			$this->Content .= "<table border=0><TR><TD>User Name:</TD><TD>" . $row["User"] . "</TD></TR><TR><TD>Name:</TD><TD>" . $LName . "," . $FName . "</TD></TR><TR><TD>Email:</TD><TD>" . $Myemail . "</TD></TR><TR><TD>Register Type:</TD><TD>" . $row["Name"] . "</TD></TR>";
			
			$this->Content .= "<TR><TD>E-mail user:</TD><TD><input type=\"checkbox\" name=\"emailuser\" value=\"true\"></TD></TR>";
			
			$this->Content .= "<TR><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"VID\" value=\"" . $row["VID"] . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"VerifyReg\"><input type=\"hidden\" name=\"VERIFYOK\" value=\"VERIFYOK\"><TD><input type=\"submit\" name=\"Accept\" value=\"Accept\"></td></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"ListPending\"><TD><input type=\"submit\" name=\"Back To List\" value=\"Back To List\"></td></form></TR></Table></center>";
			
		}
	}

	
	function DeleteRegRequest() {
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSRegister");



		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["VID"])) {
			$this->Content = $this->Content . "<BR><H3><B>Error: You must call the Verify Registration System with a valid verification specified!</B></H3></center>";
			//no error reporting done as this is likely just someone playing with the code
			return;
		} 

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["DELETEOK"])) {
			$query = "SELECT AdminVerify.UID, AdminVerify.RegID, ODOUsers.User FROM AdminVerify LEFT JOIN ODOUsers on AdminVerify.UID=ODOUsers.UID WHERE AdminVerify.VID=" . $_SESSION["ODOSessionO"]->EscapedVars["VID"];
			
			$result = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($result) < 1) {
				$this->Content = $this->Content . "<BR><H3><B>Error: You must call the Verify Registration System with a valid verification specified!</B></H3></center>";
				//no error reporting done as this is likely just someone playing with the code
				return;
			}

			$row = mysqli_fetch_assoc($result);

			$MyRegID = $row["RegID"];
			$MyUID = $row["UID"];
			$MyUserName = "";
			if(!is_null($row["User"])) {
				$MyUserName = $row["User"];
			}

			//now delete Verify
			$query = "DELETE FROM AdminVerify WHERE VID=" . $_SESSION["ODOSessionO"]->EscapedVars["VID"];
			$result = $GLOBALS['globalref'][1]->Query($query);

			if($MyRegID == 1) {
				//Standard User Reg so delete User. 
				$this->Content = $this->Content . "<BR><H3><B>We have removed the Registration Request. This was a Standard User Registration. ";

				if(strlen($MyUserName) < 1) {
					$this->Content = $this->Content . "We could not find a valid username listed in the User table. The User may have already been deleted.</B></H3></center>"; 

				} else {

					$this->Content = $this->Content . "Please click the Delete User button below to remove the user fromt he system as well.</B></H3><BR><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSUsersO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteUser\"><input type=\"hidden\" name=\"userid\" value=\"" . $MyUID . "\"><input type=\"hidden\" name=\"user\" value=\"" . $MyUserName . "\"><input type=\"hidden\" name=\"DeleteMe\" value=\"true\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></form><br></center>";

				}
			} else {
	
				$this->Content = $this->Content . "<BR><H3><B>We have removed the Registration Request.</B></H3></center>";

			}

		} else {

			$query = "SELECT AdminVerify.VID, ODOUsers.UID, ODOUsers.User, ODOUsers.rnameFirst, ODOUsers.rnameLast, ODOUsers.emailadd, RegistrationTypes.Name FROM RegistrationTypes, AdminVerify, ODOUsers WHERE AdminVerify.VID=" . $_SESSION["ODOSessionO"]->EscapedVars["VID"] . " AND AdminVerify.RegID=RegistrationTypes.RegID AND AdminVerify.UID=ODOUsers.UID";

			$result = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($result) < 1) {
				$this->Content = $this->Content . "<BR><H3><B>Error: You must call the Verify Registration System with a valid verification specified! VID value passed was not found.</B></H3></center>";
				//no error reporting as this should only happen in the case of a user playing with post/get vars.
				return;
			}

			$row = mysqli_fetch_assoc($result);

			$this->Content = $this->Content . "<BR><H3><B>Are you sure you want to deny this request?</B></H3><BR><table border=0><TR><TD>User Name:</TD><TD>" . $row["User"] . "</TD></TR><TR><TD>Name:</TD><TD>" . $row["rnameLast"] . "," . $row["rnameFirst"] . "</TD></TR><TR><TD>Register Type:</TD><TD>" . $row["Name"] . "</TD></TR><TR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"VID\" value=\"" . $row["VID"] . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteRegRequest\"><input type=\"hidden\" name=\"DELETEOK\" value=\"DELETEOK\"><TD><input type=\"submit\" name=\"Deny\" value=\"Deny\"></td></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"ListPending\"><TD><input type=\"submit\" name=\"Back To List\" value=\"Back To List\"></td></form></TR></Table></center>";

		}
	}


	function ListPending() {

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSRegister");
		
		$query = "SELECT AdminVerify.VID, ODOUsers.UID, ODOUsers.User, ODOUsers.IsEncrypted, ODOUsers.EncIV, ODOUsers.rnameFirst, ODOUsers.rnameLast, ODOUsers.emailadd, RegistrationTypes.Name FROM RegistrationTypes, AdminVerify, ODOUsers WHERE RegistrationTypes.RegID=AdminVerify.RegID AND AdminVerify.UID=ODOUsers.UID";

		$result = $GLOBALS['globalref'][1]->Query($query);

		$RecordCount = mysqli_num_rows($result);
		
		$NumofPages = $RecordCount / 30;
		$Count = 0;

		if($RecordCount == 0) {
			$this->Content = $this->Content . "<BR><h3>There are no pending requests.</h3><br><br></center>";
			return;
		} elseif ($RecordCount % 30 > 0) {
			$NumofPages = $NumofPages + 1;
		}

		if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
			$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
		} else {
			$this->Content = $this->Content . "<br><B><H3>Registrations to Verify</H3></B><br><table border=1><TR><th>UserName</TH><TH>LastName,FirstName</TH><TH>E-mail Address</TH><TH>Registration Type</TH><TH>Allow</TH><TH>Deny</TH></TR>";

			if($RecordCount < 1) {
				$this->Content = $this->Content . "\n<TR><TD><center><B>THERE ARE NO REQUESTS AT THIS TIME.</B></center></TD></TR>";
			} else {
				$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 30;
				mysqli_data_seek($result, $ChangeToRow);
				while(($Count < 30) && ($row = mysqli_fetch_assoc($result))) {
					
					$LName = "";
					if((!is_null($row["rnameLast"]))||(strlen(trim($row["rnameLast"])) > 0)) {
						$LName = $row["rnameLast"];
					} 
			
					$FName = "";
					if((!is_null($row["rnameFirst"]))||(strlen(trim($row["rnameFirst"])) > 0)) {
						$FName = $row["rnameFirst"];
					} 
	
					$email = $row["emailadd"];
					
					if($row["IsEncrypted"] == 1) {
						
						if($GLOBALS['globalref'][7]->isMcryptAvailable()) {
							$encData = new EncryptedData();
							$encData->iv = $row["EncIV"];
							$encData->encArray["emailadd"] = $email;
							$encData->encArray["rnameLast"] = $LName;
							$encData->encArray["rnameFirst"] = $FName;
								
							$encData = $GLOBALS['globalref'][7]->ODODecrypt($encData);
							$email = $encData->decArray["emailadd"];
							$LName = $encData->decArray["rnameLast"];
							$FName = $encData->decArray["rnameFirst"];
						} else {
							$email = "";
							$LName = "";
							$FName = "";
							$GLOBALS['globalref'][4]->LogEvent("DECRYPTERROR", "User info for " . $UID . " is encrypted but mcrypt is not available on server.", 3);
						}
						
					} 
			
					$this->Content = $this->Content . "\n<TR><TD><a href=\"index.php?pg=ODOCMS&ob=ODOUserO&fn=EditUser&userid=" . $row["UID"] . "\">" . $row["User"] . "</a></TD><TD>" . $LName . "," . $FName . "</TD><TD>" . $email . "</TD><TD>" . $row["Name"] . "</TD><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"VID\" value=\"" . $row["VID"] . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"VerifyReg\"><TD><input type=\"submit\" name=\"Accept\" value=\"Accept\"></td></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"VID\" value=\"" . $row["VID"] . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteRegRequest\"><TD><input type=\"submit\" name=\"Deny\" value=\"Deny\"></TD></form></TR>";
		

				}

				
				$this->Content = $this->Content . "</Table>";

				$CurPage = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"];
		
				//we want to count down the Pages
				$NumberofLinks = 1;
				//$i is the current position
				$i = $CurPage - 4;
				if($i < 1) {
					$i = 1;
				}

				if(($CurPage > 5)) {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=ListPending&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=ListPending&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

				if($i > $NumofPages) {
					$this->Content = $this->Content . "&nbsp;Next->";
				} else {
					$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=ListPending&PageNum=" . $i . "\">Next-></a>";
				}

				$this->Content = $this->Content . "</center>";

			}

			
		}

	}

     function DeleteReg() {

	}

	function EditReg() {

	}

	function CreateReg() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSRegister");


		if(isset($_SESSION["ODOSessionO"]->EscapedVars["NEWFLAG"])) {


		} else {

			$this->Content = $this->Content . "<br><B><H3>Create a new Registration Type</H3></B><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"CreateReg\"><input type=\"hidden\" name=\"NEWFLAG\" value=\"NEWFLAG\"><table border=1><TR><TD>Name:</TD><TD><input type=\"text\" name=\"RegName\" maxlength=\"254\" size=\"100\"></TD></TR><TR><TD>After Registration<BR>Complete URL:</TD><TD><input type=\"text\" name=\"RegCompleteURL\" value=\"http:\\\\\" maxlength=\"254\"></TD></TR><TR><TD>Allow Guest To Use?</TD><TD><SELECT name=\"Guest\"><option value=\"TRUE\">Yes</option><option value=\"FALSE\" SELECTED>No</option></SELECT></TD></TR><TR><TD>Admin Nees to Verify<br>Registration?</TD><TD><option value=\"TRUE\" SELECTED>Yes</option><option value=\"FALSE\">No</option></SELECT></TD></TR><TR><TD>User Form Data:<br><sub>(All your form data will be placed inside 1 table row with 2 columns)</sub></TD><TD>";

			

		}
		

	}

	function ViewRegTypes() {

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
		}

		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSRegister");



		$query = "SELECT * FROM RegistrationTypes";
		$result = $GLOBALS['globalref'][1]->Query($query);

		$RecordCount = mysqli_num_rows($result);
		$NumofPages = $RecordCount / 30;
		$Count = 0;

		if( ($RecordCount % 30 > 0) ) {
			$NumofPages = $NumofPages + 1;
		}

		if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
			$this->Content = $this->Content . "<BR><BR><h3>Page number is out of bounds!</h3><br><br></center>";
		} else {

			$this->Content = $this->Content . "<br><BR>Note: For this version of ODOWeb only 1 registration type is available.<BR><BR><B><H3>Registration Types</H3></B><br><table border=1><TR><th>ID</TH><TH>Name</TH><TH>Is Public</TH><TH>Edit</TH><TH>Delete</TH></TR>";

			if($RecordCount < 1) {
				$this->Content = $this->Content . "\n<TR><TD><center><B>THERE ARE NO REQUESTS AT THIS TIME.</B></center></TD></TR></table></center>";
			} else {
				$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 30;
				mysqli_data_seek($result, $ChangeToRow);
				while(($Count < 30) && ($row = mysqli_fetch_assoc($result))) {
					$this->Content = $this->Content . "\n<TR><TD>" . $row["RegID"] . "</TD><TD>" . $row["Name"] . "</TD><TD>";

					if($row["AllowGuest"] == 1) {
						$this->Content = $this->Content . "Yes";
					} else {
						$this->Content = $this->Content . "No";
					}

					$this->Content = $this->Content . "</TD><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"RegID\" value=\"" . $row["RegID"] . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"EditReg\"><TD><input type=\"submit\" name=\"Edit\" value=\"Edit\" disabled=\"true\"></td></form><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"RegID\" value=\"" . $row["RegID"] . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"DeleteReg\"><TD><input type=\"submit\" name=\"Delete\" value=\"Delete\" disabled=\"true\"></TD></form></TR>";
		

				}

				$this->Content = $this->Content . "</Table>";

				$CurPage = $_SESSION["ODOSessionO"]->EscapedVars["PageNum"];
		
				//we want to count down the Pages
				$NumberofLinks = 1;
				//$i is the current position
				$i = $CurPage - 4;
				if($i < 1) {
					$i = 1;
				}

				if(($CurPage > 5)) {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=ViewRegTypes&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=ViewRegTypes&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

				if($i > $NumofPages) {
					$this->Content = $this->Content . "&nbsp;Next->";
				} else {
					$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSRegisterO&fn=ViewRegTypes&PageNum=" . $i . "\">Next-></a>";
				}

				$this->Content = $this->Content . "</center>";

			}


		}
		
	}


	function EditEmail() {
		
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSRegister");


		if(isset($_SESSION["ODOSessionO"]->EscapedVars["Update"])) {
			
			$query = "UPDATE RegistrationTypes SET emailMsg='" . $_SESSION["ODOSessionO"]->EscapedVars["emailMsg"] . "' WHERE RegID=1";
			$result = $GLOBALS['globalref'][1]->Query($query);
		
			if($GLOBALS['globalref'][1]->GetNumRowsAffected()>0) {
				$this->Content = $this->Content . "<BR><BR><H3>Update complete.</H3></center>";
			} else {
				$this->Content = $this->Content . "<BR><BR><H3>We had an error updating the e-mail message.</H3></center>";
			}
			
		} else {
			
			//on first release we only have 1 registration type
			$query = "SELECT emailMsg FROM RegistrationTypes WHERE RegID=1";
			$result = $GLOBALS['globalref'][1]->Query($query);
			
			if(mysqli_num_rows($result)>0)
			{
				$row = mysqli_fetch_assoc($result);
	
				if((!is_null($row["emailMsg"]))&&(strlen($row["emailMsg"]) > 0)) {
					$message = $row["emailMsg"];
				} else {
					$message = "";
				}
				
				$tagsDesc = "<table><TR><TD><UL><LI>[[ServerName]] - server name replaced by constant</LI>
				<LI>[[CustEmail]] - replaced with customer's e-mail</LI>
				<LI>[[CustName]] - replaced with customer's name</LI>
				<LI>[[CustFirstName]] - replaced with customer's first name</LI>
				<LI>[[CustLastName]] - replaced with customer's last name</LI>
				<LI>[[CustUserName]] - replaced with customer's username</LI>
				<LI>[[Logo]] - replaced with img tag http://SERVERADDRESS/images/GetImage.php?Name=Logo</LI>
				<LI>[[RegName]] - replaced with registration name for given regID</LI>
				<LI>[[ServerAddress]] - domain name of server</LI>
				<LI>[[LoginInfo]] - replaced with username and password</LI></UL></TD></TR></TABLE>";
				
				$this->Content = $this->Content . "<BR><BR><B>The following tags are replaced with the corresponding content.</B><BR>" . $tagsDesc . "<BR><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSRegisterO\"><input type=\"hidden\" name=\"fn\" value=\"EditEmail\"><input type=\"hidden\" name=\"UPDATE\" value=\"UPDATE\"><TEXTAREA name=\"emailMsg\" COLS=75 ROWS=20>" . $message . "</TEXTAREA><BR><BR><input type=\"submit\" name=\"Update\" value=\"Update\"></form></center>";
				
			} else {
				
				$this->Content = $this->Content . "<BR><BR><H3>The default registration type is not setup. You will need to recover the table RegistrationTypes from the original sql install.</H3><BR></center>";
				
			}
			
		}
				
	}
		
//begin footer
	function __wakeup() {
		$this->Content = "";
		
	}

}


?>