<?php
defined('TYPO3_MODE') || die();

/* Add typoscript configuration to backend selection */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'rmnd_hybridauth',
    'Configuration/TypoScript',
    'RmndHybridauth Defaults'
);
