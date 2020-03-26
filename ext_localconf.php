<?php
defined('TYPO3_MODE') || die();

/*******************************************************************************
 * Plugin Configuration                                                        *
 ******************************************************************************/

/* Hybridauth login extension */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Remind.' . $_EXTKEY,
    'Login',
    [ 'Login' => 'auth'],
    // non-cacheable actions
    [ 'Login' => 'auth,authError']
);

/* exclude parameters from cHash calculation, necessary for facebook callback url */
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_rmndhybridauth_login[provider]';

// encapsulate all locally defined variables
(function () {

})();