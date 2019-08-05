<?php
defined('TYPO3_MODE') || die();

/***************
 * Add default RTE configuration
 */
## $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['wdb_language_fallback'] = 'EXT:wdb_language_fallback/Configuration/RTE/Default.yaml';

/***************
 * PageTS
 */
## \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TsConfig/Page/All.tsconfig">');

/***************
 * Hook Function to fix translation handling
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_page.php']['getRecordOverlay'][] =
    \WDB\WdbLanguageFallback\Hooks\Frontend\Page\PageRepository::class;
