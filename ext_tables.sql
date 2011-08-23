#
# Table structure for table 'tt_news'
#
CREATE TABLE tt_news (
	tx_social2news_external tinytext,
	tx_social2news_external_source tinytext
	tx_social2news_author blob NOT NULL
);



#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_social2news_twitteruser tinytext,
	tx_social2news_twitteruid tinytext,
	tx_social2news_yahoouser tinytext,
	tx_social2news_yahoouid tinytext,
	tx_social2news_facebookuser tinytext,
	tx_social2news_facebookuid tinytext,
	tx_social2news_twittertoken tinytext,
	tx_social2news_flickrtoken tinytext,
	tx_social2news_facebooktoken tinytext
);

CREATE TABLE tx_social2news_cache (
	id int(11) NOT NULL auto_increment,
	identifier varchar(128) NOT NULL DEFAULT '',
	crdate int(11) unsigned NOT NULL DEFAULT '0',
	content mediumtext,
	lifetime int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	KEY cache_id (`identifier`)
);

CREATE TABLE tx_social2news_cache_tags (
	id int(11) NOT NULL auto_increment,
	identifier varchar(128) NOT NULL DEFAULT '',
	tag varchar(250) NOT NULL DEFAULT '',
# <!-- note, for this case, the 'tag' field needs to hold urls... max length of ~varchar(2048) because of ie8/9. Since the tags won't be refferenced much, I figured text better than varchar(2048) -->
# <!-- note2, chaged this back to varchar(250) since a tag cannot be longer than 250 chars: -->
# <!--		t3lib/cache/frontend/interfaces/interface.t3lib_cache_frontend_frontend.php:	const PATTERN_TAG = '/^[a-zA-Z0-9_%\-&]{1,250}$/'; -->
	PRIMARY KEY (id),
	KEY cache_id (`identifier`),
	KEY cache_tag (`tag`)
);
