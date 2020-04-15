<?php
defined('TYPO3_MODE') || die();

/*******************************************************************************
 * Plugin Definitions                                                          *
 ******************************************************************************/

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Remind.RmndHybridauth',
    'Login',
    'LLL:EXT:rmnd_hybridauth/Resources/Private/Language/locallang_db.xlf:plugins.login.title'
);
