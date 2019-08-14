<?php

namespace WDB\WdbLanguageFallback\Hooks\Frontend\Page;

/**
 * This file is part of the "wdb_language_fallback" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
# use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
# use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Need for this class is based on https://forge.typo3.org/issues/86762
 *
 * @author David Bruchmann <david.bruchmann@gmail.com>
 * @copyright 2019 David Bruchmann <david.bruchmann@gmail.com>
 */
class PageRepository implements \TYPO3\CMS\Frontend\Page\PageRepositoryGetRecordOverlayHookInterface
{
    protected $tsConfig = null;

    protected $langConfig = null;

    protected function init()
    {
        $this->tsConfig = GeneralUtility::removeDotsFromTS($GLOBALS['TSFE']->config['config']); //['intrinsicFields.']);

        // @var TYPO3\CMS\Core\Http\ServerRequest
        // @deprecated $GLOBALS['TYPO3_REQUEST'] will be removed in future versions
        $request = $GLOBALS['TYPO3_REQUEST'];

        // @var TYPO3\CMS\Core\Site\Entity\SiteLanguage
        $siteLanguageObject = $request->getAttribute('language');
        if(!is_object($siteLanguageObject)){
            throw new \InvalidArgumentException('siteLanguageObject in '.__METHOD__.' couldn\'t be initialized. Check if your site is running completely on http or https and configured in TYPO3 accordingly.');
        }
        $this->langConfig = $siteLanguageObject->toArray();
    }

    protected function initTsfe(){
        if (!isset($GLOBALS['TSFE'])) {
           $id       = GeneralUtility::_GP('id')       ? GeneralUtility::_GP('id')       : 0;
           $type     = GeneralUtility::_GP('type')     ? GeneralUtility::_GP('type')     : 0;
           $no_cache = GeneralUtility::_GP('no_cache') ? GeneralUtility::_GP('no_cache') : 0;
           $cHash    = GeneralUtility::_GP('cHash')    ? GeneralUtility::_GP('cHash')    : '';
           $MP       = GeneralUtility::_GP('MP');
           $GLOBALS['TSFE'] = GeneralUtility::makeInstance(TypoScriptFrontendController::class, null, $id, $type, $no_cache, $cHash, null, $MP);
        }
    }

    public function getRecordOverlay_preProcess($table, &$row, &$sys_language_content, $OLmode, \TYPO3\CMS\Frontend\Page\PageRepository $parent)
    {
        $this->init();
        // Check if handling for the current language is configured
        $languageAspect = $this->tsConfig['wdb_language_fallback']['languageAspect'];
        $languageKey = $this->langConfig[$languageAspect];
        $languageActivation = $this->getActiveForLanguages();

        if(!isset($languageActivation[$languageKey]) || intval($languageActivation[$languageKey]) !== 1) {
            return;
        }

        // if tt_content['l18n_diffsource'] is not set it must be a TypoScript-Object
        // TODO: make it more intelligent, that it's working for other tables too
        $crippledRow = false;
        if($table == 'tt_content' && !isset($row['l18n_diffsource'])) {
            $crippledRow = true;
        }
        elseif(strpos($table, '_domain_model_')) {
            // extbase extension
            // trash incoming row as extbase
            // - did the overlay already by itself
            // - might have replaced some data with wrong values
            $crippledRow = true;
        }

        $originalRow = $row;
        if($crippledRow) {
            if(!$table) {
                // throw new \InvalidArgumentException('Provided data never include a table, therefore no language-overlay is possible!');
                return;
            }
            if(!$row) {
                // throw new \InvalidArgumentException('Provided data never include a row, therefore no language-overlay is possible!');
                return;
            }
            if(!isset($row['uid'])) {
                // throw new \InvalidArgumentException('Provided data never include an uid, therefore no language-overlay is possible!');
                return;
            }
            if(!isset($row['pid'])) {
                // throw new \InvalidArgumentException('Provided data never include a pid, therefore no language-overlay is possible!');
                return;
            }
            $originalRow = $this->findByUid($table, $row['uid'], $row['pid']);
        }

        $tableControl  = $GLOBALS['TCA'][$table]['ctrl'] ?? [];
        $languageField = $tableControl['languageField'];
        $transOrigPointerField = $tableControl['transOrigPointerField'];
        $languageChain = $this->getFallbackChain($parent);

        // ##########
        // OverlayRow
        // ##########
        if($languageField && strlen($languageChain)) {
            $overlayRow = $this->findOverlayRow($table, $row['uid'], $row['pid'], $languageField, $languageChain, $transOrigPointerField);
        }

        // ###############################
        // Media / table sys_file_metadata
        // ###############################
        // TODO: which aspect is deciding about overlay concerning table sys_file_metadata?
        if($table == 'sys_file_metadata'){
            if($overlayRow && is_array($overlayRow) && count($overlayRow)) {
                $row = $overlayRow;
            }
            return;
        }

        if(is_array($overlayRow) && count($overlayRow)){
            // #######################
            // Synchronize Data Arrays
            // #######################
            if(count($row) !== count($overlayRow)){
                // removing unneded fields, count must be the same
                foreach($overlayRow as $key => $value){
                    if(!array_key_exists($key, $row)){
                        unset($overlayRow[$key]);
                    }
                }
                // adding virtual fields, that are not present in database
                foreach($row as $key => $value){
                    if(!array_key_exists($key, $overlayRow)){
                        $overlayRow[$key] = $row[$key];
                        #$sys_language_content =
                    }
                }
            }
            // ####################
            // Assigning the result
            // ####################
            $row = $overlayRow;
            $sys_language_content = $row[$languageField];
        } else {
            // ######################
            // Assigning empty result
            // ######################
            $row = [];
        }
    }

