<?php

require dirname(__DIR__). '/testBase.php';

class quickconcat extends quickconcat_basetest
{	
	public function testBasic()
	{
		$this->assertEquals(
			'<h1>hello</h1>',
			$this->get('quickconcat.php?files=test/assets/a.html'));
	}
	
	public function testConcatHtml()
	{
		$this->assertEquals(
			'<h1>hello</h1><p>world</p><h3>quickconcat</h3>',
			$this->get('quickconcat.php?files=test/assets/a.html,test/assets/b.html,test/assets/c.html'));
	}
	
	public function testConcatCss()
	{
		$this->assertEquals(
			'h1 { font-weight: bold }p { font-weight: bold }h3 { font-weight: bold }',
			$this->get('quickconcat.php?files=test/assets/a.css,test/assets/b.css,test/assets/c.css'));
	}
	
	public function testConcatJs()
	{
		$this->assertEquals(
			"var a = \"a\";\nvar b = \"b\";\nvar c = \"c\";\n",
			$this->get('quickconcat.php?files=test/assets/a.js,test/assets/b.js,test/assets/c.js'));
	}
}
