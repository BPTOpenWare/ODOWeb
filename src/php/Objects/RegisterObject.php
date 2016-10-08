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

class ODORegister {
	
	var $OKCaptcha;
	var $UNameok;
	var $emailok;
	var $RQestionOK;
	var $RAnswerOK;
	var $Content;
	var $MissingText;
	var $MyUID;
	var $ApplicationFields; //used in later versions
	var $ODOStore; //to decide what complete URL we use
	
	function __construct() {

		$this->OKCaptcha = true;
		$this->UNameok = true;
		$this->emailok = true;
		$this->RQuestionOK = true;
		$this->RAnswerOK = true;
		$this->MyUID = 0;
		$this->Content = "";
		$this->MissingText = "";
		$this->ApplicationFields = array();
		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["ODOStore"])) {
			$this->ODOStore = true;
		} else {
			$this->ODOStore = false;
		}
		
	}


	private function randomstring($len)
	{
		$chars = 'aZb1cAdQKeYB2JfhjC8k9mX3DLnToRME0rNsF4tuUPvG5wHVx6yS7z';
		$str = "";
		$i = 0;
		date_default_timezone_set('UTC');
    		
    		while($i<$len)
     		{
			//0-55
        		$str.=$chars[mt_rand(0,53)];
        		$i++;
    		}
 		return $str;
	}


	private function UserRegisterCompleted($RegID, $UID) {
		$query = "INSERT INTO RegisteredUsers(RegID, UID) values(" . $RegID . "," . $UID . ")";
		$result = $GLOBALS['globalref'][1]->Query($query);
		
		if(($GLOBALS['globalref'][1]->GetNumRowsAffected())>0) {
			return true;
		} else {
			return false;
		}
	
	}
	
	public function CheckUserIsRegisteredForRegID($RegID, $UID) {
		
		$query = "SELECT * FROM RegisteredUsers WHERE UID=" . $UID . " AND RegID=" . $RegID;
		$result = $GLOBALS['globalref'][1]->Query($query);
		
			if(mysqli_num_rows($result) > 0) {
				return true;
			} else {
				return false;
			}
		
	}
	
	public function GetCompleteURLForReg($RegID) {
		$query = "SELECT AfterRegCompleteURL, ODOStoreAfterRegURL FROM RegistrationTypes WHERE RegID=" . $RegID;
		$result = $GLOBALS['globalref'][1]->Query($query);
		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
		
			if($this->ODOStore) {
				
				if((!is_null($row["ODOStoreAfterRegURL"]))&&(strlen($row["ODOStoreAfterRegURL"])>0)) {
					return $row["ODOStoreAfterRegURL"];
				} else {
					return DEFAULTODOSTOREREGCOMPLETEURL;
				}
				
			} else {
				if((!is_null($row["AfterRegCompleteURL"]))&&(strlen($row["AfterRegCompleteURL"])>0)) {
					return $row["AfterRegCompleteURL"];
				} else {
					return DEFAULTREGCOMPLETEURL;
				}
			}

		} else {
			if($this->ODOStore) {
				return DEFAULTODOSTOREREGCOMPLETEURL;
			} else {
				return DEFAULTREGCOMPLETEURL;
			}
		}
	}



