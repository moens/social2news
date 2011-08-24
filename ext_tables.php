<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Initialize

t3lib_extMgm::addStaticFile($_EXTKEY,'static/','s2n import layout');

/**
 * Save a little typing here
 *
 */

$transformClass = 'EXT:social2news/Classes/Controller/class.tx_social2news_controller_transforms.php:&tx_social2news_controller_transforms';

	/**
	 * tt_news TCA setup additional fields
	 *
	 */

$tempColumns = array (
	'tx_social2news_external' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:social2news/locallang_db.xml:tt_news.tx_social2news_external',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_social2news_external_source' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:social2news/locallang_db.xml:tt_news.tx_social2news_external_source',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
);


t3lib_div::loadTCA('tt_news');
//t3lib_extMgm::addTCAcolumns('tt_news',$tempColumns,1);
// these fields should not be needed for backend input, so no need to add them to flexforms I think...
//t3lib_extMgm::addToAllTCAtypes('tt_news','tx_social2news_external;;;;1-1-1, tx_social2news_external_source');
//t3lib_extMgm::addToAllTCAtypes('tt_news','tx_social2news_external;;;;1-1-1, tx_social2news_external_source', '', 'after:short');

// setup for the tx_social2news_author col (ripped from news_author_rel extension
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['social2news']);
error_log(json_encode($confArr));
$foreign_table = 'fe_users';
$fieldHeight = $confArr['fieldHeight'];
$fieldType = $confArr['fieldType'];
	
	// get pid of the page where the author records are stored
$pid = '###CURRENT_PID###'; 	// don't use storagePid
$StoragePid = intval($confArr['StoragePid']);

if ($confArr['useStoragePid']) {
	$pid = '###STORAGE_PID###';
	$fTableWhere = 'AND '.$foreign_table.'.pid='.$pid.' ';
}

if ($StoragePid) {
	$pid = $StoragePid;
	$fTableWhere = 'AND '.$foreign_table.'.pid='.$pid.' ';
}
if ($fieldType == 'group') {
	$tempColumns['tx_social2news_author'] = Array (
			'exclude' => 1,
			'label' => ('LLL:EXT:social2news/locallang_db.php:tt_news.tx_social2news_author'),
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => $foreign_table,
				'size' => $fieldHeight,
				'autosizeMax' => 10,
				'minitems' => 0,
				'maxitems' => 10,
				'show_thumbs' => 1,
			)
		);
} else {
	$tempColumns['tx_social2news_author'] = Array (
			'exclude' => 1,
			'label' => ('LLL:EXT:social2news/locallang_db.php:tt_news.tx_social2news_author'),
			'config' => Array (
				'type' => 'select',
				'foreign_table' => $foreign_table,
				'foreign_table_where' => $fTableWhere.'ORDER BY '.$foreign_table.'.uid',
				'size' => $fieldHeight,
				'autosizeMax' => 10,
				'minitems' => 0,
				'maxitems' => 10,
			)
		);
}

$tempColumns['tx_social2news_author']['config']['wizards'] = Array(
		'_PADDING' => 2,
		'_VERTICAL' => 1,
		'add' => Array(
			'type' => 'script',
			'title' => 'Create new ' . $foreign_table . ' record',
			'icon' => 'add.gif',
			'params' => Array(
				'table'=> $foreign_table,
				'pid' => $pid,
				'setValue' => 'set'
			),
			'script' => 'wizard_add.php',
		),
		'list' => Array(
			'type' => 'script',
			'title' => 'List',
			'icon' => 'list.gif',
			'params' => Array(
				'table'=> $foreign_table,
				'pid' => $pid,
			),
			'script' => 'wizard_list.php',
		),
		'edit' => Array(
			'type' => 'popup',
			'title' => 'Edit',
			'script' => 'wizard_edit.php',
			'popup_onlyOpenIfSelected' => 1,
			'icon' => 'edit2.gif',
			'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
		),
	);


t3lib_extMgm::addTCAcolumns('tt_news',$tempColumns,1);

