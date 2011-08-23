<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Sy Moen <tech@gallupcurrent.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Transformation functions for the 'svconnect_social' extension
 *
 * @author		Sy Moen <tech@gallupcurrent.com>
 * @package		TYPO3
 * @subpackage	tx_social2news
 *
 */
class tx_social2news_controller_transforms {
	public $prefixId = 'tx_social2news_controller_transforms';          // Same as class name
	public $scriptRelPath = 'Classes/Controller/class.tx_social2news_controller_transforms.php';    // Path to this script relative to the extension dir.
	public $extKey = 'social2news';  // The extension key.
	protected $localCache;
	protected $extConf;
	protected $urlTools;


// Setup
	public function __construct() {
		$this->initCache();
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		$this->urlTools = t3lib_div::getUserObj('EXT:svconnector_social/Classes/Api/class.tx_svconnectorsocial_api_urlTools.php:&tx_svconnectorsocial_api_urlTools'); //new urlTools; 
	}

	protected function initCache() {
		$version = class_exists('t3lib_utility_VersionNumber')
			? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
			: t3lib_div::int_from_ver(TYPO3_version);
		if ( (($version < 4006000) && TYPO3_UseCachingFramework) || $version >= 4006000) {
			// Create the cache
			try {
				// create($cacheIdentifier, $cacheName, $backendName, array $backendOptions = array())
				$GLOBALS['typo3CacheFactory']->create(
					'social2news',
					't3lib_cache_frontend_VariableFrontend',
//					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->extKey]['frontend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->extKey]['backend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->extKey]['options']
				);
			} catch(t3lib_cache_exception_DuplicateIdentifier $e) {
				// do nothing, the cache already exists
			}
			// create handle for cache
			try {
				$this->localCache = $GLOBALS['typo3CacheManager']->getCache($this->extKey);
			} catch(t3lib_cache_exception_NoSuchCache $e) {
				// Unable to load
			}
		}
	}

// Main Transforms

	/**
	 * Builds a mini social thread out of a tweet and its @'s
	 * !!!the current version is a stub!!!
	 *
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 * @return	string	hmtl string for a complete expanded social thread
	 */
	public function getSocialThread($record, $index, $params) {
		if($params['part'] == 'teaser') {
			return $this->scrubUrls($record, $index, $params);
		} elseif($params['part'] == 'expanded') return $this->getSocial($record, $index, $params);
	}	

	/**
	 * Builds a fully linked / interactive social post with links and images expanded.
	 * 	partial ref: https://dev.twitter.com/terms/display-guidelines
	 *
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 * @return	string	hmtl string for a single (expanded) social post 
	 */
	public function getSocial($record, $index, $params) {
		$words = explode(' ', $record[$index]);
		foreach($words as $word) {
			if( in_array(substr($word, 0, 1), array('#', '@')) ) {
				$wordArray['text'] = $word;
				$wordArray['type'] = substr($word, 0, 1);
				$analyzedWords[] = $wordArray;
			}
			elseif($url = $this->urlTools->validateUrl($word)) {
				$wordArray['orig'] = $word;
				$wordArray['text'] = $this->urlTools->expandUrl($url['url'], TRUE, TRUE);
				$wordArray['type'] = 'url';
				$analyzedWords[] = $wordArray;
			} else $analyzedWords[] = $word;
		}
//		if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('Raw output from getSocial', $this->extKey, 2, array(implode(' ', $analyzedWords), $record, $index, $params)); 
		switch($params['network']) {
			case 'twitter':
				$userQuery = '<a rel="nofollow" href="http://twitter.com/%1$s" class="twitter-atreply"><span class="at">@</span><span class="at-text">%1$s</span></a>';
				$termQuery = '<a rel="nofollow" href="http://twitter.com/#!/search?q=%%23%1$s" class="twitter-hashtag"><span class="hash">#</span><span class="hash-text">%1$s</span></a>'; 
				$simpleUrl = '<a href="%1$s">%1$s</a>';
				foreach($analyzedWords as $word) {
					if(is_array($word)) {
						if($word['type'] == '#') $wordStr = sprintf($termQuery, substr($word['text'], 1));
						if($word['type'] == '@') $wordStr = sprintf($userQuery, substr($word['text'], 1));
						if($word['type'] == 'url') $wordStr = sprintf($simpleUrl, $word['text']);
						$tweetArray[] = $wordStr;
					} else $tweetArray[] = $word;
				}
				$twitterControls = '<div class="twitter-controls">' . "\n" .
							'<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>' . "\n" .
							'<p><a href="https://twitter.com/intent/tweet?in_reply_to=' . $record['id_str'] . '">Reply</a></p>' . "\n" .
							'<p><a href="https://twitter.com/intent/retweet?tweet_id=' . $record['id_str'] . '">Retweet</a></p>' . "\n" .
							'<p><a href="https://twitter.com/intent/favorite?tweet_id=' . $record['id_str'] . '">Favorite</a></p></div>' . "\n";
				return implode(' ', $tweetArray) . $twitterControls;
			case 'facebook':
			case 'flickr':
			default:
		}
	}	