    /**
     * $row as parameter here has already deleted data and is useless in most cases
     * The method is required by the mandatory interface
     */
    public function getRecordOverlay_postProcess($table, &$row, &$sys_language_content, $OLmode, \TYPO3\CMS\Frontend\Page\PageRepository $parent)
    {
        /*
        // ##############################
        // SOME BASIC LOGIC (IF REQUIRED)
        // ##############################
        $this->init();
        // Check if handling for the current language is configured
        $languageAspect = $this->tsConfig['wdb_language_hook']['languageAspect'];
        $languageKey = $this->langConfig[$languageAspect];
        $languageActivation = $this->getActiveForLanguages();
        if(!isset($languageActivation[$languageKey]) || intval($languageActivation[$languageKey]) !== 1) {
            return;
        }
        
        // ... HERE ANY CONTENT ...
        
        */
        
        if(!is_array($row) || count($row) === 0) {
            $row = false;
        }
    }

    protected function findOverlayRow($table, $uid, $pid, $languageField, $languageChain, $transOrigPointerField)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);
        $queryBuilder->setRestrictions(
            GeneralUtility::makeInstance(FrontendRestrictionContainer::class)
        );
        $query = $queryBuilder->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->in(
                    $languageField,
                    $queryBuilder->createNamedParameter(GeneralUtility::intExplode(',', $languageChain), Connection::PARAM_INT_ARRAY)
                ),
                $queryBuilder->expr()->eq(
                    $transOrigPointerField,
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->add('orderBy', 'FIELD(' . $languageField . ', ' . $languageChain . ')', true)
            ->setMaxResults(1);
        $resource = $query->execute();
        $overlayRow = $resource->fetch();
        return $overlayRow;
    }

    protected function findByUid($table, $uid, $pid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);
        $queryBuilder->setRestrictions(
            GeneralUtility::makeInstance(FrontendRestrictionContainer::class)
        );
        $query = $queryBuilder->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            );
        $resource = $query->execute();
        $row = $resource->fetch();
        return $row;
    }

    protected function getActiveForLanguages()
    {
        $languageActivation = $this->tsConfig['wdb_language_fallback']['activeForLanguages'];
        return $languageActivation;
    }

    protected function getFallbackChain($parent){
        $languageAspect = $parent->context->getAspect('language');
        $fallbackChain = $languageAspect->getFallbackChain();
        // $fallbackChain =>  [0 => 1, 1 => 0, 2 => 'pageNotFound']

        $languageChainArray = [];
        foreach ($fallbackChain as $count => $languageKey) {
            if (MathUtility::canBeInterpretedAsInteger($languageKey)) {
                $languageChainArray[] = $languageKey;
            }
        }
        $languageChain = $parent->sys_language_uid . (count($languageChainArray) ? ',' . implode(',', $languageChainArray) : '');
        return $languageChain;
    }
}