if ($confArr['replace_author_field'] < 3)  {
	$fieldToReplace = 'author';
	switch ($confArr['replace_author_field']) {
		case '1':		// 1 = REPLACE author field
			$author = 'tx_social2news_author';
		break;
		case '2':		// 2 = add new field at the end of the FIRST tab of the tt_news editform
			$author = 'no_auto_pb,tx_social2news_author';
			$fieldToReplace = 'no_auto_pb';
		break;
		default: 	// 0 = DO NOT replace author field, add new field before
			$author = 'tx_social2news_author,author';
		break;
	}
		// overwrite TCA "types" from tt_news with the new author field
	$tmpTypes = $GLOBALS['TCA']['tt_news']['types'];
	foreach(array_keys($tmpTypes) as $k) {
		$GLOBALS['TCA']['tt_news']['types'][$k] = Array('showitem' => preg_replace('/'.$fieldToReplace.'/',$author,$tmpTypes[$k]['showitem']));
	}
} else {	// 3 = add new field at the end of the LAST tab of the tt_news editform
	t3lib_extMgm::addToAllTCAtypes('tt_news','tx_social2news_author;;;;1-1-1');
}



	/**
	 * fe_user TCA setup additional fields
	 *
	 */

$tempColumns = array (
	'tx_social2news_twitteruser' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:social2news/locallang_db.xml:fe_users.tx_social2news_twitteruser',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_social2news_twitteruid' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:social2news/locallang_db.xml:fe_users.tx_social2news_twitteruid',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_social2news_twittertoken' => array (
		'config' => array (
			'type' => 'passthrough',
		)
	),
	'tx_social2news_yahoouser' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:social2news/locallang_db.xml:fe_users.tx_social2news_yahoouser',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_social2news_yahoouid' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:social2news/locallang_db.xml:fe_users.tx_social2news_yahoouid',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_social2news_flickrtoken' => array (
		'config' => array (
			'type' => 'passthrough',
		)
	),
	'tx_social2news_facebookuser' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:social2news/locallang_db.xml:fe_users.tx_social2news_facebookuser',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_social2news_facebookuid' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:social2news/locallang_db.xml:fe_users.tx_social2news_facebookuid',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_social2news_facebooktoken' => array (
		'config' => array (
			'type' => 'passthrough',
		)
	),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_social2news_twitteruser;;;;1-1-1, tx_social2news_twitteruid, tx_social2news_twittertoken, tx_social2news_yahoouser, tx_social2news_yahoouid, tx_social2news_flickrtoken, tx_social2news_facebookuser, tx_social2news_facebookuid, tx_social2news_facebooktoken');

	/**
	 * fe_users ['ctrl']['external'] setup for svconnector import mapping
	 *
	 * added all the svconnector_social parameter possibilities for easy ref...
	 *
	 */

	// add fe_users first as it has a higher priority

$TCA['fe_users']['ctrl']['external'] = array(
	0 => array(
		'connector' => 'social',
		'parameters' => array(
			'network' => 'twitter',
	//			currently: flickr, twitter, facebook
	//		'flickr_api_key' => '',
	//		'flickr_api_secret' => '',
	//		'flickr_auth_token' => '',
	//		'flickr_search_tags' => '',
	//			comma delimited list, double word tags are concatenated: 'sanfrancisco, bayarea, oakland'
	//			typically, either tags or text is used, but not both.
	//		'flickr_tag_mode' => '',
	//			'any' for an OR combination of tags
	//			'all' for an AND combination. Defaults to 'any' if not specified.
	//		'flickr_search_text' => '',
	//			some logic allowed, and quotes and the minus sign for "not"
	//			"galup nm" or "gallup new mexico" or gallupnm
	//		'flickr_method' => '',		// (currently not used, hard coded to 'flickr.photos.search')
	//		'flickr_method_params' => '',
	//			http://www.flickr.com/services/api/flickr.photos.search.html
	//			eg: extras=description,date_upload,icon_server,geo,tags,url_sq,url_t,url_s,url_m,url_z,url_l,url_o&min_upload_date=1306283749
	//		'flickr_contacts' => '',
	//
	//		'facebook_api_id' => '',	// (not used currently)
	//		'facebook_api_key' => '',	// (not used currently)
	//		'facebook_app_secret' => '',	// (not used currently)
	//		'facebook_search_text' => '',
	//			very simple, no logic, just quotes or plain :(
	//		'facebook_search_object' => '',
	//			currently: post or page
	//			potentially (todo): post, user, page, event, group, place, or checkin (checkin no longer working at all without auth)
	//			ref: http://developers.facebook.com/docs/reference/api/
	//
			'twitter_search_text' => '"gallup new mexico" OR "gallup nm" OR gallupnm OR livegallup',
	//			freaking brilliant search markup, see here:
	//			http://search.twitter.com/operators (damn!)
		),
		'priority' => '10',
		'data' => 'array',
		'nodetype' => 'results', // twitter.json: 'results", twitter.atom: "entry", facebook (converted from json): item, flickr: photo
		'reference_uid' => 'tx_social2news_twitteruid', // the column in which to store the external unique identifier
		'additional_fields' => 'from_user,id_str', // other external fields needed to manipulate the data
		'disabledOperations' => 'delete', // don't allow the import to execute these func's
		'pid' => 32,
		'enforcePid' => TRUE, // only manipulate records on this page
		'description' => 'Create fe_user from Twitter uname and id'
	)
);
$TCA['fe_users']['columns']['username']['external'] = array(
	0 => array(
		'field' => 'from_user',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'validateUserName',
			'params' => array(
				'encoding' => 'utf8'
			)
		)
	)
);
$TCA['fe_users']['columns']['password']['external'] = array(
	0 => array(
		'field' => 'from_user_id_str',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'randomPass'
		)
	)
);
$TCA['fe_users']['columns']['usergroup']['external'] = array(
	0 => array(
		'value' => '1',		// hardcoded to UG with id, '1' which happens to be the "General" UG on my dev system
	)
);
$TCA['fe_users']['columns']['tx_social2news_twitteruid']['external'] = array(
	0 => array(
		'field' => 'from_user_id_str'
	)
);
$TCA['fe_users']['columns']['tx_social2news_twitteruser']['external'] = array(
	0 => array(
		'field' => 'from_user'
	)
);
$TCA['fe_users']['columns']['tx_social2news_twittertoken']['external'] = array(
	0 => array(
		'value' => NULL,	// this could look for an updated token (FB especially, which has expiring tokens), but for now, all access is public anyway
	)
);
$TCA['fe_users']['columns']['image']['external'] = array(
	0 => array(
		'field' => 'profile_image_url',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'uploadImage',
			'params' => array(
				'network' => 'twitter',
				'fieldKey' => 'profile_image_url'
			)
		)
	)
);

	/**
	 * tt_news ['ctrl']['external'] setup for svconnector import mapping
	 *
	 * see the fe_user section for all the possible svconnect_social parameters options
	 *
	 */
