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

class ODOMenu {

	var $Position;
	var $NameArray;
	var $URLArray;
	var $ChildrenArray;
	var $ParentArray;
	var $IsHeadArray;
	var $PrevUID;
	var $DepVarArray;

	function __construct() {
		//check if UID is set
		$this->Position = 0;
		$this->NameArray = array();
		$this->URLArray = array();
		$this->ChildrenArray = array(array());
		$this->IsHeadArray = array();
		$this->ParentArray = array();
		$this->DepVarArray = array();

		if($_SESSION["ODOUserO"]->getLoggedIn()) {
			$this->PrevUID = $_SESSION["ODOUserO"]->getUID();
			$this->LoadMenu();

		}

	}

	private function LoadMenu() {
		//first get UID's groups
		//next pull all menus group has access rights to
		//filter out duplicates. 
		$query = "SELECT DISTINCT ODOTree.URL, ODOTree.LinkName, ODOTree.ParentID, ODOTree.HeadTag, ODOTree.PriKey, ODOTree.ChildPos, ODOTree.DependVar FROM ODOTree, ODOMenus, ODOUserGID WHERE ODOUserGID.UID = " . $this->PrevUID . " AND ODOUserGID.GID = ODOMenus.GID AND ODOMenus.StaticLinkID = ODOTree.PriKey";

		$result = $GLOBALS['globalref'][1]->Query($query);
	
		while($row = mysqli_fetch_assoc($result)) 
		{
			if(strlen($row["HeadTag"]) > 0) {
				$this->IsHeadArray[$row["HeadTag"]] = $row["PriKey"];
			}
			
			$this->NameArray[$row["PriKey"]] = $row["LinkName"];
			$this->URLArray[$row["PriKey"]] = $row["URL"];
			
			if((!is_null($row["ParentID"]))&&($row["ParentID"] != 0)) {
				$this->ChildrenArray[$row["ParentID"]][$row["PriKey"]] = $row["ChildPos"];
				$this->ParentArray[$row["PriKey"]] = $row["ParentID"];
			}
			
			if(isset($row["DependVar"]) ) {
				$this->DepVarArray[$row["PriKey"]] = $row["DependVar"];
			}
			
		}
		
		//sort all children arrays
		foreach($this->NameArray as $ID=>$Value) {
			if(isset($this->ChildrenArray[$ID])) {
				asort($this->ChildrenArray[$ID], SORT_NUMERIC);
			}
		}
		
		
	}



	function GetMenu($LinkID, $Levels, $ChildFlag = false) {

		if($Levels > 99) {
			//per the PHP documentation: Recursive functions that are called 100 times or more
			//can crash the stack.
			return "Error building tree";
		}

		$RValue = "";

		if(isset($this->NameArray[$LinkID])) {
		
			
			if($Levels == 0) {
				$RValue = "<li><a href=\"" . $this->URLArray[$LinkID] . "\">" . $this->NameArray[$LinkID] . "</a></li>\n";
				return $RValue;
			} else {
				if(!$ChildFlag) {
					$RValue = "<ul>";
				}
			
				if(strlen($this->URLArray[$LinkID]) > 0) {
					$RValue = $RValue . "<li><a href=\"" . $this->URLArray[$LinkID] . "\">" . $this->NameArray[$LinkID] . "</a></li>";
				} else {
					$RValue = $RValue . "<li>" . $this->NameArray[$LinkID] . "</li>";
				}
				
				if(isset($this->ChildrenArray[$LinkID])) {
					$RValue = $RValue . "<ul>";

					foreach($this->ChildrenArray[$LinkID] as $ID=>$Value){
						$RValue = $RValue . $this->GetMenu($ID, ($Levels-1), 1);
					}
					
					$RValue = $RValue . "</ul>";
				}

				if(!$ChildFlag) {
					$RValue = $RValue . "</ul>";
				}
			}
		} else {
			return $RValue;
		}			
		
		return $RValue;
	}

