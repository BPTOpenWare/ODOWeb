<?PHP


class NewsView {

	private $NewsDBO;
	
	function __construct() {
		//check for registered object.
		if(!$_SESSION["ODOSessionO"]->IsRegistered("NewsDBO")) {
			$this->NewsDBO = new NewsDB();
			$_SESSION["ODOSessionO"]->RegisterObject("NewsDBO", $this->NewsDBO);

		} else {
			$this->NewsDBO = $_SESSION["ODOSessionO"]->GetObjectRef("NewsDBO");
		}
	}
	
	
	public function GetLastThreePosts() {
		
		$Posts = $this->NewsDBO->GetLastPost(3);
		

		$content = "";
		
		if(($Posts != null) && (count($Posts)>0)) {

			$content .= "<ul class=\"style3\">";
			$isFirst = true;
			
			foreach($Posts as $key=>$Post) {
				$ArticleStripped = $this->FormatPost($Post->Article);
					
			
				date_default_timezone_set('America/Chicago');
					
				//format date
				$datefromdb = $Post->PostDate;
				$year = substr($datefromdb,0,4);
				$mon  = substr($datefromdb,5,2);
				$day  = substr($datefromdb,8,2);
				$mktimeDate = mktime( 0, 0, 0,$mon, $day, $year);
			
				$mon = date('M', $mktimeDate);
				$day = date('d', $mktimeDate);
				
				if($isFirst) {
					
					$content .= "<li class=\"first\">";
					$isFirst = false;
					
				} else {
					
					$content .= "<li>";
				}
				
				$content .= "<p class=\"date\">\n<a href=\"#\">{$mon}<b>{$day}</b></a></p>\n";
				$content .= "<h3>{$Post->Title}</H3>\n";
				$content .= "<p><a href=\"#\">{$ArticleStripped}</a></p>\n</li>";
					
			}
			
			$content .= "</ul>\n";
			
		}
	
		return $content;
	}
	
	public function GetLastPost() {
		
		$Posts = $this->NewsDBO->GetLastPost(1);
		$Post = $Posts[0];
		$ArticleStripped = $this->FormatPost($Post->Article);
		
		$content = "<div id=\"banner_title\">{$Post->Title}</div>\n<p>{$ArticleStripped}</p>\n<div class=\"more_button\"><a href=\"index.php?pg=News&PostID={$Post->PostID}\">Read More</a></div>";
		
		return $content;
	}
	
	public function buildNewsContent() {
	
		$newsContent = "";
		$Posts = array();
		
		//Load up by Post first then Cat then Date if no parameters are specified then we need to load all
		//cats we have permission for. The result is displayed by the dynamic news page which everyone in the
		//system has access to.
		if(isset($_SESSION["ODOSessionO"]->EscapedVars["PostID"])) {
			//verify they have access rights to it also
			//if guest though we need to check based on guest group and not guest userid
			//this allows guests to be in the system but not have a guest login id
		
			if(!is_numeric($_SESSION["ODOSessionO"]->EscapedVars["PostID"])) {
				trigger_error("PostID is not numeric! Your IP and this event have been logged.", E_USER_ERROR);
				exit(1);
			}
		
			$PostID = $_SESSION["ODOSessionO"]->EscapedVars["PostID"];
			
			$Posts = $this->NewsDBO->getPostByID($PostID);
			
		} else {
			
			$catId = null;
			$sDate = null;
			$eDate = null;
			
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["CatID"]))&&
					(strlen($_SESSION["ODOSessionO"]->EscapedVars["CatID"]) > 0)) {
		
				if(!is_numeric($_SESSION["ODOSessionO"]->EscapedVars["CatID"])) {
					trigger_error("CatID is not numeric! Your IP and this event have been logged.", E_USER_ERROR);
					return $newsContent;
				}
				
				$catId = $_SESSION["ODOSessionO"]->EscapedVars["CatID"];
				
			}
			

			if((isset($_SESSION["ODOSessionO"]->EscapedVars["sDate"]))&&
					(strlen($_SESSION["ODOSessionO"]->EscapedVars["sDate"]) > 0)){
			
				$sDate = date("Y-m-d", strtotime($_SESSION["ODOSessionO"]->EscapedVars["sDate"]));
			
			}
			
			if((isset($_SESSION["ODOSessionO"]->EscapedVars["eDate"]))&&
				(strlen($_SESSION["ODOSessionO"]->EscapedVars["eDate"]) > 0)){
			
				$eDate = date("Y-m-d", strtotime($_SESSION["ODOSessionO"]->EscapedVars["eDate"]));
			}
			
			$Posts = $this->NewsDBO->getAllPosts($catId, $sDate, $eDate);
			
		}
		
		if(count($Posts) > 0) {
		
			$newsContent .= "<div id=\"three-column\" >\n";
			
			foreach($Posts as $newPost) {
				

				date_default_timezone_set('America/Chicago');
					
				//format date
				$datefromdb = $newPost->PostDate;
				$year = substr($datefromdb,0,4);
				$mon  = substr($datefromdb,5,2);
				$day  = substr($datefromdb,8,2);
				$mktimeDate = mktime( 0, 0, 0,$mon, $day, $year);
					
				$mon = date('M', $mktimeDate);
				$day = date('d', $mktimeDate);
				$year = date('Y', $mktimeDate);
				
				$newsContent .= "<div class=\"row\">\n<div class=\"col-md-2\"></div>";
				$newsContent .= "<div class=\"col-md-8\" id=\"wrapper-bg\">\n<ul class=\"style3\">\n";
				$newsContent .= "<li><p class=\"date\"><a href=\"#\">{$mon} {$day} {$year}</a></p><h2>";
				$newsContent .= "{$newPost->Title}</h2></li></ul></div><div class=\"col-md-2\"></div></div>\n";
				
				$newsContent .= "<div class=\"row\"><div class=\"col-md-2\"></div><div class=\"col-md-8 NewsBlock\"><p>";
				$newsContent .= "{$newPost->Article}</p></div><div class=\"col-md-2\"></div></div>\n";
			
			}
		
			$newsContent .= "</div>\n";
		}
		
		return $newsContent;
		
	}
	
	
	private function FormatPost($Article) {
		
		return strip_tags(substr($Article, 0, 50), '<p><a><b>');
		
	}
	
	//begin footer

	function __sleep() {
		
		$this->NewsDBO = null;
		
		
		return( array_keys( get_object_vars( $this ) ) );
	}
	
	
	function __wakeup() {
		//check for registered object.
		if(!$_SESSION["ODOSessionO"]->IsRegistered("NewsDBO")) {
			$this->NewsDBO = new NewsDB();
			$_SESSION["ODOSessionO"]->RegisterObject("NewsDBO", $this->NewsDBO);

		} else {
			$this->NewsDBO = $_SESSION["ODOSessionO"]->GetObjectRef("NewsDBO");
		}
		
	}
}


?>