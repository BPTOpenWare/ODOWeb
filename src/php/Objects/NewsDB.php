<?PHP

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

class NewsDB {

	function __construct() {
		
	}
	
	function GetLastPost() {
		$Post = new NewsItem();
		global $ODODBO;
		
		
		if($_SESSION["ODOUserO"]->getIsGuest()) {
			
			$query = "SELECT news.PriKey, Date, Title, Article, CatID, CatName, AllowGuest FROM news LEFT JOIN newsCats on news.CatID=newsCats.PriKey WHERE AllowGuest=1 ORDER BY Date Desc LIMIT 1";
			
		} else {
			$query = "SELECT news.PriKey, Date, Title, Article, news.CatID, newsCats.CatName, AllowGuest FROM news, newsCats, ODOUserGID, newsGACL WHERE ODOUserGID.UID=? AND ODOUserGID.GID=newsGACL.GID AND newsGACL.newsGID=news.CatID and news.CatID=newsCats.PriKey ORDER BY Date Desc LIMIT 1";
		}
		
		//prepare
		if(!($MyStm = $ODODBO->prepare($query))) {
			trigger_error("Error executing query!", E_USER_ERROR);
			exit(1);
		}
			
		//bind if needed
		if($_SESSION["ODOUserO"]->isNonGuestLogin()) {
			$UID = $_SESSION["ODOUserO"]->getUID();
			$MyStm->bind_param("i", $UID);
		}
		
		if(!$MyStm->execute()) {
			trigger_error("Error executing query!", E_USER_ERROR);
			exit(1);
		}
			
		/* store result */
		$MyStm->store_result();
		
		if(!$MyStm->bind_result($PostID, $dt, $title, $art, $catID, $CatName, $AllowGuest)) 
		{
			trigger_error("Error binding result!", E_USER_ERROR);
			exit(1);
		}
		
		if($MyStm->fetch()) {
				
			$Post->PostID = $PostID;
			$Post->PostDate = $dt;
			$Post->Title = $title;
			$Post->Article = $art;
			$Post->CatID = $catID;
			$Post->CategoryName = $CatName;
			$Post->AllowGuest = $AllowGuest;
			
		}
		
		/* free result */
		$MyStm->free_result();

		/* close statement */
		$MyStm->close();
		
		
		return $Post;
		
	}
	
}



?>