	function GetPathFromLastPosition() {
		$RValue = "";
		$tempPos = $this->Position;
		while(isset($this->ParentArray[$tempPos])) {

			if(strlen($this->NameArray[$tempPos]) > 0) {
				
				$RValue = $this->NameArray[$tempPos] . $RValue;	
				if(strlen($this->URLArray[$LinkID]) > 0) {
					$RValue = "<a href=\"" . $this->URLArray[$LinkID] . "\">" . $RValue . "</a>";
				}
				$RValue = ">" . $RValue;

			} //else we do nothing since only Tag is set then
			$tempPos = $this->ParentArray[$tempPos];
		}
		
		return $RValue;
	}

	function SetPosition($NewPos) {
		
		if(is_numeric($NewPos)) {
			$this->Position = $NewPos;
		}

	}

	function FullMenu($HeadTag) {
		$RValue = "";
		//get position
		$Pos = 0;
		$tempArray = array();
		$LevelCount = 0;
		$LevelArray = array();

		if(isset($this->IsHeadArray[$HeadTag])) {
			$RValue = "<ul>";
			$Pos = $this->IsHeadArray[$HeadTag];
			array_push($tempArray, $Pos);
			$LevelArray[$LevelCount] = 0;

			while(count($tempArray) > 0) {
				$Pos = array_pop($tempArray);
				$RValue = $RValue . "<li>";
				if(strlen($this->URLArray[$Pos]) > 0) {
					$RValue = $RValue . "<a href=\"" . $this->URLArray[$Pos] . "\">" . $this->NameArray[$Pos] . "</a>";
				} else {
					$RValue = $RValue . $this->NameArray[$Pos];
				}
			
				if(isset($this->ChildrenArray[$Pos])) {
					$RValue = $RValue . "<UL>";
					$LevelCount = $LevelCount + 1;
					foreach($this->ChildrenArray[$Pos] as $ID=>$Value){
						array_push($tempArray, $ID);
						$LevelArray[$LevelCount] = $LevelArray[$LevelCount] + 1;
					}

				} else {
					if($LevelArray[$LevelCount] > 1) {
						$RValue = $RValue . "</LI>";
						$LevelArray[$LevelCount] = $LevelArray[$LevelCount] - 1;
					} else {
						$LevelArray[$LevelCount] = $LevelArray[$LevelCount] - 1;
						$LevelCount = $LevelCount - 1;
						$RValue = $RValue . "</LI></UL>";
					}
				}

			}
			
			return $RValue;

		} else {
			return $RValue;
		}
		
		
	}

	function OneLevel($PageID) {
		$RValue = GetMenu($PageID, 1);
		return $RValue;
	}

