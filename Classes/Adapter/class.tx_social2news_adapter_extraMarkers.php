<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Rupert Germann <rupi@gmx.li>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Modified from news_author_rel allowing several fields from fe_users to be output as markers in tt_news templates.
 *
 * @author thanks to Rupert Germann <rupi@gmx.li> (What ever happened to you, man? Keep rocking where ever you are...) and Sy Moen http://geekphd.com
 */
class tx_social2news_adapter_extraMarkers {
	/**
 	 * tx_social2news_adapter_extraMarkers::extraItemMarkerProcessor()
 	 * this function is called by the Hook in the function getItemMarkerArray() from class.tx_ttnews.php
 	 *
 	 * @param	array		$markerArray: the markerArray from the tt_news class
 	 * @param	array		$row: the database row for the current news-record
 	 * @param	array		$lConf: the TS setup array from tt_news (holds the TS vars from the current tt_news view)
 	 * @param	object		$pObj: reference to the parent object
 	 * @return	array		$markerArray: the processed markerArray
 	 * @see EXT:tt_news/pi/class.tx_ttnews.php->getItemMarkerArray()
 	 */
	function extraItemMarkerProcessor($markerArray, $row, $lConf, &$pObj) {
		if ($row['tx_social2news_author']) {
			if (!is_array($pObj->confArr)) {
				$pObj->confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['social2news']);
			}
			elseif(empty($pObj->confArr['replace_author_field'])) {
				$pObj->confArr = array_merge($pObj->confArr, unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['social2news']));
			}
			if (!$pObj->authortable) {
				$pObj->authortable = 'fe_users'; 	// if not set table to "fe_users"
			}
			$markerNames = array(
				'name'		=> '###AUTHOR_NAME###',
				'socialUser'	=> '###AUTHOR_SOCIAL_USERNAME###',
				'image'		=> '###AUTHOR_IMAGE###',
				'extUrl'	=> '###AUTHOR_EXT_URL###',
				'intUrl'	=> '###AUTHOR_INT_URL###'
			);
			$markerArray[$markerName] = '';

			$authortable = $pObj->authortable;
			$local_cObj = t3lib_div::makeInstance('tslib_cObj'); 	// Local cObj.
			$lines = array();
// Got to here >>
			// get the list of author ids from the tt_news col
			$authorUid = trim($row['tx_social2news_author']);
			// get the foreign row that each author id refers to
			$tmprow = $pObj->pi_getRecord($authortable, $authorUid);
			if (is_array($tmprow)) {
				// take the row and construct the tslib_cObj with it, so that we can work on it with TS (stdWarp and all that)
				$local_cObj->start($tmprow, '');
				// based on the loaded row, render a linked author name for each $authors
				$markerArray[$markerNames['name']]	= $local_cObj->stdWrap($local_cObj->cObjGetSingle($lConf['authorName'], $lConf['authorName.'], 's2n_authorName'),$lConf['authorName_stdWrap.']);
				$markerArray[$markerNames['socialUser']]	= $local_cObj->stdWrap($local_cObj->cObjGetSingle($lConf['authorSocialUser'], $lConf['authorSocialUser.'], 's2n_authorSocialUser'),$lConf['authorSocialUser_stdWrap.']);
				$markerArray[$markerNames['image']]	= $local_cObj->stdWrap($local_cObj->cObjGetSingle($lConf['authorImage'], $lConf['authorImage.'], 's2n_authorImage'),$lConf['authorImage_stdWrap.']);
				$markerArray[$markerNames['extUrl']]	= $local_cObj->stdWrap($local_cObj->cObjGetSingle($lConf['authorExtUrl'], $lConf['authorExtUrl.'], 's2n_authorExtUrl'),$lConf['authorExtUrl_stdWrap.']); 
				$markerArray[$markerNames['intUrl']]	= $local_cObj->stdWrap($local_cObj->cObjGetSingle($lConf['authorIntUrl'], $lConf['authorIntUrl.'], 's2n_authorIntUrl'),$lConf['authorIntUrl_stdWrap.']); 
			}
		}
		if ($this->extConf['debug'] || TYPO3_DLOG) t3lib_div::devLog('social2news extraItemMarkerProcessor gets called', $this->extKey, 2, array($authortable, $authorUid, $tmprow, $markerArray, $row, $lConf, $pObj)); 
		return $markerArray;
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/social2news/Classes/Adapter/class.tx_social2news_adapter_extraMarkers.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/social2news/Classes/Adapter/class.tx_social2news_adapter_extraMarkers.php']);
}
?>
