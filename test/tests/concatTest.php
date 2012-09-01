<?php

require dirname(__DIR__). '/testBase.php';

class quickconcat extends quickconcat_basetest
{	
	public function testBasic()
	{
		$this->assertEquals(
			$this->get('quickconcat.php?files=test/assets/a.html'),
			'<h1>hello</h1>');
	}
	
	public function testConcatHtml()
	{
		$this->assertEquals(
			$this->get('quickconcat.php?files=test/assets/a.html,test/assets/b.html,test/assets/c.html'),
			'<h1>hello</h1><p>world</p><h3>quickconcat</h3>');
	}
	
	public function testConcatCss()
	{
		$this->assertEquals(
			$this->get('quickconcat.php?files=test/assets/a.css,test/assets/b.css,test/assets/c.css'),
			'h1 { font-weight: bold }p { font-weight: bold }h3 { font-weight: bold }');
	}
	
	public function testConcatJs()
	{
		$this->assertEquals(
			$this->get('quickconcat.php?files=test/assets/a.js,test/assets/b.js,test/assets/c.js'),
			'var a = "a";var b = "b";var c = "c";');
	}
}
