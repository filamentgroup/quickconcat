<?php

require_once dirname(__DIR__). '/testBase.php';

class relativeUrls extends quickconcat_basetest
{
	public function testUrls()
	{
		$expected =
"@font-face {
	font-family: Route; src: url(//test/assets/Route.woff);
}

div.absolute {
	background: url('/woaar/yes.png') repeat-y bottom;
}
span.absolute {
	background: url(/pics/item.jpeg) repeat-y bottom;
}
div.absolute .abc {
	background: url(\"/img/a/img.png\") repeat-y bottom;
}
div.relative {
	background: url(//test/assets/../img/layout/warehouse.png) repeat-y bottom;
}
";
	$this->assertEquals(
		$expected,
		$this->get('quickconcat.php?files=test/assets/url.css'));
}
}
