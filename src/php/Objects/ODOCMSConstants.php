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


class ODOCMSConstants {
	
		var $Content;
		
		function __construct() {
			$this->Content = "";
			
		}
	
		function EditConstants() {
				if(!isset($_SESSION["ODOSessionO"]->EscapedVars["ConstID"])) {
						$this->Content = "<center><H4>You must select a constant to edit first!</H4></center>";
						return;
				}
				
				if(isset($_SESSION["ODOSessionO"]->EscapedVars["UpdateConst"])) {
						if(!isset($_SESSION["ODOSessionO"]->EscapedVars["UValue"])) {
							$this->Content = "<center><H4>You must set a value to update this constant to!</H4></center>";
							return;
						}
						
						$query = "UPDATE Constants SET Value='" . $_SESSION["ODOSessionO"]->EscapedVars["UValue"] . "' WHERE ConstID=" .  $_SESSION["ODOSessionO"]->EscapedVars["ConstID"];
						$TempRecords = $GLOBALS['globalref'][1]->Query($query);
					
						if($GLOBALS['globalref'][1]->GetNumRowsAffected() > 0) {
							$this->Content = "<center><H4>Constant has been updated.</H4></center>";
						} else {
							$this->Content = "<center><H4>The Constant was NOT updated. Please verify the table structure.</H4></center>";
						}
						
				} else {
				
					$query = "SELECT ConstID, ConstName, Value, Constants.Description as CDesc, Constants.ModID as ModID, Modules.ModName as ModName FROM Constants LEFT JOIN Modules on Constants.ModID = Modules.ModID WHERE ConstID=" . $_SESSION["ODOSessionO"]->EscapedVars["ConstID"];
					$TempRecords = $GLOBALS['globalref'][1]->Query($query);
					
					if(mysqli_num_rows($TempRecords)>0) {
						$row = mysqli_fetch_assoc($TempRecords);
						$this->Content = "<center>\n<H4>Edit Constant</H4>\n<BR><BR>\n\n<table border=\"2\">\n<TR>\n\t<TD>ConstantID:</TD><TD>" . $row["ConstID"] . "</TD>\n</TR>\n<TR>\n\t<TD>ModID:</TD><TD>" . $row["ModID"] . "</TD>\n</TR>\n<TR>\n\t<TD>Module name:</TD><TD>" . $row["ModName"] . "</TD>\n</TR>\n<TR>\n\t<TD>Constnt Name:</TD><TD>" . $row["ConstName"] . "</TD>\n</TR>\n<TR>\n\t<TD>Constant <BR>Description:</TD><TD>" . $row["CDesc"] . "</TD>\n</TR>\n<TR>\n\t<TD>Value:</TD>";
						
						$this->Content = $this->Content . "<form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"ODOCMS\"><input type=\"hidden\" name=\"ob\" value=\"ODOCMSConstantsO\"><input type=\"hidden\" name=\"fn\" value=\"EditConstants\"><input type=\"hidden\" name=\"ConstID\" value=\"" . $row["ConstID"] . "\"><input type=\"hidden\" name=\"UpdateConst\" value=\"UpdateConst\"><TD><input type=\"text\" size=\"50\" name=\"UValue\" value=\"" . $row["Value"] . "\"></TD>\n</TR>\n</TABLE>\n<br>\n<input type=\"submit\" name=\"Update\" value=\"Update\">\n</form>\n</CENTER>";
						
					} else {
						$this->Content = "<center><H4>Constant not found!</H4></center>";
					}
				
				}
		}
		
		function ViewConstants() {
			$query = "SELECT ConstID, ConstName, Value, Constants.Description as CDesc, Constants.ModID as ModID, Modules.ModName as ModName FROM Constants LEFT JOIN Modules on Constants.ModID = Modules.ModID ORDER BY Constants.ModID ASC";
				
				$TempRecords = $GLOBALS['globalref'][1]->Query($query);

			$CurModID = 0;
			
				if(mysqli_num_rows($TempRecords)>0) {
					$this->Content = "<center>\n<H4>Constants</H4>\n<br>\n<Table border=\"2\">\n<TR>\n\t<TH>ConstID</TH><TH>Constant Name</TH><TH>Description</TH><TH>Value</TH><TH>Mod ID</TH><TH>Module Name</TH><TH>Edit</TH>\n</TR>\n";
					while($row = mysqli_fetch_assoc($TempRecords)){
			
						//check to see if we need to show a divider
						if((isset($row["ModID"]))&&($CurModID != $row["ModID"])) {
							$CurModID = $row["ModID"];
							$this->Content = $this->Content . "<TR>\n\t<TD colspan=\"7\" align=\"center\"><B>";
							if(isset($row["ModName"])) {
								$this->Content = $this->Content . $row["ModName"];
							} else {
								$this->Content = $this->Content . "Unknown Module";
							}

							$this->Content = $this->Content . "</B></TD>\n</TR>\n";
						}
						
						$this->Content = $this->Content . "<TR>\n\t<TD>" . $row["ConstID"] . "</TD><TD>" . $row["ConstName"] . "</TD><TD>" . $row["Value"] . "</TD><TD>" . $row["CDesc"] . "</TD><TD>";
						
						if(isset($row["ModID"])) {
							$this->Content = $this->Content . $row["ModID"] . "</TD><TD>";
							
							if(isset($row["ModName"])) {
								$this->Content = $this->Content . $row["ModName"] . "</TD>";
							} else {
								$this->Content = $this->Content . "Default</TD>";
							}
							
						} else {
							$this->Content = $this->Content . "Not Assigned</TD><TD>Not Assigned</TD>";
						}
						
						$this->Content = $this->Content . "<TD><input type=\"button\" name=\"Edit\" value=\"Edit\" onclick=\"window.location.href='index.php?pg=ODOCMS&ob=ODOCMSConstantsO&fn=EditConstants&ConstID=" . $row["ConstID"] . "'\"/></TD>\n</TR>\n";
						
					}
					
					$this->Content = $this->Content . "</table>";
					
				} else {
						$this->Content = "<center><B>There was an error retreiving the constants.</B></center>";
				}
		}
	
	//footer
	
	function __sleep() {
		$this->Content = "";
		return( array_keys( get_object_vars( $this ) ) );
	}
	
}




?>