//TODO: category (tt_news_cat, tt_news_cat_mm)
$TCA['tt_news']['ctrl']['external'] = array(
	0 => array(
		'connector' => 'social',
		'parameters' => array(
			'network' => 'twitter',
			'twitter_search_text' => '"gallup new mexico" OR "gallup nm" OR gallupnm OR livegallup',
		),
		'priority' => '20',
		'data' => 'array',
		'nodetype' => 'results', // twitter.json: results, twitter.atom: entry, facebook (converted from json): item, flickr: photo
		'reference_uid' => 'tx_social2news_external', // the column in which to store the external identifier, usually from the 'guid' tag
		'additional_fields' => 'from_user,id_str', // other external fields needed to manipulate the data
		'disabledOperations' => 'delete', // don't allow the import to execute these func's
		'pid' => 33,
		'enforcePid' => TRUE, // only manipulate records on this page
		'description' => 'Import Twitter search into tt_news'
	)
);

	// Add the external information for each column
/* old version
$TCA['tt_news']['columns']['title']['external'] = array(
	0 => array(
		'field' => 'text'
	)
);
*/
/**
 * new version will actually construct the tweet in the tt_news body, and some kind of teaser in the title... not sure what
 *
 */
$TCA['tt_news']['columns']['title']['external'] = array(
	0 => array(
		'field' => 'text',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'getSocialThread',
			'params' => array(
				'network' => 'twitter',
				'part' => 'teaser'
			)
		)
	)
);
$TCA['tt_news']['columns']['bodytext']['external'] = array(
	0 => array(
		'field' => 'text',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'getSocialThread',
			'params' => array(
				'network' => 'twitter',
				'part' => 'expanded'
			)
		)
	)
);

