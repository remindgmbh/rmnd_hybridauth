<?php

defined('TYPO3_MODE') || die;

/*******************************************************************************
 * Plugin Configuration                                                        *
 ******************************************************************************/

/* Hybridauth login plugin */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Remind.' . $_EXTKEY,
    'Login',
    [ 'Login' => 'auth,afterAuth,authError,loginComplete'],
    // non-cacheable actions
    [ 'Login' => 'auth,afterAuth,authError,loginComplete']
);

/*******************************************************************************
 * TYPO3 globals                                                               *
 ******************************************************************************/

/* exclude parameters from cHash calculation, necessary for facebook callback url */
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_rmndhybridauth_login[provider]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_rmndhybridauth_login[action]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_rmndhybridauth_login[connection]';

/*******************************************************************************
 * Service Registry                                                            *
 ******************************************************************************/

/* Only use service if option is activated in the extension settings */
if (\Remind\RmndHybridauth\Utility\ExtensionSettingsUtility::isUseAfterAuthLoginService()) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
        'rmnd_hybridauth',
        'auth',
        \Remind\RmndHybridauth\Service\AfterAuthLoginService::class,
        [
            'title' => 'RmndHybridAuth Social Login Service',
            'description' => 'Process login after hybridauth connection',
            'subtype' => 'getUserFE,authUserFE',
            'available' => true,
            'priority' => 74,
            'quality' => 74,
            'os' => '',
            'exec' => '',
            'className' => \Remind\RmndHybridauth\Service\AfterAuthLoginService::class
        ]
    );
}
