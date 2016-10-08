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

/**
 * FileUtil class allows applications to files to the database
 * with a list of groups
 * @author nict
 *
 */
class FileUtil {


	/**
	 * Adds a file to the system
	 * @param string $fileName
	 * @param array $groups
	 * @param string $type
	 * @return int FID file id of the inserted file
	 */
	public function addFile($fileName, $fileLoc, $Description, $groups, $type) {
		$fid = 0;

		global $ODODBO;

		//verify file exists
		if(!file_exists($fileLoc)) {
			//log error
			$comment = "File not found {$fileLoc}";
			$GLOBALS['globalref'][4]->LogEvent("FILEUTIL", $comment, 3);

			return -1;
		}

		//get group ids
		$groupIDs = array();

		if(($groups != null)&&(count($groups)>0)) {

			foreach($groups as $group) {
				$query = "SELECT GID FROM ODOGroups WHERE GroupName='{$group}'";

				$tempRecords = $ODODBO->Query($query);

				if($row = mysqli_fetch_assoc($tempRecords)) {

					array_push($groupIDs,$row["GID"]);

				} else {
					//log error
					$comment = "Group passed not found {$group}";
					$GLOBALS['globalref'][4]->LogEvent("FILEUTIL", $comment, 3);

				}


			}

		}

		//get fileType id first
		$query = "SELECT FTypeID FROM FileTypes WHERE Type='{$type}'";

		$tempRecords = $ODODBO->Query($query);

		if(!($row = mysqli_fetch_assoc($tempRecords))) {

			//log error
			$comment = "FileUtil error missing type:{$type}";
			$GLOBALS['globalref'][4]->LogEvent("FILEUTIL", $comment, 3);

			return -1;
		}

		$typeID = $row["FTypeID"];



		//get md5sum and size of file
		$md5sum = md5_file($fileLoc);

		$size = filesize($fileLoc);
		
		//read in file as blob
		$handle = fopen($fileLoc, "r");
		$contents = fread($handle, $size);

		$contents = $ODODBO->EscapeMe($contents);
		
		fclose($handle);

		$ODODBO->startTransaction();

		$query = "INSERT INTO ODOFiles(Name, BFile, Description, chksum, Size, PermFlag, FTypeID) values('{$fileName}',";
		$query .= "'{$contents}',";
		$query .= "'{$Description}','{$md5sum}',{$size},";

		if(($groups != null)&&(count($groups)>0)) {
			$query .= "1,";
		} else {
			$query .= "0,";
		}

		$query .= "{$typeID})";

		$tempRecords = $ODODBO->Query($query);

		if($ODODBO->GetNumRowsAffected() < 1) {
			$ODODBO->rollBack();
			$_SESSION["ODOLoggingO"]->LogEvent("FILEUTIL", "Insert failed for file {$fileLoc}", 5);
			return -1;
		}

		//get last insert id
		$tempFID = $ODODBO->LastInsertID();

		if(count($groupIDs)>0) {

			foreach($groupIDs as $GID) {
				$query = "INSERT INTO GroupsForFiles(GID,FID) values({$GID},{$tempFID})";
				$tempRecords = $ODODBO->Query($query);

				if($ODODBO->GetNumRowsAffected() < 1) {
					$ODODBO->rollBack();
					$_SESSION["ODOLoggingO"]->LogEvent("FILEUTIL", "Insert failed for file {$fileLoc}", 5);
					return -1;
				}
			}
		}

		if(!$ODODBO->commit()) {
			$_SESSION["ODOLoggingO"]->LogEvent("FILEUTIL", "Commit failed for inserting file.", 5);
			return -1;
		}

		$fid = $tempFID;

		return $fid;
	}

	/**
	 * Updates a file based on fid
	 * @param int $fid
	 * @param string $fileName
	 * @param array $groups
	 * @param string $type
	 * @return boolean true on success otherwise false
	 */
	public function updateFile($fid, $fileLoc) {

		global $ODODBO;

		//verify file exists
		if(!file_exists($fileLoc)) {
			//log error
			$comment = "File not found {$fileLoc}";
			$GLOBALS['globalref'][4]->LogEvent("FILEUTIL", $comment, 3);

			return false;
		}

		//get md5sum and size of file
		$md5sum = md5_file($fileLoc);

		$size = filesize($fileLoc);
		
		//read in file as blob
		$handle = fopen($fileLoc, "r");
		$contents = fread($handle, $size);
		$contents = $ODODBO->EscapeMe($contents);
		
		fclose($handle);

		$ODODBO->startTransaction();

		$query = "UPDATE ODOFiles SET BFile=";
		$query .= "'{$contents}',";
		$query .= "chksum='{$md5sum}', Size={$size}";
		$query .= " WHERE FID={$fid}";

		$tempRecords = $ODODBO->Query($query);

		if($ODODBO->GetNumRowsAffected() < 1) {
			$ODODBO->rollBack();
			$_SESSION["ODOLoggingO"]->LogEvent("FILEUTIL", "Update failed for file {$fileLoc}", 5);
			return false;
		}

		if(!$ODODBO->commit()) {
			$_SESSION["ODOLoggingO"]->LogEvent("FILEUTIL", "Commit failed for updating file.", 5);
			return false;
		}


		return true;
	}

}


?>