$TCA['tt_news']['columns']['tx_social2news_external']['external'] = array(
	0 => array(
		'field' => 'id_str'
	)
);
$TCA['tt_news']['columns']['tx_social2news_external_source']['external'] = array(
	0 => array(
		'value' => 'twitter'
	)
);
$TCA['tt_news']['columns']['datetime']['external'] = array(
	0 => array(
		'field' => 'created_at',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'date2ts'
		)
	)
);
/* This gives spurius results! Most of the time I get my logged in BE user...
 * some times I get this value in the records, but if I do, it sets the created 
 * tt_news item to hidden regardless of the "hidden" entry elsewhere in this file
$TCA['tt_news']['columns']['cruser_id']['external'] = array(
	0 => array(
		'value' => '3', // this just happens to be the uid of the _cli_scheduler user in this installation... probably change this... not sure if it matters
	)
);
*/
$TCA['tt_news']['columns']['author']['external'] = array(
	0 => array(
		'field' => 'from_user'
	)
);
$TCA['tt_news']['columns']['tx_social2news_author']['external'] = array(
	0 => array( 
		'field' => 'from_user_id_str', 
		'mapping' => array( 
			'table' => 'fe_users', 
			'reference_field' => 'tx_social2news_twitteruid', 
			'value_field' => 'uid'
		)
	)
);
$TCA['tt_news']['columns']['type']['external'] = array(
	0 => array(
/**
 * in v1 this was external url type: 'value' => '0'
 */ 
		'value' => '0'	// set all twitter news records to type 'external' since there just isn't much to display
	)
);
/**
 * in v2 this funciton will change to simply give a tweet link... not sure it will even be necessary
 */ 
$TCA['tt_news']['columns']['ext_url']['external'] = array(
	0 => array(
		'field' => 'text',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'getExternalUrl', // first url in content, else source link
			'params' => array(
				'network' => 'twitter'
			)
		)
	)
);
$TCA['tt_news']['columns']['hidden']['external'] = array(
	0 => array(
		'value' => 0
	)
);
$TCA['tt_news']['columns']['image']['external'] = array(
	0 => array(
		'field' => 'text',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'getImages',
			'params' => array(
				'network' => 'twitter',
				'task' => 'uploadImage',
				'embedlyKey' => 'fe15e6b2bc8111e0bc8c4040d3dc5c07'
/*				'embedlyUrlParams' => array(	// default is array('maxwidth' => '500', 'chars' => '200')
					'maxwidth' => '',	// int
					'maxheight' => '',	// int
					'width' => '',		// int
					'wmode' => '',		// (window / opaque / transparent)
					'allowscripts' => '',	// bool (requires format of "jsonp"... hack the source)
					'nostyle' => '',	// bool
					'autoplay' => '',	// bool
					'videosrc' => '',	// bool
					'words' => '',		// int
					'chars' => ''		// int
				)	*/
			)
		)
	)
);
$TCA['tt_news']['columns']['imagecaption']['external'] = array(
	0 => array(
		'field' => 'text',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'getImages',
			'params' => array(
				'network' => 'twitter',
				'task' => 'imageCaption',
				'cacheOnly' => TRUE
			)
		)
	)
);
$TCA['tt_news']['columns']['imagealttext']['external'] = array(
	0 => array(
		'field' => 'text',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'getImages',
			'params' => array(
				'network' => 'twitter',
				'task' => 'altText',
				'cacheOnly' => TRUE
			)
		)
	)
);
$TCA['tt_news']['columns']['imagetitletext']['external'] = array(
	0 => array(
		'field' => 'text',
		'userFunc' => array(
			'class' => $transformClass,
			'method' => 'getImages',
			'params' => array(
				'network' => 'twitter',
				'task' => 'titleText',
				'cacheOnly' => TRUE
			)
		)
	)
);
$TCA['tt_news']['columns']['category']['external'] = array(
	0 => array(
		'value' => 0 // TODO change this to use a userfunc that looks up the category names, makes a match, and inserts the id. to make this right, it has to update the tt_news_cat_mm table... bleh.
	)
);


?>

