<?php

defined('TYPO3_MODE') || die();

/***************
 * Hook Function to fix translation handling
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_page.php']['getRecordOverlay'][] =
    \WDB\WdbLanguageFallback\Hooks\Frontend\Page\PageRepository::class;
