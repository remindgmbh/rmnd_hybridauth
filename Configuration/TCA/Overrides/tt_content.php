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

/*******************************************************************************
 * Flexform Configuration                                                      *
 ******************************************************************************/

//$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['rmndhybridauth_login'] = 'pi_flexform';
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
//    'rmndhybridauth_login',
//    'FILE:EXT:rmnd_hybridauth/Configuration/Flexform/Login.xml'
//);
