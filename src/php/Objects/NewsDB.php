<?PHP

class NewsDB {

	private $recentPosts;
	
	function __construct() {
		
		$recentPosts = array();
		
	}
	
	/**
	 * Gets the most recent posts and uses the limit passed
	 * in to only return that many. it is assumed an application
	 * will only use one limit when calling this. Otherwise the cached
	 * recentPosts will be of an invalid length
	 * @param unknown $Limit limit on query
	 * @param string $Refresh if true the cache is ignored and the query is performed again
	 * @return array An array of all recent posts
	 */
	public function GetLastPost($Limit, $Refresh=FALSE) {
		
		$Post = new NewsItem();
		$Posts = Array();
		global $ODODBO;
		
		//if the cached array has values and we're not supposed
		//to refresh it then just return
		if((count($this->recentPosts) > 0)&&(!$Refresh)) {
			return $this->recentPosts;
		}
		
		if($_SESSION["ODOUserO"]->getIsGuest()) {
			
			$query = "SELECT news.PriKey, Date, Title, Article, CatID, CatName, AllowGuest FROM news LEFT JOIN newsCats on news.CatID=newsCats.PriKey WHERE AllowGuest=1 ORDER BY Date Desc LIMIT {$Limit}";
			
		} else {
			$query = "SELECT news.PriKey, Date, Title, Article, news.CatID, newsCats.CatName, AllowGuest FROM news, newsCats, ODOUserGID, newsGACL WHERE ODOUserGID.UID=? AND ODOUserGID.GID=newsGACL.GID AND newsGACL.newsGID=news.CatID and news.CatID=newsCats.PriKey ORDER BY Date Desc LIMIT {$Limit}";
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
		
		while($MyStm->fetch()) {
			$Post = new NewsItem();
			
			$Post->PostID = $PostID;
			$Post->PostDate = $dt;
			$Post->Title = $title;
			$Post->Article = $art;
			$Post->CatID = $catID;
			$Post->CategoryName = $CatName;
			$Post->AllowGuest = $AllowGuest;
			
			array_push($Posts, $Post);
		}
		
		/* free result */
		$MyStm->free_result();

		/* close statement */
		$MyStm->close();
		
		
		return $Posts;
		
	}
	
	public function getPostByID($PostID) {
		
		$Posts = array();

		if($_SESSION["ODOUserO"]->getIsGuest()) {
		
			$query = "SELECT news.PriKey, news.title, news.Article, news.Date, newsCats.CatName, news.CatID, AllowGuest from news, newsCats where news.AllowGuest=1 and news.CatID=newsCats.PriKey and news.PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"];
		
		} else {
		
			$query = "SELECT distinct news.PriKey, news.title, news.Article, news.Date, newsCats.CatName, news.CatID, AllowGuest FROM news, ODOUserGID, newsGACL, newsCats where ODOUserGID.UID=" . $_SESSION["ODOUserO"]->getUID() . " and ODOUserGID.GID=newsGACL.GID and newsGACL.newsGID=news.CatID and news.CatID=newsCats.PriKey and news.PriKey=" . $_SESSION["ODOSessionO"]->EscapedVars["PostID"];
		
		}
		
		$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			
		while($row = mysqli_fetch_assoc($TempRecords)) {
			$Post = new NewsItem();
			
			$Post->PostID = $row["PriKey"];
			$Post->PostDate = $row["Date"];
			$Post->Title = $row["title"];
			$Post->Article = $row["Article"];
			$Post->CatID = $row["CatID"];
			$Post->CategoryName = $row["CatName"];
			
			if((isset($row["AllowGuest"]))&&($row["AllowGuest"] == 1)) {
				$Post->AllowGuest = true;
			} else {
				$Post->AllowGuest = false;
			}
				
			array_push($Posts, $Post);
				
		}
		
		return $Posts;
		
	}
	
	
	public function getAllPosts($catID, $sDate, $eDate) {

		
			$Posts = array();
		
		
			if($_SESSION["ODOUserO"]->getIsGuest()) {
				$query = "SELECT news.PriKey, news.title, news.Article, news.Date, newsCats.CatName, news.CatID, AllowGuest FROM news, newsCats where news.AllowGuest=1 and news.CatID=newsCats.PriKey";
		
			} else {
				$query = "SELECT distinct news.PriKey, news.title, news.Article, news.Date, newsCats.CatName, news.CatID, AllowGuest FROM news, ODOUserGID, newsGACL, newsCats where ODOUserGID.UID=" . $_SESSION["ODOUserO"]->getUID() . " and ODOUserGID.GID=newsGACL.GID and newsGACL.newsGID=news.CatID and news.CatID=newsCats.PriKey";
			}
		
		
		
			if((isset($catID)) && (!is_null($catID))) {
		
				if(!is_numeric($_SESSION["ODOSessionO"]->EscapedVars["CatID"])) {
					trigger_error("CatID is not numeric! Your IP and this event have been logged.", E_USER_ERROR);
					exit(1);
				}
		
				$query = $query . " and news.CatID=" . $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
			}
		
			if((isset($sDate)) && (!is_null($sDate))) {
		
				$query = $query . " and news.Date>=" . date("Y-m-d", strtotime($_SESSION["ODOSessionO"]->EscapedVars["sDate"]));
		
			}
		
			if((isset($eDate)) && (!is_null($eDate))) {
		
				$query = $query . " and news.Date<=" . date("Y-m-d", strtotime($_SESSION["ODOSessionO"]->EscapedVars["eDate"]));
			}
		
		
			$query = $query . " order by news.Date DESC";
			
			$TempRecords = $GLOBALS['globalref'][1]->Query($query);
			

			while($row = mysqli_fetch_assoc($TempRecords)) {
				$Post = new NewsItem();
					
				$Post->PostID = $row["PriKey"];
				$Post->PostDate = $row["Date"];
				$Post->Title = $row["title"];
				$Post->Article = $row["Article"];
				$Post->CatID = $row["CatID"];
				$Post->CategoryName = $row["CatName"];
				
				if((isset($row["AllowGuest"]))&&($row["AllowGuest"] == 1)) {
					$Post->AllowGuest = true;
				} else {
					$Post->AllowGuest = false;
				}
			
				array_push($Posts, $Post);
			
			}
			
			return $Posts;
	}
}



?>