<?php 
class tx_social2news_api_urlToolsTest extends PHPUnit_Framework_TestCase 
{ 

	/**
	 * @dataProvider urlProvider
	 */
	public function testValidateUrl($url, $bool) 
	{
		$this->assertTrue(validateUrl($url) && $bool);
		 
	} 
	public function urlProvider() 
	{
		return array (
			array('http://domain.com', true),
			array('https://domain.com', true),
			array('ftp.domain.com', true),
			array('domain.com', true),
			array('domain.communist', false),
			array('domain.com?param=1', true),
			array('domain.com?param=1&you=2', true)
		);
	} 
	
	public function testHarvestUrls() 
	{
		$this->assertTrue() 
	} 
} 
?> 