	function GetODOLeftMenu($HeadTag) {

		$i = 0;
		$RValue = "<div class=\"ODOmenuLeft\">\n<ul>";
	
		if(isset($this->IsHeadArray[$HeadTag])) {
			foreach($this->ChildrenArray[$this->IsHeadArray[$HeadTag]] as $ID=>$Value) {
				if($i == 0) {
					$RValue = $RValue . "\n<li class=\"menuLeftHead\">";
				} else {
					$RValue = $RValue . "\n<li>";
				}
				
				$RValue = $RValue . "<a id=\"leftsubmenu" . $i . "a\" href=\"javascript:leftmOpenClose('leftsubmenu" . $i . "', 'leftsubmenu" . $i . "a')\">" . $this->NameArray[$ID] . "</a>";
				
				
				if(isset($this->ChildrenArray[$ID])) {
					$RValue = $RValue . "\n<ul id=\"leftsubmenu" . $i . "\">";
					foreach($this->ChildrenArray[$ID] as $Child=>$CValue) {
						$RValue = $RValue . "\n<li>";
						if(isset($this->URLArray[$Child])) {
							$RValue = $RValue . "<a href=\"" . $this->URLArray[$Child] . "\">" . $this->NameArray[$Child] . "</a>\n";
						} else {
							$RValue = $RValue . $this->NameArray[$Child];
						}
						$RValue = $RValue . "\n</li>";
					}
					$RValue = $RValue . "\n</ul>";
				}
				$RValue = $RValue . "</li>";
				$i++;
			}

		}
		
		$RValue = $RValue . "</ul></div>\n";

		//check if user is logged in. If they are then post logout and admin function link
		//if they aren't then post link to login if on admin page? If not on Admin page then check for flag
		if(($_SESSION["ODOUserO"]->getLoggedIn()) && (!$_SESSION["ODOUserO"]->getIsGuest())) {
			$RValue = $RValue . "<br><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"DefaultLogOut\"><input type=\"hidden\" name=\"ob\" value=\"ODOUserO\"><input type=\"hidden\" name=\"fn\" value=\"LogoutPublic\"><input type=\"hidden\" name=\"ObjectOnlyOutput\" value=\"1\"><input type=\"hidden\" name=\"PromptUser\" value=\"1\"><input type=\"submit\" name=\"Logout\" value=\"Logout\"></form><br>";
		} elseif((!$_SESSION["ODOPageO"]->IsAdmin) && (ALLOWUSERANYPAGELOGIN)) {
			$RValue = $RValue . "<br><br><form action=\"index.php\" enctype=\"application/x-www-form-urlencoded\" method=\"POST\"><input type=\"hidden\" name=\"pg\" value=\"" . $_SESSION["ODOPageO"]->PageID . "\"><input type=\"hidden\" name=\"ob\" value=\"ODOUserO\"><input type=\"hidden\" name=\"fn\" value=\"Login\"><input type=\"hidden\" name=\"ObjectOnlyOutput\" value=\"1\"><input type=\"submit\" name=\"Login\" value=\"Login\"></form>";
		} else {
			//we don't want to do anything otherwise
		}

		return $RValue;
	}