	/**
	 * returns a string with all valid urls removed.
	 *
	 * @param	array	$record: the that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 * @return	int	pubDate (or 'now' if error) converted to timestamp 
	 */
	public function scrubUrls($record, $index, $params) {
		$this->urlTools->setOrigText($record[$index]);
		if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('Return of scrubUrls: ' . $this->urlTools->getStrippedText(), $this->extKey, 2, array($record, $index, $params)); 
		return $this->urlTools->getStrippedText();
	}	

	/**
	 * converts a pubDate (Twitter, FB) to a timestamp
	 *
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 * @return	int	pubDate (or 'now' if error) converted to timestamp 
	 */
	public function date2ts($record, $index, $params) {
		if(strtotime($record[$index])) return strtotime($record[$index]);
		else return strtotime('now');
	}

	/**
	 * creates a random salted phpass password for the newly created user stub 
	 *
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 * @return	mixed	string for random password 
	 */
	public function randomPass($record, $index, $params) {
		$password = mt_rand(); // plain-text password 
		$saltedPassword = ''; 
		if (t3lib_extMgm::isLoaded('saltedpasswords')) { 
			if (tx_saltedpasswords_div::isUsageEnabled('FE')) { 
				$objSalt = tx_saltedpasswords_salts_factory::getSaltingInstance(NULL); 
				if (is_object($objSalt)) { 
					$saltedPassword = $objSalt->getHashedPassword($password); 
				}
			}
		}
		return $saltedPassword;
	}

	/**
	 * Take a public username and create a unique, valid local username from it
	 * 
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 *			$params['encoding']: UTF8 or other 
	 * @return	mixed	Unique, TYPO3 compliant username
	 */
	public function validateUserName($record, $index, $params) {
			// Make sure the encoding uses the proper code
		$encoding = $GLOBALS['LANG']->csConvObj->parse_charset($params['encoding']);
		$baseName = $record['from_user'];
		$userNameBase = $GLOBALS['LANG']->csConvObj->conv_case($encoding, $baseName, 'toLower');
			// We must make sure this doesn't contain non-ASCII characters
		$userName = $GLOBALS['LANG']->csConvObj->specCharsToASCII($encoding, $userNameBase);
			// Lastly remove single quotes and double quotes, and replace spaces by underscores
			// Other special characters are acceptable
		$userNameClean = preg_replace('/[\'"]/', '', trim($userName));
		$userNameClean = preg_replace('/[\s]/', '_', trim($userName));
		return $userNameClean;
	}

	/**
	 * Looks for an external url referred to by this resource
	 *
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 * @return	mixed	Full name, i.e. last name and first name concatenated
	 */
	public function getExternalUrl($record, $index, $params) {
		switch($params['network']) {
			case 'twitter':
				$urls = array();
				try {
					$this->urlTools->setOrigText($record[$index]);
					$urls = $this->urlTools->getUrls();
					if(isset($urls[0])) $expanded = $this->urlTools->expandUrl($urls[0], TRUE, TRUE); // on twitter, expect all urls to be redirects
				}
				catch(Exception $e) {
					if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog($e->getMessage(), $this->extKey, 2, array($record, $index, $params)); 
				}
				$link = 'http://twitter.com/' . $record['from_user'] . '/statuses/' . $record['id_str'];
				if(count($urls) == 0) {
					if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('ext_url will return the tweet link: ' . $link, $this->extKey, 1);
					return $link; // link to the tweet itself
				} else {
					if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('ext_url will return the first inline url: ' . $expanded, $this->extKey, 1, $urls);
					return $expanded; //first url found in body
				}
				break;
			case 'facebook':
				// TODO
				break;
			case 'flickr':
				// TODO
				break;
			default:
				break;
		}
	}

