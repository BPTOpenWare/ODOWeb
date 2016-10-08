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


class ODOCMSLogs {

	var $SelectedReport;
	var $Content;
	var $OurDataLogLength;


	function __construct() {
		$Content = "";
		$SelectedReport = 0;
		$this->OurDataLogLength = 0;
		
	}
	
	private function LoadODOMenus() {
		
		//LOGS
		//->View By Types
		//->View By Severity
		//->View IPs
		//->Search
		
		
		//Reports
		//->Login Logout Report
		//->Access Violations
		//->Website errors
		//->Manage Reports
		
		//Cleanup
		//->Remove Old Logs
		//->Remove Logs by...

		//IP Lists
		//Black/White List
		//IP tools

	
		

	}
//header


	function ViewByType() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSLogs");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";
		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedType"])) {
			$query = "SELECT * FROM ODOLogs WHERE ActionDone='" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "' ORDER BY TimeStamp DESC";
	
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			//use old school PHP page viewer because of the amount of data that could be here
			//will use MySQL LIMIT in future versions

			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
				$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			}

			$RecordCount = mysqli_num_rows($TempRecords);
			$NumofPages = $RecordCount / 20;
			$Count = 0;

			if( ($RecordCount % 20 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
			} else {
				
				$this->Content = $this->Content . "<center><br><H3><B>Log List</B></H3><br><H3>Type: " . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "</H3><BR><BR><table border=1><TR><TH>Time Stamp</TH><th>UserID</th><th>Severity</th><TH>IP Add</TH><th>Description</th></TR>";

				$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 20;
				mysqli_data_seek($TempRecords, $ChangeToRow);
				
				while(($Count < 20) && ($row = mysqli_fetch_assoc($TempRecords))) {
					$this->Content = $this->Content . "<tr><TD>" . $row["TimeStamp"] . "</TD><td><center><a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=EditUser&userid=" . $row["UID"] . "\">" . $row["UID"] . "</a></center></td><td>" . $row["Severity"] . "</td><td><center><a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=IPTools&IP=" . $row["IP"] . "\">" . $row["IP"] . "</a></center></td>";

					$this->Content = $this->Content . "<td>" . $row["Comment"] . "</td></TR>\n";
			
			
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
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByType&SelectedType=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByType&SelectedType=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

				if($i > $NumofPages) {
					$this->Content = $this->Content . "&nbsp;Next->";
				} else {
					$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByType&SelectedType=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "&PageNum=" . $i . "\">Next-></a>";
				}

				$this->Content = $this->Content . "</center>";

			}

		} else {
		
			$this->Content = $this->Content . "<BR><BR><center><H3>Please select a type of log to view.</H3><BR>\n<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSLogsO\"><input type=\"hidden\" name=\"fn\" value=\"ViewByType\"><SELECT name=\"SelectedType\">";

			$query = "SELECT ActionDone FROM ODOLogs Group by ActionDone";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<option value=\"" . $row["ActionDone"] . "\">" . $row["ActionDone"] . "</option>";

			}

			$this->Content = $this->Content . "</SELECT>\n<BR><BR><input type=\"submit\" name=\"View\" value=\"View\"></form></center>";

		}
	}


	function ViewBySeverity() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSLogs");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";
		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["SelectedType"])) {
			$query = "SELECT * FROM ODOLogs WHERE Severity=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . " ORDER BY TimeStamp DESC";
	
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			//use old school PHP page viewer because of the amount of data that could be here
			//will use MySQL LIMIT in future versions

			if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
				$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			}

			$RecordCount = mysqli_num_rows($TempRecords);
			$NumofPages = $RecordCount / 20;
			$Count = 0;

			if( ($RecordCount % 20 > 0) ) {
				$NumofPages = $NumofPages + 1;
			}

			if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				$this->Content = $this->Content . "<h3>Page number is out of bounds!</h3><br><br></center>";
			} else {
				
				$this->Content = $this->Content . "<center><br><H3><B>Log List</B></H3><br><H3>Type: " . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "</H3><BR><BR><table border=1><TR><TH>Time Stamp</TH><th>UserID</th><th>Action</th><TH>IP Add</TH><th>Description</th></TR>";

				$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 20;
				mysqli_data_seek($TempRecords, $ChangeToRow);
				
				while(($Count < 20) && ($row = mysqli_fetch_assoc($TempRecords))) {
					$this->Content = $this->Content . "<tr><TD>" . $row["TimeStamp"] . "</TD><td><center><a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=EditUser&userid=" . $row["UID"] . "\">" . $row["UID"] . "</a></center></td><td>" . $row["ActionDone"] . "</td><td><center><a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=IPTools&IP=" . $row["IP"] . "\">" . $row["IP"] . "</a></center></td>";

					$this->Content = $this->Content . "<td>" . $row["Comment"] . "</td></TR>\n";
			
			
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
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewBySeverity&SelectedType=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "&PageNum=" . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
				} else {
					$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
				}

				while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
					if($CurPage == $i) {
						$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
					} else {
						$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewBySeverity&SelectedType=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

					}
					$i = $i + 1;
					$NumberofLinks = $NumberofLinks + 1;
				}

				if($i > $NumofPages) {
					$this->Content = $this->Content . "&nbsp;Next->";
				} else {
					$this->Content = $this->Content . "&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewBySeverity&SelectedType=" . $_SESSION["ODOSessionO"]->EscapedVars["SelectedType"] . "&PageNum=" . $i . "\">Next-></a>";
				}

				$this->Content = $this->Content . "</center>";

			}

		} else {
		
			$this->Content = $this->Content . "<BR><BR><center><H3>Please select a Severity to view.</H3><BR>\n<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSLogsO\"><input type=\"hidden\" name=\"fn\" value=\"ViewBySeverity\"><SELECT name=\"SelectedType\">";

			$query = "SELECT Severity FROM ODOLogs Group by Severity";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<option value=\"" . $row["Severity"] . "\">" . $row["Severity"] . "</option>";

			}

			$this->Content = $this->Content . "</SELECT>\n<BR><BR><input type=\"submit\" name=\"View\" value=\"View\"></form></center>";

		}
	}

	function ViewByTimeStamp() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSLogs");

		$this->LoadODOMenus();
		
		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			//reset our data length
			$this->OurDataLogLength=0;
		}
			
		if($this->OurDataLogLength==0) {
			$query = "SELECT count(*) as ULogs FROM ODOLogs";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			if(mysqli_num_rows($TempRecords) > 0) {
				$row = mysqli_fetch_assoc($TempRecords);
				$this->OurDataLogLength = $row["ULogs"];
			} else {
				trigger_error("Error: NO LOGS FOUND!", E_USER_NOTICE);
				$this->Content = $this->Content . "<B>NO LOGS WERE FOUND!</B>";
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
			
			$query = "select * from ODOLogs order by TimeStamp DESC, PriKey DESC LIMIT " . $LimitStart . ",20";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			//simple table format
			$this->Content = $this->Content . "<BR><BR><H3>Logs By TimeStamp</H3><BR><table border=1><TR><TH>TimeStamp</TH><TH>UID</TH><TH>Action</TH><TH>Comment</TH><TH>Severity</TH><TH>IP</TH></TR>";
	
			while(($Count < 20) && ($row = mysqli_fetch_assoc($TempRecords))) {
				
				$this->Content = $this->Content . "<tr><td>" . $row["TimeStamp"] . "</td><TD><a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=EditUser&userid=" . $row["UID"] . "\">" . $row["UID"] . "</a></td><td>" . $row["ActionDone"] . "</td><td>" . $row["Comment"] . "</td><td>" . $row["Severity"] . "</td><td>" . $row["IP"] . "</td></tr>";
				
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
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByTimeStamp&PageNum=" . ($CurPage - 5) . "\"><<</a>&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByTimeStamp&PageNum=" . ($CurPage - 1) . "\"><-Previous</a>&nbsp;&nbsp;";
				//Not counted in number of page links
			} elseif($CurPage > 1) {
				$this->Content = $this->Content . "<<&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByTimeStamp&PageNum=" . ($CurPage - 1) . "\"><-Previous</a>&nbsp;&nbsp;";
			} else {
				$this->Content = $this->Content . "<<&nbsp;<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByTimeStamp&PageNum=" . $i . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if(($CurPage+5) <= $NumofPages) {
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByTimeStamp&PageNum=" . ($CurPage+1) . "\">Next-></a>&nbsp;<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByTimeStamp&PageNum=" . ($CurPage+5) . "\">>></a>";
			} elseif($CurPage < $NumofPages) {
				$this->Content = $this->Content . "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=ViewByTimeStamp&PageNum=" . ($CurPage+1) . "\">Next-></a>&nbsp;>>";
			} else {
				$this->Content = $this->Content . "Next->&nbsp;>>";
			}
			
		}
		
	}
		
	function ViewByIP() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSLogs");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center><BR><BR><center><H3>Logs By IP Address</H3><BR><table border=1><TR><TH>IP Address</TH><TH>Number of Logs</TH></TR>";
		
		$query = "SELECT IP, count(*) from ODOLogs Group BY IP ORDER BY count(*) DESC";

		$TempRecords = $GLOBALS['globalref'][1]->Query($query);

		$i = 0;
		$RecordCount = mysqli_num_rows($TempRecords);
		$NumofPages = $RecordCount / 20;
		$NumofPages = intval($NumofPages);

		if( ($RecordCount % 20 > 0) ) {
			$NumofPages = $NumofPages + 1;
		}
		
		while($row = mysqli_fetch_assoc($TempRecords)) {
			$this->Content = $this->Content . "<TR id=\"rownum" . $i . "\"";

			if($i > 19) {
				$this->Content = $this->Content . " style=display:none";
			}
			
			$this->Content = $this->Content . "><TD><a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=IPTools&IP=" . $row["IP"] . "\">" . $row["IP"] . "</a></TD><TD>" . $row["count(UID)"] . "</TD></TR>";
			$i = $i + 1;
		}

		$this->Content = $this->Content . "</TABLE></center>";

		
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

	function Search() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSLogs");

		$this->LoadODOMenus();
		
		$this->Content = $this->Content . "</center>";
		
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["SearchMe"])) {
			
			
			$SearchCrit = "";
			
			
			
			if(strlen($SearchCrit) < 3) {
				trigger_error("You must select at least one Search Criteria!");
				$this->Content = $this->Content . "You must select at least one Search Criteria!</center>";
			} else {
				
				$query = "SELECT * FROM ODOLogs WHERE" . $SearchCrit;
				//store to session and use page view to goto next record
				
			}
			

		} else if(isset($_SESSION["ODOSessionO"]->EscapedVars["Page"])) {
			
			
			
		} else {
			$this->Content = $this->Content . "<BR><BR><center><H3>Enter Search Terms</H3></center><BR><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"SearchMe\" value=\"SearchMe\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSLogsO\"><input type=\"hidden\" name=\"fn\" value=\"Search\">";
		
			//by Action done
			$this->Content = $this->Content . "<table><TR><TD>Select by ActionDone:</TD><TD><SELECT name=\"SelectedType\"><option value=\"\"></option>\n";

			$query = "SELECT ActionDone FROM ODOLogs Group by ActionDone";

			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<option value=\"" . $row["ActionDone"] . "\">" . $row["ActionDone"] . "</option>\n";

			}

			$this->Content = $this->Content . "</SELECT></TD></TR>";

			//by UID
			$query = "SELECT ODOLogs.UID as UID, user FROM ODOLogs LEFT JOIN ODOUsers on ODOLogs.UID=ODOUsers.UID Group by UID ";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$this->Content = $this->Content . "<TR><TD>Select By UID:</TD><TD><SELECT name=\"SelectedType\"><option value=\"\"></option>\n";

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<option value=\"" . $row["UID"] . "\">";
				if(!is_null($row["user"])) {
					$this->Content = $this->Content . "(" . $row["user"] . ")";
				}
				$this->Content = $this->Content . $row["UID"] . "</option>\n";

			}

			$this->Content = $this->Content . "</SELECT></TD></TR>";

			//by date/time
			$this->Content = $this->Content . "<TR><TD>Start Time:</TD><TD><input type=\"datetime\" name=\"StartDate\"></TD></TR>";
			$this->Content = $this->Content . "<TR><TD>End Time:</td><TD><input type=\"datetime\" name=\"EndDate\"></TD></TR>";

			//by severity
			$query = "SELECT DISTINCT Severity FROM ODOLogs";
			$this->Content = $this->Content . "<TR><TD>Severity:</TD><TD><SELECT name=\"Severity\"><option value=\"\"></option>\n";
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			while($row = mysqli_fetch_assoc($TempRecords)) {
				$this->Content = $this->Content . "<option value=\"" . $row["Severity"] . "\">" . $row["Severity"] . "</option>\n";

			}

			$this->Content = $this->Content . "</SELECT></TD></TR>";
			
			//IP addy
			$this->Content = $this->Content . "<TR><TD>IP Address (all or partial x.x.x.x ipv4 only)</TD><TD><input type=\"text\" name=\"IPAddy\" size=\"15\" length=\"15\"></TD></TR></TABLE><input type=\"submit\" name=\"Search\" value=\"Search\"></center>";

		}

	}

	function UserLoginLogoutReport() {
		$this->Content = "<center>" . $_SESSION["ODOSessionO"]->UserObjectArray["ODOMenuO"]->GetODOTopMenu("AdminCMSLogs");

		$this->LoadODOMenus();

		if(!isset($_SESSION["ODOSessionO"]->EscapedVars["PageNum"])) {
			$_SESSION["ODOSessionO"]->EscapedVars["PageNum"] = 1;
			//reset our data length only used for user logs
			$this->OurDataLogLength=0;
		}

		if(isset($_SESSION["ODOSessionO"]->EscapedVars["CurUser"])) {
			$this->BuildUserLoginLogOutReport();
		} else {
			$this->BuildUserListForLoginLogOutReporting() ;
		}
		
		$this->Content = $this->Content . "</center>";
		
	}
		
	private function BuildUserListForLoginLogOutReporting() {
			//load UserRecords
			$query = "select * from ODOUsers ORDER BY user";
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$RecordCount = mysqli_num_rows($TempRecords);
			$NumofPages = $RecordCount / 15;
			$Count = 0;

			if( ($RecordCount % 15 > 0) ) {
				$NumofPages = intval($NumofPages) + 1;
			}
			
			if(($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] > $NumofPages) || ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] < 1)) {
				$this->Content = $this->Content . "<center><h4>Page number is out of bounds!</h4><br><br></center>";
				return;
			}
			
			$this->Content = $this->Content . "<br><br><table border=1><TR><TH>User ID</TH><th>User Name</th><th>User's Real Name</th><th>User's e-mail address</th><th>View Report</th></TR><TR><TD>N/A</TD><TD>View All Users</TD><TD>View All Users</TD><TD>N/A</TD><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSLogsO\"><input type=\"hidden\" name=\"fn\" value=\"UserLoginLogoutReport\"><input type=\"hidden\" name=\"CurUser\" value=\"0\"><TD><input type=\"submit\" name=\"ViewAll\" value=\"View All\"></TD></form></TR>";

			$ChangeToRow = ($_SESSION["ODOSessionO"]->EscapedVars["PageNum"] - 1) * 15;
			mysqli_data_seek($TempRecords, $ChangeToRow);
			while(($Count < 15) && ($row = mysqli_fetch_assoc($TempRecords))) {
				$this->Content = $this->Content . "<tr><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSLogsO\"><input type=\"hidden\" name=\"fn\" value=\"UserLoginLogoutReport\"><input type=\"hidden\" name=\"CurUser\" value=\"" . $row["UID"] . "\"><TD>" . $row["UID"] . "</TD><TD>" . $row["user"] . "</TD><TD>" . $row["rnameLast"] . ", " . $row["rnameFirst"] . "</TD><TD>" . $row["emailadd"] . "</TD><TD><input type=\"submit\" name=\"ViewReport\" value=\"View Report\"></TD></form></TR>";
			
			
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

			$link = "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=UserLoginLogoutReport&PageNum=";
			if(($CurPage > 5)) {
				$this->Content = $this->Content . $link . ($CurPage - 5) . "\"><-Previous</a>&nbsp;&nbsp;";
					//Not counted in number of page links
			} else {
				$this->Content = $this->Content . "<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . $link . $i . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if($NumofPages >= ($i+5)) {
				$this->Content = $this->Content . "&nbsp;" . $link . ($i+5) . "\">Next-></a>";
			} elseif($NumofPages > $i) {
				$this->Content = $this->Content . "&nbsp;" . $link . $NumofPages . "\">Next-></a>";
				
			} else {
				$this->Content = $this->Content . "&nbsp;Next->";
			}
		
	}
	
	private function BuildUserLoginLogOutReport() {
		
			//build query
			//separate out to use two different queries. 
			$querySelectColumns = "SELECT * ";
			$querySelectCount = "SELECT count(*) as ULogs ";
			$query = "FROM ODOLogs WHERE (ActionDone = 'LOGIN' OR ActionDone='LOGOUT')";
				
			if($_SESSION["ODOSessionO"]->EscapedVars["CurUser"] != 0) {
				$query = $query . " AND UID=" . $_SESSION["ODOSessionO"]->EscapedVars["CurUser"];
			}
				
			$query = $query . " ORDER BY TimeStamp DESC";
			
			if($this->OurDataLogLength==0) {

				$TempRecords = $GLOBALS['globalref'][1]->Query($querySelectCount . $query);
				if(mysqli_num_rows($TempRecords) > 0) {
					$row = mysqli_fetch_assoc($TempRecords);
					if($row["ULogs"] > 0) {
						$this->OurDataLogLength = $row["ULogs"];
					} else {
						$this->Content = $this->Content . "<BR><BR><B>NO LOGS WERE FOUND!</B>";
						return;
					}
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
			
				$TempRecords = $GLOBALS['globalref'][1]->Query($querySelectColumns . $query . " LIMIT " . $LimitStart . ",20");

			//simple table format
			$this->Content = $this->Content . "<BR><BR><H3>Login/Logout Report</H3><BR><table border=1><TR><TH>TimeStamp</TH><TH>UID</TH><TH>Action</TH><TH>Comment</TH><TH>Severity</TH><TH>IP</TH></TR>";
	
			while(($Count < 20) && ($row = mysqli_fetch_assoc($TempRecords))) {
				
				$this->Content = $this->Content . "<tr><td>" . $row["TimeStamp"] . "</td><TD><a href=\"index.php?pg=ODOCMS&ob=ODOCMSUsersO&fn=EditUser&userid=" . $row["UID"] . "\">" . $row["UID"] . "</a></td><td>" . $row["ActionDone"] . "</td><td>" . $row["Comment"] . "</td><td>" . $row["Severity"] . "</td><td>" . $row["IP"] . "</td></tr>";
				
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

			$link = "<a href=\"index.php?pg=ODOCMS&ob=ODOCMSLogsO&fn=UserLoginLogoutReport&CurUser=" . htmlentities($_SESSION["ODOSessionO"]->EscapedVars["CurUser"]) . "&PageNum=";
			
			if(($CurPage > 5)) {
				$this->Content = $this->Content . $link . ($CurPage - 5) . "\"><<</a>&nbsp;" . $link . ($CurPage - 1) . "\"><-Previous</a>&nbsp;&nbsp;";
				//Not counted in number of page links
			} elseif($CurPage > 1) {
				$this->Content = $this->Content . "<<&nbsp;" . $link . ($CurPage - 1) . "\"><-Previous</a>&nbsp;&nbsp;";
			} else {
				$this->Content = $this->Content . "<<&nbsp;<-Previous&nbsp;&nbsp;";
			}

			while(($NumberofLinks < 11) && ($i <= $NumofPages)) {
				if($CurPage == $i) {
					$this->Content = $this->Content . "<B>" . $i . "</B>&nbsp;";
				} else {
					$this->Content = $this->Content . $link . $i . "\">" . $i . "</a>&nbsp;";

				}
				$i = $i + 1;
				$NumberofLinks = $NumberofLinks + 1;
			}

			if(($CurPage+5) <= $NumofPages) {
				$this->Content = $this->Content . $link . ($CurPage+1) . "\">Next-></a>&nbsp;" . $link . ($CurPage+5) . "\">>></a>";
			} elseif(($CurPage+1) < $NumofPages) {
				$this->Content = $this->Content . $link . ($CurPage+1) . "\">Next-></a>&nbsp;>>";
			} else {
				$this->Content = $this->Content . "Next->&nbsp;>>";
			}
			
		}

		
	}
	
//footer
	function __wakeup() {
		$this->Content = "";
	}

}




?>