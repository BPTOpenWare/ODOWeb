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
 * Basic POPO for testing the session object CSRF logic. 
 * @author nict
 *
 */
class TestCSRFObject {
	
	private $testVarOne;
	
	private $testVarTwo;
	
	function __construct() {
		
		$this->testVarOne = "testone";
		$this->testVarTwo = "testtwo";
	}
	
	public function getTestVarOne() {
		return $this->testVarOne;
	}
	
	public function getTestVarTwo() {
		return $this->testVarTwo;
	}
	
	//@ODOWebHeaderEnd
	
	/**
	 * @ODOWebMethodGroups :admin:
	 * @ODOWebExposed
	 */
	public function getRequestTestTwo() {
		
		$this->testVarTwo = "CallOk";
	}
	
	/**
	 * @ODOWebMethodGroups :admin:
	 * @ODOWebExposed
	 */
	public function getRequestTestOne() {
		
		$this->testVarOne = "CallOk";
	}
	
	/**
	 * @ODOWebFooterStart
	 */
	function __sleep() {
		return( array_keys( get_object_vars( $this ) ) );
	}
	
}

?>