	/**
	 * Looks for images in external url references
	 *
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 * @return	array	[{"url":"url-encoded string", "caption":"html-encoded string", "alt":"html-encoded string", "title":"html-encoded string"}]
	 */
	public function getImages($record, $index, $params) {

		switch($params['network']) {
			case 'twitter':
				if($record[$index]) {
					try {
						$this->urlTools->setOrigText($record[$index]);
						$cacheKeys = $this->urlTools->getUrls();
					}
					catch(Exception $e) {
						if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog($e->getMessage(), $this->extKey, 2, array($record, $index, $params)); 
						return NULL;
					}

					if(count($cacheKeys) > 0) {
						// get stuf from cache if possible
						foreach($cacheKeys as $tag) {
							$tag = $this->urlTools->createValidTagFromUrl($tag);
							try {
								$cacheResult = $this->localCache->getByTag($tag);
							} catch(\InvalidArgumentException $e) {
								if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('invalid tag (\'/^[a-zA-Z0-9_%\-&]{1,250}$/\'): ' . $tag . ' e->msg: ' . $e->getMessage(), $this->extKey, 2, array($cacheTags, $record));
							}
							if($cacheResult) {
								try {
									if(count($cacheResult) == 1) {
										$oembedsFromCache[] = $cacheResult[0];
										$this->urlTools->removeUrl($key);
									}
									else $this->localCache->flushByTag($tag); // not sure how there could be more than one location with the same url... but just in case
								} catch(\InvalidArgumentException $e) {
									if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog($e->getMessage() . ' - ' . $tag, $this->extKey, 2);
								}
							}
						}
						if(is_array($oembedsFromCache)) $oembedList = $oembedsFromCache;
						
						// get non-cached stuff from web services
						// only if there are urls to get, there is a key for embedly (for now), and there is no cache only requirement (for repeated calls to the method for data from a single oembed)
						if( ($this->urlTools->countUrls() > 0) && $params['embedlyKey'] && !$params['cacheOnly']) {
							try {
								$newOembeds = $this->urlTools->getOembeds($params['embedlyKey'], $params['embedlyUrlParams']?$params['embedlyUrlParams']:'');
							} catch(Exception $e) {
								if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog($e->getMessage(), $this->extKey, 2, array($record, $index, $params));
							}
								
							if($newOembeds) {
								if(is_array($oembedsFromCache)) $oembedList = array_merge($oembedsFromCache, $newOembeds);
								else $oembedList = $newOembeds;
								foreach($newOembeds as $oembed) {
									$cacheId = md5(implode(' ', $oembed));
									$cacheTags = array($this->prefixId, $this->urlTools->createValidTagFromUrl($oembed['orig']));
									if($oembed['url']) $cacheTags[] = $this->urlTools->createValidTagFromUrl($oembed['url']); // some oembed error will not return the 'url' value
									try {
										$this->localCache->set(
											$cacheId,		// string - cache identifier 
											$oembed,		// mixed - data to cache
											$cacheTags,		// array - tags to add to the cache entry
											28 * 86400		// int - lifetime in seconds
										);
									} catch(\InvalidArgumentException $e) {
										if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('invalid tag (\'/^[a-zA-Z0-9_%\-&]{1,250}$/\') [see data] or identifier:' . $cacheId . ' e->msg: ' . $e->getMessage(), $this->extKey, 2, array($cacheTags, $record));				
									}
								}
							}
						} elseif (!$params['embedlyKey'] && !$params['cacheOnly'] && ($this->extConf['debug'] || TYPO3_DLOG)) t3lib_div::devLog('Currently the only option for oembeds is embedly, and an embedly key was not provided', $this->extKey, 2, array($record, $index, $params));
						if(is_array($oembedList)) {
							foreach($oembedList as $oembed) {
								if(array_key_exists('thumbnail_url', $oembed) || (array_key_exists('url', $oembed) && $oembed['type'] == 'photo')) {
									$images[$oembed['url']]['url'] = $oembed['thumbnail_url'] ? $oembed['thumbnail_url'] : $oembed['url'];
									if(array_key_exists('author_name', $oembed))
										$images[$oembed['url']]['imageCaption'] = '<a href="' . ($oembed['author_url'] ? $oembed['author_url'] : $oembed['url']) .'">' . $oembed['author_name'] . '</a>';
									if(array_key_exists('title', $oembed))
										$images[$oembed['url']]['titleText'] = $oembed['title'];
									if(array_key_exists('description', $oembed))
										$images[$oembed['url']]['altText'] = $oembed['description'];
								}
							}
							if(count($images) == 0) {
								if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('Oembeds did not contain thumbnail_url or url keys', $this->extKey, 1, $oembedList);
								return NULL;
							}
							foreach($images as $url => $image) {
								switch($params['task']) {
									case 'uploadImage':
										$record = $record + $image;
										$filename = $this->uploadImage($record, 'url', $params);
										// tt_news images dir is hardcoded to uploads/pics...
										$copied = t3lib_div::upload_copy_move(PATH_site . "uploads/tx_" . $this->extKey  . "/" . $filename, PATH_site . "uploads/pics/" . $filename);	
										if($copied) {
											@unlink(PATH_site . "uploads/tx_" . $this->extKey  . "/" . $filename);
											return $filename;
										else return NULL;
									case 'imageCaption':
										return $image['imageCaption'];
									case 'titleText':
										return $image['titleText'];
									case 'altText':
										return $image['altText'];
									default:
								}
							}
						}
					} else {
						if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('No resolvable urls in $record[text]' . $imageExt, $this->extKey, 1, array($record, $cacheKeys));
						return NULL;
					}
				} else {
					if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('$record[text] had no data' . $imageExt, $this->extKey, 1, array($record, $params));
					return NULL;
				}
				break;
			case 'facebook':
				// TODO
				break;
			case 'flickr':
				// TODO
				break;
			default:
				break;
		}

	}

// Class specific subtasks (should be in ts3lib_div?)	

	/**
	 * Uploads an image and returns its name
	 *
	 * @param	array	$record: the ful$link, $urlShortenersl record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 * @return	string	$imageName: returns an image name or false if not uploaded
	 */
	public function uploadImage($record, $index, $params) {
		$url = trim($record[$index]);

		// let's play a game called "find the image"
		$parsedImgUrl = parse_url($url);
		$vettedUrl = $url;
		$imagesPattern = '/(\.gif|\.jpg|\.jpeg|\.bmp|\.png)/';
		if(!preg_match($imagesPattern, $parsedImgUrl['path'], $matches)) {
			// check the http header to see what it really is
			$contentType = t3lib_div::getURL($url, 2, $this->getHttpHeaderParts, $report);
			preg_match('/Content-Type: ([^\/]*)\/(.*)[\n\r]*/', $contentType, $matches);
			if($matches[1] == 'image') {
				// The content type is right, but is it a redirect? bleh...
				if(preg_match('/Location: (.*)[\n\r]*/', $contentType, $matches)) {
					$vettedUrl = $matches[1];
					if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('Location match', $this->extKey, 1, array($parsedImgUrl, $matches));
				} else $addFileType = $matches[2];
			} else {
				// where are they hiding that thing?
				$findRedirectUrl = $this->urlTools->getFinalUrl($url);
				$parsedImgUrl = parse_url($findRedirectUrl);
				$vettedUrl = $findRedirectUrl;
				if(!preg_match($imagesPattern, $parsedImgUrl['path'], $matches)) {
					// look at the header of this url just in case
					$contentType = t3lib_div::getURL($findRedirectUrl, 2, $this->getHttpHeaderParts, $report);
					preg_match('/Content-Type: ([^\/]*)\/(.*)[\n\r]*/', $contentType, $matches);
					if($matches[1] == 'image') $addFileType = $matches[2];
					// give up, its probably there, but a more clever means will have to be employed to get it
					else return FALSE;
				}
			}
		}
		// if we got a hit, pass cURL the full url
		$remoteImgUrl = $vettedUrl;

		$tmp_filename = $this->prefixId . "-" .md5(mt_rand());

//		$ch = curl_init($remoteImgUrl);
//		$fp = fopen("/tmp/" . $tmp_filename, 'w');

		$save_to = '/tmp/' . $tmp_filename;
		if(FALSE !== file_put_contents($save_to, file_get_contents($remoteImgUrl))) {


//		if($fp) {
//			$header = array(
//				'Accept	text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8,application/json',
//				'Accept-Language	en-us,en;q=0.5',
//				'Accept-Encoding	gzip, deflate',
//				'Accept-Charset	ISO-8859-1,utf-8;q=0.7,*;q=0.7',
//				'Connection	keep-alive'
//			);
//			curl_setopt($ch, CURLOPT_FILE, $fp);
//			curl_setopt($ch, CURLOPT_HEADER, 0);
//			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.5; rv:5.0.1) Gecko/20100101 Firefox/5.0.1');
//			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//			curl_setopt($ch, CURLOPT_REFERER, 'http://livegallup.com/');
//			curl_setopt($curl, CURLOPT_AUTOREFERER, true);
//			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
//			curl_exec($ch);

//			$curl_info = curl_getInfo($ch);
//			curl_close($ch);
//			fclose($fp);

			if(filesize("/tmp/" . $tmp_filename) > 0) {
				$filename = $this->extKey . '-' .
						$params['network'] . '[' . $record['from_user'] . ']-' . 
						substr($parsedImgUrl['path'], (strrpos($parsedImgUrl['path'], '/') + 1)) .
//							(strrpos($parsedImgUrl['path'], '/') ? (strrpos($parsedImgUrl['path'], '/') + 1) : 0)) .
						($addFileType ? '.' . $addFileType : '');					
				$copied = t3lib_div::upload_copy_move("/tmp/" . $tmp_filename, PATH_site . "uploads/tx_" . $this->extKey  . "/" . $filename);
				if($copied) @unlink("/tmp/" . $tmp_filename);
				$uploaded = TRUE;
			} else { // error, filesize 0
				if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('The file was opened, but nothing was written...', $this->extKey, 2, array("/tmp/" . $tmp_filename, $remoteImgUrl, $curl_info, $record, $index));
			}
		} else { // could not open file... probably the user has no permission on this directory
			if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('could not open file to write in /tmp/' . $save_to . ', or could not get file, ' . $remoteImgUrl, $this->extKey, 2);
		}
		return $uploaded ? $filename : FALSE;
	}

// TYPO3 realted subtasks !!!this method was moved to urlTools and will be removed

	/**
	 * returns a string that is a human readable valid TYPO3 cache tag from a url
	 *	eg: http://domain.com/really/long/url/string?more=that_just+keeps+going+and+going&howLongDoesItGo=and+going+and+going+and+going+for+like#upto-2000-chars
	 *	becomes: domain.com...everything+cut+out+except+for+the+last+part+creating+a+249+char+string
	 *
	 * @param	string	$url: the full record that is being transformed
	 * @return	string	$tag: string that conforms to this pattern: '/^[a-zA-Z0-9_%\-&]{1,250}$/'
	 */
	private function createValidTagFromUrl($url) {	
		if(strlen($url) > 250) {
			$parsedUrl = $this->urlTools->validateUrl($url);
			$url = $parsedUrl['host'] . '...' . 
				substr(
					$parsedUrl['path_abempty']?$parsedUrl['path_abempty']:'' . 
					$parsedUrl['query']?('?' . $parsedUrl['query']):'' . 
					$parsedUrl['fragment']?('#' . $parsedUrl['fragment']):''
					, -245 + strlen($parsedUrl['host']), 0);
		}
		$tag = str_replace('.', '_', urlencode($url));
		return $tag;
	}

// Deprecated cruft (from main transforms)

	/**
	 * xml: Extract Twitter username from .atom:<author><uri> node
	 *
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 *			$params['part']: can either be 'uid' or 'name' 
	 * @return	mixed	username from twitter
	 */
	/* Deprecated, but should work if needed
	public function parseTwitterName($record, $index, $params) {
		$preg_match('/^([^ ]*) \((.*)\)$/', $record[$index], $name);
		switch($params['part']) {
			case 'uid':
				return $name[1];
				break;
			case 'name':
				return $name[2];
				break;
		}
	}*/

	/**
	 * Determine local fe_users account for this Social Network username using the <author><uri> node
	 *
	 * @param	array	$record: the full record that is being transformed
	 * @param	string	$index: the index of the field to transform
	 * @param	array	$params: additional parameters from the TCA
	 *			$params['network']: can either be 'twitter', 'facebook' or 'flickr' 
	 * @return	int	uid from fe_users record
	 */

	/* deprecated, but should work if needed
	public function getFeUser($record, $index, $params) {
		switch($params['network']) {
			case 'twitter':
//				$params = array_merge($params, array('part' => 'uid'));
//				$twitterUid = $this->parseTwitterName($record, $index, $params);
//				$statement = $GLOBALS['TYPO3_DB']->prepare_SELECTquery('uid', 'fe_users', 'twitteruid = ' . $twitterUid);
				$statement = $GLOBALS['TYPO3_DB']->prepare_SELECTquery('uid', 'fe_users', 'twitteruid = ' . $record[$index]);
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($statement);
				$statement->free();
				return $row['uid'];
				break;
			case 'facebook':
				// TODO
				break;
			case 'flickr':
				// TODO
				break;
			default:
				break;
		}
	}*/

}
?>
