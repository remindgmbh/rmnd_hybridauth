<?php
defined('TYPO3_MODE') || die();

/*******************************************************************************
 * Plugin Configuration                                                        *
 ******************************************************************************/

/* Hybridauth login extension */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Remind.' . $_EXTKEY,
    'Login',
    [ 'Login' => 'show,auth,noProviderError,authError'],
    // non-cacheable actions
    [ 'Login' => 'auth,authError']
);

// encapsulate all locally defined variables
(function () {

})();