	function GetODOTopMenu($HeadTag) {

		$RValue = "<B>MENU TAG NOT FOUND</B>";		
		$DepVar = false;
		$DepVarSet = false;
		
		
		if(isset($this->IsHeadArray[$HeadTag])) {

			$RValue = "<div class=\"ODOmenuTopSt\">\n<ul>\n";
			//level 1
			foreach($this->ChildrenArray[$this->IsHeadArray[$HeadTag]] as $ID=>$Value) {
				//reset
				$DepVar = false;
				$DepVarSet = false;
				$DepVar2 = false;
				$DepVarSet2 = false;
				$DepVar3 = false;
				$DepVarSet3 = false;
				
				//check for conditional var
				if(isset($this->DepVarArray [$ID])) {
						$DepVar = true;
				} 
				
				if($DepVar) {
			
					if(isset($_SESSION["ODOSessionO"]->EscapedVars[$this->DepVarArray[$ID]])) {
						$DepVarSet = true;
					}
					
				}
					
				if((!$DepVar)||(($DepVar)&&($DepVarSet))) {
					
					$RValue = $RValue . "<li>";
					
				
					if(strlen($this->URLArray[$ID]) > 0) {
						$RValue = $RValue . "<a href=\"" . $this->URLArray[$ID];
						if($DepVarSet) {
							$RValue = $RValue . "&" . $this->DepVarArray[$ID] . "=" . $_SESSION["ODOUtil"]->ODOUrlEncode($this->DepVarArray[$ID]);
						}
						$RValue = $RValue . "\">" . $this->NameArray[$ID] . "</a>";
					} else {
						$RValue = $RValue . "<a href=\"#\" >" . $this->NameArray[$ID] . "</a>";
					}

					if(isset($this->ChildrenArray[$ID])) {
						$RValue = $RValue . "\n<ul>";
						//level 2
						foreach($this->ChildrenArray[$ID] as $Child=>$CValue) {
							//reset this level
							$DepVar2 = false;
							$DepVarSet2 = false;
								
							//check for conditional var
							if(isset($this->DepVarArray [$Child])) {
								$DepVar2 = true;
							} 
				
							if($DepVar2) {
			
								if(isset($_SESSION["ODOSessionO"]->EscapedVars[$this->DepVarArray[$Child]])) {
									$DepVarSet2 = true;
								}
					
							}
					
							if((!$DepVar2)||(($DepVar2)&&($DepVarSet2))) {
								$RValue = $RValue . "\n<li>";
								if(isset($this->URLArray[$Child])) {
									$RValue = $RValue . "<a href=\"" . $this->URLArray[$Child];
									if($DepVarSet2) {
										$RValue = $RValue . "&" . $this->DepVarArray[$Child] . "=" . $_SESSION["ODOUtil"]->ODOUrlEncode($this->DepVarArray[$Child]);
									}
									$RValue = $RValue . "\">" . $this->NameArray[$Child] . "</a>";
								} else {
									$RValue = $RValue . "<a href=\"#\">" . $this->NameArray[$Child] . "</a>" ;
								}
						
								if(isset($this->ChildrenArray[$Child])) {
									$RValue = $RValue . "\n<ul>";
									//level 3
									foreach($this->ChildrenArray[$Child] as $ChildThree=>$CValueThree) {
										//reset this level
										$DepVar3 = false;
										$DepVarSet3 = false;
										
										//check for conditional var
										if(isset($this->DepVarArray [$ChildThree])) {
											$DepVar3 = true;
										} 
				
										if($DepVar3) {
			
											if(isset($_SESSION["ODOSessionO"]->EscapedVars[$this->DepVarArray[$ChildThree]])) {
												$DepVarSet3 = true;
											}
					
										}
					
										if((!$DepVar3)||(($DepVar3)&&($DepVarSet3))) {
			
											$RValue = $RValue . "\n<li>";
											if(isset($this->URLArray[$ChildThree])) {
												$RValue = $RValue . "<a href=\"" . $this->URLArray[$ChildThree];
												if($DepVarSet3) {
													$RValue = $RValue . "&" . $this->DepVarArray[$ChildThree] . "=" . $_SESSION["ODOUtil"]->ODOUrlEncode($this->DepVarArray[$ChildThree]);
												}
												$RValue = $RValue . "\">" . $this->NameArray[$ChildThree] . "</a>";
											} else {
												$RValue = $RValue . "<a href=\"#\">" . $this->NameArray[$ChildThree] . "</a>" ;
											}
						
											$RValue = $RValue . "</li>";
										}
									}
									
									$RValue = $RValue . "\n</ul>";
								}
								$RValue = $RValue . "</li>";
							}
						}
						$RValue = $RValue . "\n</ul>";
					}
					$RValue = $RValue . "</li>";
				}
				
			
			}
			
			$RValue = $RValue . "</ul></div>\n";
		}
		
		return $RValue;
		
	}

//header