//end header

	
	private function RunStandardUserChecks() {
		
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["chanswer"])) {
			$this->MissingText = "You need to enter the challenge fields on the form! Please hit your browser's back button and try again.";
			return false;
			//if spammer then don't bother checking the rest
		} else {

			$this->OKCaptcha = false;

			$query = "SELECT * FROM NoSpam WHERE SessionID='" . session_id() . "'";
			$result = $GLOBALS['globalref'][1]->Query($query);
		
			if(mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				if($row["txtSpam"] == $_SESSION["ODOSessionO"]->EscapedVars["chanswer"]) {
					$this->OKCaptcha = true;
				} else {
					$this->OKCaptcha = false;
					$this->MissingText = "You have entered the wrong CAPTCHA text. Please enter the correct letters in the box at the bottom of this page. ";
					return false;
				}
			}

		}

		$ErrorWithData = false;
		//continue checking
		//check if user is already setup
		//if user is not set then register for standard user
		if((!($_SESSION["ODOUserO"]->getLoggedIn())) || ($_SESSION["ODOUserO"]->getIsGuest())) {
			
			//first check to see if we allow standard users (always RegID=1)
			
			//check UName
			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["UName"])) {
				$ErrorWithData = true;
				$this->MissingText = $this->MissingText . "You have not entered a username! ";
			}

			if((strlen($_SESSION["ODOSessionO"]->EscapedVars["UName"]) > 20) || (strlen($_SESSION["ODOSessionO"]->EscapedVars["UName"]) < 3)) {
				$ErrorWithData = true;
				$this->MissingText = $this->MissingText . "Your username is not between 3 and 20 characters! ";
			}

			$query = "SELECT User FROM ODOUsers WHERE User='" . $_SESSION["ODOSessionO"]->EscapedVars["UName"] . "'";
			$result = $GLOBALS['globalref'][1]->Query($query);
			
			if(mysqli_num_rows($result) > 0) {
				$ErrorWithData = true;
				$this->MissingText = $this->MissingText . "The username you selected is already chosen. Please select another. ";
			}

			
			//check e-mail address
			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["email"])) {

				$ErrorWithData = true;
				$this->MissingText = $this->MissingText . "You are missing the e-mail address! ";

			} else {

				$trans = array("\n" => "newline", "\r" => "newline", ":" => "COLON");
				$from = strtr($_SESSION["ODOSessionO"]->EscapedVars["email"], $trans);
				if(!(preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$from))) {
					$ErrorWithData = true;
					$this->MissingText = $this->MissingText . "There is an error with your e-mail address. Please check the format. ";
				} else {
					$query = "SELECT UID FROM ODOUsers WHERE emailadd='" . $from . "'";
					$result = $GLOBALS['globalref'][1]->Query($query);

					if(mysqli_num_rows($result) > 0) {
						$ErrorWithData = true;
						$this->MissingText = $this->MissingText . "This e-mail address already exists. Please choose another to register with or login to your account. ";

					} 

				}
			}

			
			//check for name
			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["FName"])) {
				$ErrorWithData = true;
				$this->MissingText = $this->MissingText . "Please enter your first name. ";

			} else {

				if((strlen($_SESSION["ODOSessionO"]->EscapedVars["FName"]) < 1) || (strlen($_SESSION["ODOSessionO"]->EscapedVars["FName"] > 100))) {
					$ErrorWithData = true;
					$this->MissingText = $this->MissingText . "Your first name is not between 1 and 100 characters. ";
				}

			}

			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["LName"])) {
				$ErrorWithData = true;
				$this->MissingText = $this->MissingText . "Please enter your last name. ";

			} else {

				if((strlen($_SESSION["ODOSessionO"]->EscapedVars["LName"]) < 1) || (strlen($_SESSION["ODOSessionO"]->EscapedVars["LName"] > 100))) {
					$ErrorWithData = true;
					$this->MissingText = $this->MissingText . "Your last name is not between 1 and 100 characters. ";
				}

			}


			//check Q and A for Password reset
			if(REQUIREQARESETONREGISTRATION) {
				if((!isset($_SESSION["ODOSessionO"]->EscapedVars["RQuestion"])) || (!isset($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"]))) {
					$ErrorWithData = true;
					$this->MissingText = $this->MissingText . "You are missing either the Reset Question or Answer. ";
				}
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["RQuestion"])) {
				if(!is_numeric($_SESSION["ODOSessionO"]->EscapedVars["RQuestion"])) {
					$ErrorWithData = true;
					$this->MissingText = $this->MissingText . "Your Reset Question is not between 3 and 100 characters. ";
				} elseif((REQUIREQARESETONREGISTRATION)&&($_SESSION["ODOSessionO"]->EscapedVars["RQuestion"]==0)) {
					$ErrorWithData = true;
					$this->MissingText = $this->MissingText . "Your must select a Reset Question. ";
			
				}
			}

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"])) {
				if((strlen($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"]) < 3) || (strlen($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"]) > 100)) {
					$ErrorWithData = true;
					$this->MissingText = $this->MissingText . "Your Reset Answer is not between 3 and 100 characters. ";
				}
			}

			if($ErrorWithData) {
				return false;
			} else {
				return true;
			}

		} else {
			//standard user is already setup. Just return
			return true;
		}

	}


	function ProcRegisterPage() {

		$ErrorWithData = false;
		$NeedVerify = false;

		//**********************************************************
		//Start Validation
		//**********************************************************
		
		$MyID = 0; //check if logged in as standard user and set ID
		if(($_SESSION["ODOUserO"]->getLoggedIn()) && (!($_SESSION["ODOUserO"]->getIsGuest()))) {
			$MyID = $_SESSION["ODOUserO"]->getUID();
			$this->MyUID = $_SESSION["ODOUserO"]->getUID();
		} else {
			//if we are not logged in then make check for standard registration user checks
			//only make check if we are allowed to do standard user registration
			if(ALLOWGUESTSTANDARDREG) {
				if(!($this->RunStandardUserChecks())) {
					$ErrorWithData = true;
				}
			} else {
				$this->MissingText = "This site does not allow Standard User Registration. You must be logged in to Register for features of this site. Please contact the system Administrator"; //we can not continue so exit
				return false;
			}
			
		}
		

		//add check for TOS
		if(TOSONREGISTER) {
			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["TOS"])) {
				$this->MissingText = $this->MissingText . " You must accept the Terms of Service to continue. ";
				$ErrorWithData = true;
			}
			
		}
		

		//run check for RegID
		$RegName = "";
		if((isset($_SESSION["ODOSessionO"]->EscapedVars["RegID"])) && ($_SESSION["ODOSessionO"]->EscapedVars["RegID"]!=1)) {
			$query = "SELECT Name, DataCheckCode, AllowGuest FROM RegistrationTypes WHERE RegID=" . $_SESSION["ODOSessionO"]->EscapedVars["RegID"];
			
			if(($_SESSION["ODOUserO"]->getLoggedIn()) && (!($_SESSION["ODOUserO"]->getIsGuest()))) {
			 	$query = $query . " AND AllowGuest=1";
			} 

			$result = $GLOBALS['globalref'][1]->Query($query);

			
			if(mysqli_num_rows($result) < 1) {
				//invalid RegID type, just exit
				$this->MissingText = $this->MissingText . "An invalid Registration type was passed. ";
				return false;
			} else {
				$row = mysqli_fetch_assoc($result);
				$RegName = $row["Name"];

				$MyFunc = $row["DataCheckCode"];
				if(is_callable(array(&$this, $MyFunc))) {
					$RValue = call_user_func(array(&$this, $MyFunc));
					if($RValue === "ERROR") {
						$ErrorWithData = true;
						return false;
					}
				} else {
					trigger_error("REGISTER ERROR:We could not call the function name " . $row["DataCheckCode"], E_USER_ERROR);
					exit();
				}
			}
		}

		//*****************************************************************
		//END Check of validation
		//*****************************************************************
		
		//*****************************************************************
		//*****************************************************************
		//Start Registration
		//*****************************************************************
		//*****************************************************************
		
		//*****************************************************************
		//Start Standard User Registration
		//*****************************************************************
		//if we don't already have a standard user then we need to create one.
        
        //start transaction
        if(!$ErrorWithData) {
            $GLOBALS['globalref'][1]->startTransaction();
        }
    
		if((!$ErrorWithData) && (ALLOWGUESTSTANDARDREG) && ((!$_SESSION["ODOUserO"]->getLoggedIn()) || ($_SESSION["ODOUserO"]->getIsGuest()))) {
			
			//we've already made all our checks so just insert
			
			$query = "INSERT INTO ODOUsers(User,Pistemp,IsLocked,rnameFirst,rnameLast,ResetQID,ResetA,emailadd,FailedLogins, IsEncrypted, HashedMode, EncIV) values('" . htmlentities($_SESSION["ODOSessionO"]->EscapedVars["UName"]) . "',1,";

			//Create EncryptedData container
			$encData = new EncryptedData();
			
			//temp vars needed
			//Setup DB vars based on encryption status
			$RegName = "";
			$encData->decArray["emailadd"] = htmlentities($_SESSION["ODOSessionO"]->EscapedVars["email"]);
			
			$LName = "";
		
			if((!is_null($_SESSION["ODOSessionO"]->EscapedVars["LName"]))||(strlen(trim($_SESSION["ODOSessionO"]->EscapedVars["LName"])) > 0)) {
				$encData->decArray["rnameLast"] = htmlentities($_SESSION["ODOSessionO"]->EscapedVars["LName"]);
				
			} 
			
			$FName = "";
			
			if((!is_null($_SESSION["ODOSessionO"]->EscapedVars["FName"]))||(strlen(trim($_SESSION["ODOSessionO"]->EscapedVars["FName"])) > 0)) {
				$encData->decArray["rnameFirst"] = htmlentities($_SESSION["ODOSessionO"]->EscapedVars["FName"]);
			} 
			
			
			$FullName = $encData->decArray["rnameFirst"] . " " . $encData->decArray["rnameLast"];
		
			//run encryption
			$encData = $_SESSION["ODOUtil"]->ODOEncrypt($encData);
			
			//Lock the account if we have to have an admin verify it
			if(ADMINVERIFYSTANDARDUSERREG) {
				$NeedVerify = true;
				$query = $query . "1,";
			} else {
				$query = $query . "0,";
			}
	
			//Add First name and last name encrypted values
			if(isset($encData->encArray["rnameFirst"])) {
				$query = $query . "'" . $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["rnameFirst"]) . "',";
			} else {
				$query = $query . "NULL,";
			}
			
			if(isset($encData->encArray["rnameLast"])) {
				$query = $query . "'" . $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["rnameLast"]) . "',";
			} else {
				$query = $query . "NULL,";
			}
			 

			//confirm and set reset Q ID
			$QID = 0;
			$questionquery = "SELECT * FROM ResetQuestions WHERE ResetQID=" . $_SESSION["ODOSessionO"]->EscapedVars["RQuestion"];
			$questionresult = $GLOBALS['globalref'][1]->Query($questionquery);

			if(mysqli_num_rows($questionresult)>0) {
				$QID = $_SESSION["ODOSessionO"]->EscapedVars["RQuestion"];
			}
			
			$query = $query . $QID . ",";
			
			//if an Admin needs to verify standard user regs then do not set the reset answer
			if(ADMINVERIFYSTANDARDUSERREG) {

				$query = $query . "NULL";

			} else {
				//if answer was set then go ahead and hash it and set to the database
				if((isset($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"]))&&(strlen($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"])>0)) {
					$query = $query . "'" . $_SESSION["ODOUserO"]->ODOHash(htmlentities($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"]), $_SESSION["ODOUserO"]->GetHashMode()) . "'";
				} else {
					$query = $query . "NULL";
				}

			}

			//set encrypted e-mail db
			
			$query = $query . ",'" . $GLOBALS['globalref'][1]->EscapeMe($encData->encArray["emailadd"]) . "',0,";
			
			//set encrypted mode
			if($_SESSION["ODOUtil"]->isMcryptAvailable()) {
				$query = $query . "1,";
			} else {
				$query = $query . "0,";
			}
			
			//set hashed mode
			$query = $query . $_SESSION["ODOUserO"]->GetHashMode() . ",";
			
			if(strlen($encData->iv)>0) {
				$query = $query . "'" . $GLOBALS['globalref'][1]->EscapeMe($encData->iv) . "')";
			} else {
				$query = $query . "NULL)";
			}
			
			$result = $GLOBALS['globalref'][1]->Query($query);

			$MyID = 0;
			$MyID = $GLOBALS['globalref'][1]->LastInsertID();
			$this->MyUID = $MyID;
			
			$_SESSION["ODOUserO"]->setGENUID($MyID); // set for ODOStore
			
			if($MyID == 0) {
				trigger_error("RegisterObject:ProcRegisterPage:Error getting UID for new user!", E_USER_ERROR);
				return false;
				//not recoverable error
			}

			$Comment = "UserID: " . $MyID . ":" . $_SESSION["ODOSessionO"]->EscapedVars["UName"] . " was created by public registration.";
			$GLOBALS['globalref'][4]->LogEvent("USERCREATEDPUB", $Comment, 1);
			
			//get UID and set to AdminVerify entry if required
			if(ADMINVERIFYSTANDARDUSERREG) {
				

				//standard user is always 1 for regid
				$query = "INSERT INTO AdminVerify(RegID, UID, RAnswer) values(1," . $MyID . ",";
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"])) {
					$query = $query . "'" . $_SESSION["ODOUserO"]->ODOHash(htmlentities($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"]), $_SESSION["ODOUserO"]->GetHashMode()) . "')";
				} else {
					$query = $query . "NULL)";
				}
			
			
				$result = $GLOBALS['globalref'][1]->Query($query);
				$MyVID = 0;
				$MyVID = $GLOBALS['globalref'][1]->LastInsertID();
				if($MyVID == 0) {
					trigger_error("RegisterObject:ProcRegisterPage:Error getting VID for new user!", E_USER_ERROR);
					return false;
					//not recoverable error
				}

			} else {
				//else set groups now
				$query = "SELECT * FROM RegAssignedToGroups WHERE RegID=1";
				$result = $GLOBALS['globalref'][1]->Query($query);
				
				while($row = mysqli_fetch_assoc($result)) {
					
					$query = "INSERT INTO ODOUserGID(GID,UID) values(" . $row["GID"] . "," . $MyID . ")";
					$result2 = $GLOBALS['globalref'][1]->Query($query);
				
				}

			}


			//now create password
			$newpass = $this->randomstring(10);
			
			
			$query = "SELECT Name, emailMsg FROM RegistrationTypes WHERE RegID=1";
			$result = $GLOBALS['globalref'][1]->Query($query);
			
			$message = "";
			$message = "<html>\n<head>\n<TITLE>Welcome to [[ServerName]]</TITLE>\n</head>\n<body bgcolor=\"Gray\" text=\"White\">\n[[Logo]]<br>\n<H3>Your Registration at [[ServerName]] has been accepted!</H3><Hr>\n<P>The Administrator at [[ServerName]] has accepted your request to register for [[RegName]]. To access your account please login <a href=\"http://[[ServerAddress]]/login.php\">here</a>. Or simply visit <B>http://[[ServerAddress]]/login.php</B> .</P>\n\n[[LoginInfo]]<BR><BR><B>Thank You!</B><br>\n[[ServerName]]\n</body>\n</html></body>\n</html>";
					
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
		
				if(mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_assoc($result);
					
					$RegName = $row["Name"];
					
					if((!is_null($row["emailMsg"]))&&(strlen(trim($row["emailMsg"]))>0)) {
						$message = $row["emailMsg"];
					}
					
				} 
				
				//e-mail the user of their new access
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
                $headers .= 'From: ' . REGREPLYEMAIL . "\n";
				$headers .= 'Reply-To:  ' . REGREPLYEMAIL . "\n";
				$headers .= 'X-Mailer: ODOWeb' . "\n";

				$message = str_replace("[[ServerName]]", SERVERNAME, $message);
				$message = str_replace("[[CustEmail]]", $encData->decArray["emailadd"], $message);
				$message = str_replace("[[CustName]]", $FullName, $message);
				$message = str_replace("[[CustFirstName]]", $encData->decArray["rnameFirst"], $message);
				$message = str_replace("[[CustLastName]]", $encData->decArray["rnameLast"], $message);
				$message = str_replace("[[CustUserName]]", htmlentities($_SESSION["ODOSessionO"]->EscapedVars["UName"]), $message);
				$LogoURL = "<img src=\"http://" . SERVERADDRESS . "/" . GETIMAGEURLPATH . "?Name=Logo\">";
				$message = str_replace("[[Logo]]", $LogoURL, $message);
				$message = str_replace("[[RegName]]", $RegName, $message);
				$message = str_replace("[[ServerAddress]]", SERVERADDRESS, $message);
				
				$LoginInfo = "<BR>Your username is: " . htmlentities($_SESSION["ODOSessionO"]->EscapedVars["UName"]) . "<BR>\nYour new password is: " . $newpass . "<BR><BR>\n\n";
				
				if($NeedVerify) {
				
					$LoginInfo = $LoginInfo . "<p>Your account requires verification from an Administrator before you will be able to login. Please wait to login until you receive a confirmation e-mail from an administrator.</p>";

				}

			$message = str_replace("[[LoginInfo]]", $LoginInfo, $message);
		
			$query = "UPDATE ODOUsers SET pass='" . $_SESSION["ODOUserO"]->ODOHash($newpass, $_SESSION["ODOUserO"]->GetHashMode()) . "' WHERE UID=" . $MyID;
			$result = $GLOBALS['globalref'][1]->Query($query);

			if(!($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0)) {
				//error updating. We will need to notify the user that the admin will have to resolve this issue.
				trigger_error("ODOWEBERROR:RegisterObject:Could not update password for UID:" . $MyID, E_USER_WARNING);
				$message = $message . "<B>There was an error trying to update your temporary password. The admin has been notified of this error.</B><BR>";

			}

            $EmailMsg = new ODOMailMessage();
            
            $EmailMsg->to = $_SESSION["ODOSessionO"]->EscapedVars["email"];
            $EmailMsg->subject = "Registered at " . SERVERNAME;
            $EmailMsg->message = $message;
            $EmailMsg->headers = $headers;
            
            $GLOBALS['globalref'][4]->TransactionAwareEmail($EmailMsg);
			
			$this->Content = $this->Content . "<br>The password has been e-mailed to: " . $_SESSION["ODOSessionO"]->EscapedVars["email"];
			

			
		}
		
		//********************************************************
		//END Standard User Registration
		//********************************************************
		
		//********************************************************
		//START Specific Registration Type code
		//********************************************************
		//check registration type passed
		//if RegType is 1 then ignore as we already registered above if allowed for standard users
		if((!$ErrorWithData)&&(isset($_SESSION["ODOSessionO"]->EscapedVars["RegID"])) && ($_SESSION["ODOSessionO"]->EscapedVars["RegID"]!=1)) {
			$query = "SELECT Name, InsertDataCode, AllowGuest, NeedVerify, AfterRegCompleteURL FROM RegistrationTypes WHERE RegID=" . $_SESSION["ODOSessionO"]->EscapedVars["RegID"];
			
			if(($_SESSION["ODOUserO"]->getLoggedIn()) && (!($_SESSION["ODOUserO"]->getIsGuest()))) {
			 	$query = $query . " AND AllowGuest=1";
			} 

			$result = $GLOBALS['globalref'][1]->Query($query);

			$RegName = "";
			if(mysqli_num_rows($result) < 1) {
				//invalid RegID type, just exit
				$this->MissingText = $this->MissingText . "An invalid Registration type was passed. ";
				return false;
			} else {
				$row = mysqli_fetch_assoc($result);
				$RegName = $row["Name"];
				
				$RValue = "";
				$MyFunc = $row["InsertDataCode"];
				if(is_callable(array(&$this, $MyFunc))) {
					$RValue = call_user_func(array(&$this, $MyFunc));
					if(($RValue === "ERROR") || ($RValue === FALSE)) {
						$ErrorWithData = true;
						return false;
					}
				} else {
					trigger_error("REGISTER ERROR:We could not call the function name " . $row["InsertDataCode"], E_USER_ERROR);
					exit();
				}

				//setup verify if needed by reg type
				if($row["NeedVerify"] == 1) {
					$NeedVerify = true;
					//we might not have a UID here check for errors before inserting anything
					if((!$ErrorWithData) && ($MyID != 0)) { 
						//RQuestion is always null for non standard user verifies
						$query = "INSERT INTO AdminVerify(RegID, UID, RQuestion) values(" . $_SESSION["ODOSessionO"]->EscapedVars["RegID"] . "," . $MyID . ",NULL)";
						$result = $GLOBALS['globalref'][1]->Query($query);

						if(mysqli_num_rows($result) < 1) {
							trigger_error("REGISTER ERROR:We could not create verification entry for new user." . $MyID, E_USER_ERROR);
							exit();
						}
					}

				} else {
					
					//add user groups
					//else set groups now
					//we might not have a UID here check for errors before inserting anything
					if((!$ErrorWithData) && ($MyID != 0)) { 
					
						$query = "SELECT * FROM RegAssignedToGroups WHERE RegID=" . $_SESSION["ODOSessionO"]->EscapedVars["RegID"];
						$result = $GLOBALS['globalref'][1]->Query($query);
				
						while($row = mysqli_fetch_assoc($result)) {
							//first remove in case of duplicates
							//we don't know if creator of reg type checked for previous registrations
							$query = "SELECT * FROM ODOUserGID WHERE GID=" . $row["GID"] . " AND UID=" . $MyID;
							$result2 = $GLOBALS['globalref'][1]->Query($query);
							if(mysqli_num_rows($result2) < 1) {
								$query = "INSERT INTO ODOUserGID(GID,UID) values(" . $row["GID"] . "," . $MyID . ")";
								$result2 = $GLOBALS['globalref'][1]->Query($query);
							}
						}
					}

					$Comment = "Userid:" . $MyID . " Registered for " . $RegName . ".";
					$GLOBALS['globalref'][4]->LogEvent("USERREGISTEREDPUB", $Comment, 1);
				}

			}

		}

		//*******************************************************
		//END Specific Registration type
		//*******************************************************
		
		//*******************************************************
		//*******************************************************
		//END of Registration
		//*******************************************************
		//*******************************************************
		
		//*******************************************************
		//Start of Forwarding onto Registration complete url
		//*******************************************************
		if($ErrorWithData) {
            
            $GLOBALS['globalref'][1]->rollBack();
            
			return false;
		
        } else {

            $GLOBALS['globalref'][1]->commit();
            
			$this->Content = $this->Content . "<BR>\n<B>";
			if($NeedVerify) {
				$this->Content = $this->Content . "This Registration Type requires a site Admin to verify your account before you will have access. You will be notified by e-mail once this access has been granted. ";
			

				$this->Content = $this->Content . "Registration is complete.</B></BR><BR>";
			} else {
				//should replace this with one query at the top of the function
				$localReg = 1;
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["RegID"])) {
					$localReg = $_SESSION["ODOSessionO"]->EscapedVars["RegID"];
				} 

			
				$this->Content = $this->Content . "Registration is complete.</B></BR><BR>";
				
				$rURL = $this->GetCompleteURLForReg($localReg);
					
					if(strlen($rURL) > 0) {
						$this->Content = $this->Content . "Please wait...<BR><BR>If you are not forwared in a few seconds you may click <a href=\"" . $rURL . "\">here</a> to continue.<BR><BR><script>\nfunction OnLoadRefresh(){\nwindow.location=\"" . $rURL . "\"\n}\ntimer=setTimeout('OnLoadRefresh()',5000)\n  </script>";
					}
					
				}

		}

		//**************************************************
		//END forwarding onto Registration complete URL
		//**************************************************

			return true;
	}
		
	

	
	function LoadRegisterPage() {
		
	
		$this->Content = "<form action=\"index.php\" name=\"registration\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"Register\"><input type=\"hidden\" name=\"RegMe\" value=\"true\">";


		//if user is not set then register for standard user
		if(($_SESSION["ODOUserO"]->getLoggedIn()) && (!($_SESSION["ODOUserO"]->getIsGuest()))) {

			//username firstname lastname
			$this->Content .= "<table id=\"regtable\"><TR><TD><div id=\"regboxid\" class=\"regboxed\"><div id=\"regcontent\" class=\"regcontent\"><table id=\"regcontenttable\"><TR id=\"regcontentusernametr\"><TD id=\"regcontentusernamelabeltd\"><div id=\"regcontentusernamelabel\" class=\"regcontentlabel\">UserName:</div></TD><TD id=\"regcontentusernametd\"><div id=\"regcontentusername\" class=\"regcontentinput\">" . $_SESSION["ODOUserO"]->getusername() . "</div></TD></TR><TR id=\"regcontentfirstnametr\"><TD id=\"regcontentfirstnamelabeltd\"><div id=\"regcontentfirstnamelabel\" class=\"regcontentlabel\">First Name:</div></TD><TD id=\"regcontentfirstnametd\"><div id=\"regcontentfirstname\" class=\"regcontentinput\">" . $_SESSION["ODOUserO"]->getfirstname() . "</div></TD></TR><TR id=\"regcontentlastnametr\"><TD id=\"regcontentlastnamelabeltd\"><div id=\"regcontentlastnamelabel\" class=\"regcontentlabel\">Last Name:</div></TD><TD id=\"regcontentlastnametd\"><div id=\"regcontentlastname\" class=\"regcontentinput\">" . $_SESSION["ODOUserO"]->getlastname() . "</div></TD></TR>";
			
			//email
			$this->Content .= "<TR id=\"regcontentemailtr\"><TD id=\"regcontentemaillabeltd\"><div id=\"regcontentemaillabel\" class=\"regcontentlabel\">E-mail Address:</div><div id=\"regcontentemailsublabel\"><sub>(will be verified)</sub></div></TD><TD id=\"regcontentemailtd\"><div id=\"regcontentemail\" class=\"regcontentinput\">" . $_SESSION["ODOUserO"]->getemail() . "</div></TD></TR>";
			
			
			if(isset($_SESSION["ODOSessionO"]->EscapedVars["RegID"])) {
					if(($_SESSION["ODOSessionO"]->EscapedVars["RegID"]==1)||($this->CheckUserIsRegisteredForRegID($_SESSION["ODOSessionO"]->EscapedVars["RegID"], $_SESSION["ODOUserO"]->getUID()))) {
					$url = $this->GetCompleteURLForReg($_SESSION["ODOSessionO"]->EscapedVars["RegID"]);
					$this->Content = $this->Content . "<div id=\"regcontentmsg\">It looks like you are already registered. Please wait while we redirect you. If you are not redirected in a few seconds please click <a href=\"" . $url . "\">here</a> to continue.</div></div></div></TD></TR></table><script>\nfunction OnLoadRefresh(){\nwindow.location='" . $url . "';\n}\ntimer=setTimeout('OnLoadRefresh()',5000)\n  </script>";
					return true;
				}
			}
		
		} else {

			if(!ALLOWGUESTSTANDARDREG) {
				//Guests can not use the public registration object to register. Admins can only enter a request.
				//prompt them to login
				$this->Content = "<div id=\"regcontentmsg\">This site does not allow guests to register. Please contact the Website owner or login below.</div>";
	
				//check if login failed. Login is called before this at page loadup. params are still set check for last function call
				$RValue = $_SESSION["ODOUserO"]->Login();
				if($RValue == false) {
					$this->Content = $this->Content . "Login Failed";
				} else {
					$this->Content = $this->Content . $RValue;
				}


				return true;
			}

			
			$this->Content = $this->Content . "<table id=\"regtable\">\n<TR>\n<TD>\n<div class=\"regboxed\" id=\"regboxid\">\n<div id=\"regcontent\" class=\"regcontent\">\n<table id=\"regcontenttable\">\n<TR id=\"regcontentusernametr\"><TD id=\"regcontentusernamelabeltd\"><div id=\"regcontentusernamelabel\" class=\"regcontentlabel\">UserName:</div></TD><TD id=\"regcontentusernametd\"><div id=\"regcontentusername\" class=\"regcontentinput\"><INPUT type=\"text\" id=\"regcontentusernametext\" name=\"UName\" size=\"10\" maxlength=\"20\"";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["UName"])) {
				$this->Content = $this->Content . " value=\"" . htmlentities($_POST["UName"]) . "\"";
			}
			
			$this->Content = $this->Content . "></div></TD></TR>\n<TR id=\"regcontentfirstnametr\">
						<TD id=\"regcontentfirstnamelabeltd\"><div id=\"regcontentfirstnamelabel\" class=\"regcontentlabel\">First Name:</div></TD><TD id=\"regcontentfirstnametd\"><div id=\"regcontentfirstname\" class=\"regcontentinput\"><input type=\"text\" id=\"regcontentfirstnametext\" name=\"FName\" size=\"20\" maxlength=\"100\"";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["FName"])) {
				$this->Content = $this->Content . " value=\"" . htmlentities($_POST["FName"]) . "\"";
	
			}

			$this->Content = $this->Content . "></div></TD></TR>\n<TR id=\"regcontentlastnametr\">
						<TD id=\"regcontentlastnamelabeltd\"><div id=\"regcontentlastnamelabel\" class=\"regcontentlabel\">Last Name:</div></TD><TD id=\"regcontentlastnametd\"><div id=\"regcontentlastname\" class=\"regcontentinput\"><input type=\"text\" id=\"regcontentlastnametext\" name=\"LName\" size=\"20\" maxlength=\"100\"";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["LName"])) {
				$this->Content = $this->Content . " value=\"" . htmlentities($_POST["LName"]) . "\"";
	
			}

			$this->Content = $this->Content . "></div></TD></TR>\n<TR id=\"regcontentemailtr\">
						<TD id=\"regcontentemaillabeltd\"><div id=\"regcontentemaillabel\" class=\"regcontentlabel\">E-mail Address:</div><div id=\"regcontentemailsublabel\"><sub>(will be verified)</sub></div></TD>
						<TD id=\"regcontentemailtd\"><div id=\"regcontentemail\" class=\"regcontentinput\"><input type=\"text\" id=\"regcontentemailtext\" name=\"email\" size=\"25\" maxlength=\"100\"";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["email"])) {
				$this->Content = $this->Content . " value=\"" . htmlentities($_POST["email"]) . "\"";
	
			}

			$query = "SELECT * FROM ResetQuestions";
			
			$result = $GLOBALS['globalref'][1]->Query($query);
				
			
						
			$this->Content = $this->Content . "></div></TD></TR>\n<TR id=\"regcontentresetquestiontr\"><TD id=\"regcontentresetquestionlabeltd\"><div id=\"regcontentresetquestionlabel\" class=\"regcontentlabel\">Password Reset Question:</div></TD><TD id=\"regcontentresetquestiontd\"><div id=\"regcontentresetquestion\"><SELECT id=\"regcontentresetquestiontext\" NAME=\"RQuestion\"><OPTION value=\"0\" ";

			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["RQuestion"])) {
				$this->Content = $this->Content . " selected";
			} elseif($_SESSION["ODOSessionO"]->EscapedVars["RQuestion"]=="0") {
				$this->Content = $this->Content . " selected";
			}
			
			$this->Content = $this->Content . "></OPTION>\n";
			
			while($row = mysqli_fetch_assoc($result)) {
				
				$this->Content = $this->Content . "<OPTION value=\"" . $row["ResetQID"] . "\"";
				
				if((isset($_SESSION["ODOSessionO"]->EscapedVars["RQuestion"]))&&($_SESSION["ODOSessionO"]->EscapedVars["RQuestion"]==$row["ResetQID"])) {
					
					$this->Content = $this->Content . " selected";
				}
				
				$this->Content = $this->Content . ">" . $row["ResetQ"] . "</OPTION>";

			}

			$this->Content = $this->Content . "</div></TD></TR>\n<TR id=\"regcontentresetanswertr\"><TD id=\"regcontentresetanswerlabeltd\"><div id=\"regcontentresetanswerlabel\" class=\"regcontentlabel\">Password Reset Answer:</div><div id=\"regcontentresetanswersublabel\"><sub>(case sensitive)</sub></div></TD><TD id=\"regcontentresetanswertd\"><div id=\"regcontentresetanswer\"><input type=\"text\" name=\"RAnswer\" size=\"25\" maxlength=\"100\"";

			if(isset($_SESSION["ODOSessionO"]->EscapedVars["RAnswer"])) {

				$this->Content = $this->Content . " value=\"" . htmlentities($_POST["RAnswer"]) . "\"";

			}

			$this->Content = $this->Content . "></div></TD></TR>\n";

		}
	
		//check registration type passed
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["RegID"])) {
			$query = "SELECT RegID, UserInputCode FROM RegistrationTypes WHERE RegID=" . $_SESSION["ODOSessionO"]->EscapedVars["RegID"];

			if(($_SESSION["ODOUserO"]->getLoggedIn()) && (($_SESSION["ODOUserO"]->getIsGuest()))) {
			 	$query = $query . " AND AllowGuest=1";
			} 
			$result = $GLOBALS['globalref'][1]->Query($query);

			if(mysqli_num_rows($result) < 1) {
				//invalid RegID type, just exit
				return false;
			} else {
				$row = mysqli_fetch_assoc($result);
				$RValue = "";
				$MyFunc = $row["UserInputCode"];
				if(is_callable(array(&$this, $MyFunc))) {
					$RValue = call_user_func(array(&$this, $MyFunc));
					if($RValue == false) {
						trigger_error("REGISTER ERROR:We could not call the function name " . $row["UserInputCode"], E_USER_ERROR);
						exit();
					}
					$this->Content = $this->Content . "<input type=\"hidden\" name=\"RegID\" value=\"" . $row["RegID"] . "\">" . $RValue;
				}
				

			}

		} else {
		
			$query = "SELECT * FROM RegistrationTypes WHERE RegID>1";
		
			if(($_SESSION["ODOUserO"]->getLoggedIn()) && (($_SESSION["ODOUserO"]->getIsGuest()))) {
			 	$query = $query . " AND AllowGuest=1";
			} 
			
			$result = $GLOBALS['globalref'][1]->Query($query);

			//assign different row names for each type
			//first row is drop down combo
			
			if(mysqli_num_rows($result) > 0) {

				$this->Content = $this->Content . "<BR><BR><center>Register as:<SELECT id=\"RegType\" name=\"RegID\" onChange=\"ShowOtherTableRows(this)\"><OPTION value=\"1\" SELECTED>Standard User</Option>";

				$RestofRows = "";

				while($row = mysqli_fetch_assoc($result)) {
		
					$this->Content = $this->Content . "<OPTION value=\"" . $row["RegID"] . "\">" . $row["Name"] . "</OPTION>\n";
					//make sure it is callable
					//call function to load user input values
					$RValue = "";
					$MyFunc = $row["UserInputCode"];
					if(is_callable(array(&$this, $MyFunc))) {
						$RValue = call_user_func(array(&$this, $MyFunc));
						if($RValue == false) {
							trigger_error("REGISTER ERROR:We could not call the function name " . $row["UserInputCode"], E_USER_ERROR);
							exit();
						}
						
					} else {
						trigger_error("REGISTER ERROR:We could not call the function name " . $row["UserInputCode"], E_USER_ERROR);
						exit();
					}
					$RestofRows = $RestofRows . $RValue;

				}

				$this->Content = $this->Content . $RestofRows;
			}

		}

		$this->Content = $this->Content . "<TR><TD colspan=\"2\"><div id=\"regcontentspamlabel\"><sub>Enter the text you see in the image.</sub></div><div id=\"regcontentspam\"><span id=\"regcontentspamimage\"><img src=\"NoSpam.php\"></span><span id=\"regcontentspamanswer\"><input type=\"text\" id=\"regcontentspamanswertext\" name=\"chanswer\" size=\"10\" maxsize=\"10\"></span></div>\n";
	
		if(TOSONREGISTER) {
			$this->Content = $this->Content . "<div id=\"regcontenttos\"><input type=\"checkbox\" id=\"regcontenttoscheckbox\" name=\"TOS\">&nbsp;<span id=\"regcontenttoslabel\">I agree to the </span><span id=\"regcontenttoslink\"><a href=\"#\" onclick=\"window.open('index.php?pg=Legal', 'Legal')\">Terms of Service</a></span>.</div>";
		}

		$this->Content = $this->Content . "<div id=\"regcontentsubmitdiv\"><input type=\"submit\" id=\"regcontentsubmit\" name=\"submit\" value=\"Submit\"></div></div></div></TD></TR></TABLE></TD></TR></TABLE></form>";

		return true;
	}
//custom functions


//begin footer
	function __wakeup() {

		$this->OKCaptcha = true;
		$this->UNameok = true;
		$this->emailok = true;
		$this->RQuestionOK = true;
		$this->RAnswerOK = true;
		$this->MyUID = 0;
		$this->Content = "";
		$this->MissingText = "";

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["ODOStore"])) {
			$this->ODOStore = true;
		}
		
	}

}


?>