<?php

abstract class quickconcat_basetest extends PHPUnit_Framework_TestCase
{	
	protected function get($url) {
		return file_get_contents('http://localhost:8888/'. $url);
	}
}