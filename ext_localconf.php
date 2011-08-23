<?php

t3lib_cache::enableCachingFramework();
// If cache is not already defined, define it
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['social2news'])) {
	 $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['social2news'] = array(
			'backend' => 't3lib_cache_backend_DbBackend',
			'options' => array(
				'cacheTable' => 'tx_social2news_cache',
				'tagsTable' => 'tx_social2news_cache_tags',
			)
	);
}
// load classes needed for hooks, etc...
require_once(t3lib_extMgm::extPath('social2news').'Classes/Adapter/class.tx_social2news_adapter_extraMarkers.php');

	// ... and configure the 'extraItemMarkerHook' from tt_news/pi/class.tx_ttnews.php to use class 'tx_social2news_adapter_extraMarkers'.
	// (the name for the method in this class which processes the markerArray is hardcoded in tt_news: 'extraItemMarkerProcessor')
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraItemMarkerHook'][] = 'tx_social2news_adapter_extraMarkers';

?>
