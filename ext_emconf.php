<?php

/**
 * Extension Manager/Repository config file for ext "wdb_language_fallback".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Language-fallback',
    'description' => 'Fixes some issues concerning language-fallback in TYPO3. Obtain the professional version for support of special extensions like powermail.',
    'category' => 'frontend',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
        'conflicts' => [
            'wdb_language_fallback_pro' => '*',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'WDB\\WdbLanguageFallback\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'David Bruchmann',
    'author_email' => 'david.bruchmann@gmail.com',
    'author_company' => 'Webdevelopment Barlian',
    'version' => '1.3.0',
];