	function GetODOMuseTopMenu($HeadTag) {

		$RValue = null;		
		$DepVar = false;
		$DepVarSet = false;
		$TopLevelMenuCounter = 0;
		
		if(isset($this->IsHeadArray[$HeadTag])) {

			$RValue = "<div class=\"ODOMuseMenu\">\n<ul>\n";
			//level 1
			foreach($this->ChildrenArray[$this->IsHeadArray[$HeadTag]] as $ID=>$Value) {
				//reset
				$DepVar = false;
				$DepVarSet = false;
				$DepVar2 = false;
				$DepVarSet2 = false;
				$DepVar3 = false;
				$DepVarSet3 = false;
				
				//check for conditional var
				if(isset($this->DepVarArray [$ID])) {
						$DepVar = true;
				} 
				
				if($DepVar) {
			
					if(isset($_SESSION["ODOSessionO"]->EscapedVars[$this->DepVarArray[$ID]])) {
						$DepVarSet = true;
					}
					
				}
					
				if((!$DepVar)||(($DepVar)&&($DepVarSet))) {
					
					if($TopLevelMenuCounter == (count($this->ChildrenArray[$this->IsHeadArray[$HeadTag]] ) - 1)) {
						$RValue = $RValue . "<li class=\"lastmenu\">";
					} else {
						$RValue = $RValue . "<li>";
					}
				
					if(strlen($this->URLArray[$ID]) > 0) {
						$RValue = $RValue . "<a href=\"" . $this->URLArray[$ID];
						if($DepVarSet) {
							$RValue = $RValue . "&" . $this->DepVarArray[$ID] . "=" . $_SESSION["ODOUtil"]->ODOUrlEncode($this->DepVarArray[$ID]);
						}
						$RValue = $RValue . "\">" . $this->NameArray[$ID] . "</a>";
					} else {
						$RValue = $RValue . "<a href=\"#\" >" . $this->NameArray[$ID] . "</a>";
					}

					if(isset($this->ChildrenArray[$ID])) {
						$RValue = $RValue . "\n<ul>";
						//level 2
						foreach($this->ChildrenArray[$ID] as $Child=>$CValue) {
							//reset this level
							$DepVar2 = false;
							$DepVarSet2 = false;
								
							//check for conditional var
							if(isset($this->DepVarArray [$Child])) {
								$DepVar2 = true;
							} 
				
							if($DepVar2) {
			
								if(isset($_SESSION["ODOSessionO"]->EscapedVars[$this->DepVarArray[$Child]])) {
									$DepVarSet2 = true;
								}
					
							}
					
							if((!$DepVar2)||(($DepVar2)&&($DepVarSet2))) {
								$RValue = $RValue . "\n<li>";
								if(isset($this->URLArray[$Child])) {
									$RValue = $RValue . "<a href=\"" . $this->URLArray[$Child];
									if($DepVarSet2) {
										$RValue = $RValue . "&" . $this->DepVarArray[$Child] . "=" . $_SESSION["ODOUtil"]->ODOUrlEncode($this->DepVarArray[$Child]);
									}
									$RValue = $RValue . "\">" . $this->NameArray[$Child] . "</a>";
								} else {
									$RValue = $RValue . "<a href=\"#\">" . $this->NameArray[$Child] . "</a>" ;
								}
						
								if(isset($this->ChildrenArray[$Child])) {
									$RValue = $RValue . "\n<ul>";
									//level 3
									foreach($this->ChildrenArray[$Child] as $ChildThree=>$CValueThree) {
										//reset this level
										$DepVar3 = false;
										$DepVarSet3 = false;
										
										//check for conditional var
										if(isset($this->DepVarArray [$ChildThree])) {
											$DepVar3 = true;
										} 
				
										if($DepVar3) {
			
											if(isset($_SESSION["ODOSessionO"]->EscapedVars[$this->DepVarArray[$ChildThree]])) {
												$DepVarSet3 = true;
											}
					
										}
					
										if((!$DepVar3)||(($DepVar3)&&($DepVarSet3))) {
			
											$RValue = $RValue . "\n<li>";
											if(isset($this->URLArray[$ChildThree])) {
												$RValue = $RValue . "<a href=\"" . $this->URLArray[$ChildThree];
												if($DepVarSet3) {
													$RValue = $RValue . "&" . $this->DepVarArray[$ChildThree] . "=" . $_SESSION["ODOUtil"]->ODOUrlEncode($this->DepVarArray[$ChildThree]);
												}
												$RValue = $RValue . "\">" . $this->NameArray[$ChildThree] . "</a>";
											} else {
												$RValue = $RValue . "<a href=\"#\">" . $this->NameArray[$ChildThree] . "</a>" ;
											}
						
											$RValue = $RValue . "</li>";
										}
									}
									
									$RValue = $RValue . "\n</ul>";
								}
								$RValue = $RValue . "</li>";
							}
						}
						$RValue = $RValue . "\n</ul>";
					}
					$RValue = $RValue . "</li>";
				}
				
				$TopLevelMenuCounter++;
			}
			
			$RValue = $RValue . "</ul></div>\n";
		}
		
		return $RValue;
		
	}

