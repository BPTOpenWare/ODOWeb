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
	
	
	function GetLastPost() {
		
		$Post = $this->NewsDBO->GetLastPost();
		$ArticleStripped = $this->FormatPost($Post->Article);
		
		$content = "<div id=\"banner_title\">{$Post->Title}</div>\n<p>{$ArticleStripped}</p>\n<div class=\"more_button\"><a href=\"index.php?pg=News&PostID={$Post->PostID}\">Read More</a></div>";
		
		return $content;
	}
	
	
	private function FormatPost($Article) {
		
		return strip_tags(substr($Article, 0, 700), '<p><a>');
		
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