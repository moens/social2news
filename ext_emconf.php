<?php

########################################################################
# Extension Manager/Repository config file for ext "social2news".
#
# Auto generated 27-05-2011 14:30
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Social2News',
	'description' => 'Import Facebook wall, Twitter Recent Tweets, Twitter Saved Searches, Flickr Search, Wordpress RSS into tt_news',
	'category' => 'services',
	'author' => 'Sy Moen',
	'author_email' => 'tech@gallupcurrent.com',
	'shy' => '',
	'dependencies' => 'cms,svconnector,svconnector_social',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.1.7',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'svconnector' => '',
			'svconnector_social' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:61:{s:9:"ChangeLog";s:4:"9ef7";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"d41d";s:14:"ext_tables.php";s:4:"c09c";s:14:"ext_tables.sql";s:4:"52c0";s:16:"locallang_db.xml";s:4:"ef81";s:18:"Classes/Api/README";s:4:"85ff";s:29:"Classes/Api/php-sdk/readme.md";s:4:"5998";s:40:"Classes/Api/php-sdk/examples/example.php";s:4:"b1e5";s:36:"Classes/Api/php-sdk/src/facebook.php";s:4:"df78";s:46:"Classes/Api/php-sdk/src/fb_ca_chain_bundle.crt";s:4:"c305";s:35:"Classes/Api/php-sdk/tests/tests.php";s:4:"de86";s:32:"Classes/Api/phpflickr/README.txt";s:4:"0e8f";s:30:"Classes/Api/phpflickr/auth.php";s:4:"2e21";s:33:"Classes/Api/phpflickr/example.php";s:4:"4eb3";s:34:"Classes/Api/phpflickr/getToken.php";s:4:"42a5";s:35:"Classes/Api/phpflickr/phpFlickr.php";s:4:"9f62";s:33:"Classes/Api/phpflickr/PEAR/DB.php";s:4:"34a9";s:35:"Classes/Api/phpflickr/PEAR/PEAR.php";s:4:"b67d";s:40:"Classes/Api/phpflickr/PEAR/DB/common.php";s:4:"5100";s:39:"Classes/Api/phpflickr/PEAR/DB/mysql.php";s:4:"331e";s:39:"Classes/Api/phpflickr/PEAR/DB/pgsql.php";s:4:"bbdc";s:41:"Classes/Api/phpflickr/PEAR/DB/storage.php";s:4:"06ae";s:43:"Classes/Api/phpflickr/PEAR/HTTP/Request.php";s:4:"fe38";s:52:"Classes/Api/phpflickr/PEAR/HTTP/Request/Listener.php";s:4:"2a4f";s:41:"Classes/Api/phpflickr/PEAR/Net/Socket.php";s:4:"567d";s:38:"Classes/Api/phpflickr/PEAR/Net/URL.php";s:4:"a7ee";s:28:"Classes/Api/tmhOAuth/LICENSE";s:4:"3b83";s:30:"Classes/Api/tmhOAuth/README.md";s:4:"8b43";s:33:"Classes/Api/tmhOAuth/tmhOAuth.php";s:4:"92cc";s:38:"Classes/Api/tmhOAuth/examples/auth.php";s:4:"f13f";s:47:"Classes/Api/tmhOAuth/examples/auto_fix_time.php";s:4:"5c35";s:42:"Classes/Api/tmhOAuth/examples/entities.php";s:4:"bde6";s:40:"Classes/Api/tmhOAuth/examples/images.php";s:4:"c81f";s:44:"Classes/Api/tmhOAuth/examples/oauth_echo.php";s:4:"92d8";s:37:"Classes/Api/tmhOAuth/examples/oob.php";s:4:"4283";s:37:"Classes/Api/tmhOAuth/examples/rss.php";s:4:"4a1e";s:43:"Classes/Api/tmhOAuth/examples/streaming.php";s:4:"d535";s:39:"Classes/Api/tmhOAuth/examples/tweet.php";s:4:"2a9a";s:40:"Classes/Api/tmhOAuth/examples/verify.php";s:4:"77f6";s:39:"Classes/Api/tmhOAuth/examples/xauth.php";s:4:"454b";s:38:"Classes/Api/twitteroauth/DOCUMENTATION";s:4:"cd3e";s:32:"Classes/Api/twitteroauth/LICENSE";s:4:"a205";s:31:"Classes/Api/twitteroauth/README";s:4:"7fc6";s:37:"Classes/Api/twitteroauth/callback.php";s:4:"2423";s:42:"Classes/Api/twitteroauth/clearsessions.php";s:4:"21fa";s:35:"Classes/Api/twitteroauth/config.php";s:4:"befa";s:36:"Classes/Api/twitteroauth/connect.php";s:4:"732c";s:33:"Classes/Api/twitteroauth/html.inc";s:4:"c4f4";s:34:"Classes/Api/twitteroauth/index.php";s:4:"d67e";s:37:"Classes/Api/twitteroauth/redirect.php";s:4:"45b3";s:33:"Classes/Api/twitteroauth/test.php";s:4:"ddc6";s:42:"Classes/Api/twitteroauth/images/darker.png";s:4:"0b2e";s:43:"Classes/Api/twitteroauth/images/lighter.png";s:4:"32bb";s:47:"Classes/Api/twitteroauth/twitteroauth/OAuth.php";s:4:"d645";s:54:"Classes/Api/twitteroauth/twitteroauth/twitteroauth.php";s:4:"9a40";s:49:"Classes/Controller/class.tx_social2news_hooks.php";s:4:"d41d";s:54:"Classes/Controller/class.tx_social2news_transforms.php";s:4:"b6b0";s:19:"doc/wizard_form.dat";s:4:"6cb4";s:20:"doc/wizard_form.html";s:4:"8139";}',
);

?>
