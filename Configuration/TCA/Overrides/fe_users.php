<?php

defined('TYPO3_MODE') || die;

// encapsulate all locally defined variables
(function () {
    /**
     * Add connection inline items to fe_users backend
     */
    $tca = [
       'tx_rmndhybridauth_domain_model_connections' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rmnd_jobs/Resources/Private/Language/locallang_db.xlf:fe_users.tx_rmndhybridauth_domain_model_connections',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_rmndhybridauth_domain_model_connection',
                'foreign_field' => 'fe_user',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 0,
                ],
            ],
       ]
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tca);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'fe_users',
        '--div--;LLL:EXT:rmnd_hybridauth/Resources/Private/Language/locallang_db.xlf:fe_users.tab.tx_rmndhybridauth, '
        . 'tx_rmndhybridauth_domain_model_connections'
    );
})();
