<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:rmnd_hybridauth/Resources/Private/Language/locallang_db.xlf:tx_rmndhybridauth_domain_model_connection',
        'label' => 'fe_user',
        'label_alt' => 'provider,identifier',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:rmnd_jobs/Resources/Public/Icons/tx_rmndhybridauth_domain_model_connection.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, fe_user, provider, identifier',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, fe_user, provider, identifier'],
    ],
    'columns' => [

        'hidden' => [
            'exclude' => false,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],

        'fe_user' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rmnd_jobs/Resources/Private/Language/locallang_db.xlf:tx_rmndhybridauth_domain_model_connection.fe_user',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'foreign_table' =>  'fe_users',
                'allowed' => 'fe_users',
                'maxitems' => 1,
                'minitems' => 0,
                'size' => 1,
                'default' => 0,
                'prepend_tname' => false
            ],
        ],

        'provider' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rmnd_jobs/Resources/Private/Language/locallang_db.xlf:tx_rmndhybridauth_domain_model_connection.provider',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'login_hash' => [
            'exclude' => true,
            'readOnly' => true,
            'label' => 'LLL:EXT:rmnd_jobs/Resources/Private/Language/locallang_db.xlf:tx_rmndhybridauth_domain_model_connection.login_hash',
            'config' => [
                'type' => 'input',
                'size' => 60,
            ],
        ],

        'last_validation' => [
            'exclude' => true,
            'readOnly' => true,
            'label' => 'LLL:EXT:rmnd_jobs/Resources/Private/Language/locallang_db.xlf:tx_rmndhybridauth_domain_model_connection.last_validation',
            'config' => [
                'type' => 'input',
                'size' => 60,
            ],
        ],

        'identifier' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rmnd_jobs/Resources/Private/Language/locallang_db.xlf:tx_rmndhybridauth_domain_model_connection.identifier',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

    ],
];