	/**
	 * Builds menu for mobile users
	 */
	function getODOMuseMobile($HeadTag) {
		
		$RValue = null;
		$TopLevelMenuCounter = 0;
		
		if(isset($this->IsHeadArray[$HeadTag])) {
		
			$RValue = "";
			
			//level 1
			foreach($this->ChildrenArray[$this->IsHeadArray[$HeadTag]] as $ID=>$Value) {
				//reset
		
				$RValue .= "<a href=\"{$this->URLArray[$ID]}\" class=\"ui-btn\">{$this->NameArray[$ID]}</a>";
				
			}
			
			//add search form
			$RValue .= "<div id=\"search\" data-role=\"collapsible\">\n<h1>Search</h1>\n<p>\n\n<form method=\"get\" action=\"index.php\">";
			$RValue .= "<input type=\"hidden\" name=\"pg\" value=\"msearch\">\n";
			$RValue .= "<div><label>Search Artists:</label>&nbsp;<input class=\"inputfield\" name=\"keyword\" type=\"text\" id=\"keyword\" size=\"15\"/></div>\n";
			$RValue .= "<input class=\"button\" type=\"submit\" name=\"Search\" value=\"Search\" />\n</form>\n</p>\n</div>";
			
			
			if($_SESSION["ODOUserO"]->isNonGuestLogin()) {
				
				$RValue .= "<FORM action=\"index.php\" name=\"loginform\" method=\"POST\" data-ajax=\"false\" enctype=\"application/x-www-form-urlencoded\">\n";
				$RValue .= "<input type=\"hidden\" name=\"ObjectOnlyOutput\" value=\"1\">\n";
				$RValue .= "<input type=\"hidden\" name=\"ob\" value=\"ODOUserO\">\n";
				$RValue .= "<input type=\"hidden\" name=\"fn\" value=\"LogoutPublic\">\n";
				$RValue .= "<input type=\"submit\" name=\"Logout\" class=\"ui-btn ui-icon-lock ui-btn-icon-left\" value=\"Logout\"></form>\n";
				
			} else {
				
				$RValue .= "<a href=\"login.php\" data-ajax=\"false\" class=\"ui-btn ui-icon-lock ui-btn-icon-left\">Log In</a>\n";
				$RValue .= "<a href=\"index.php?pg=mRegister\" class=\"ui-btn\">Regsiter</a>\n";
				
			}
			
			$RValue .= "<a href=\"../index.php?overrideMobile=true\" data-ajax=\"false\">Load Full Site</a>\n";
			
			
		}
		
		return $RValue;
	}
	
	
//footer
	function __wakeup() {
		if(!$_SESSION["ODOUserO"]->getLoggedIn()) {
			//then unset everything
			
			unset($this->Position);
			unset($this->NameArray);
			unset($this->URLArray);
			unset($this->ChildrenArray);
			unset($this->IsHeadArray);
			unset($this->PrevUID);
			unset($this->ParentArray);

		} else {
			
			if($this->PrevUID != $_SESSION["ODOUserO"]->getUID()) {
				unset($this->Position);
				unset($this->NameArray);
				unset($this->URLArray);
				unset($this->ChildrenArray);
				unset($this->IsHeadArray);
				unset($this->PrevUID);
				unset($this->ParentArray);

				$this->Position = 0;
				$this->NameArray = array();
				$this->URLArray = array();
				$this->ChildrenArray = array(array());
				$this->IsHeadArray = array();
				$this->ParentArray = array();

				$this->PrevUID = $_SESSION["ODOUserO"]->getUID();
				$this->LoadMenu();
			}

		}

	}



}

?>