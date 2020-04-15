<?php

namespace Remind\RmndHybridauth\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class ExtensionSettingsUtility
{
    /**
     * Key for receiving extension settings
     * @var string
     */
    const EXTENSTION_KEY = 'rmnd_hybridauth';

    /**
     * @todo is this needed
     * @return array
     */
    public static function getSettings(): array
    {
        $configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(self::EXTENSTION_KEY);

        if(!\is_array($configuration)) {
            return [];
        }

        return $configuration;
    }

    /**
     *
     * @return bool
     */
    public static function isAskBeforeCreatingNewFeUser(): bool
    {
        $value = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(self::EXTENSTION_KEY, 'askBeforeCreatingNewFeUser');

        if(empty($value)) {
            return false;
        }

        return ((bool)$value === true);
    }

    /**
     *
     * @return bool
     */
    public static function isAutoDisconnectHybridauthAfterLogin(): bool
    {
        $value = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(self::EXTENSTION_KEY, 'autoDisconnectHybridauthAfterLogin');

        if(empty($value)) {
            return false;
        }

        return ((bool)$value === true);
    }

    /**
     *
     * @return bool
     */
    public static function isUseEmailAddressAsUsername(): bool
    {
        $value = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(self::EXTENSTION_KEY, 'useEmailAddressAsUsername');

        if(empty($value)) {
            return false;
        }

        return ((bool)$value === true);
    }

    /**
     *
     * @return bool
     */
    public static function isUseAfterAuthLoginService(): bool
    {
        $value = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(self::EXTENSTION_KEY, 'useAfterAuthLoginService');

        if(empty($value)) {
            return false;
        }

        return ((bool)$value === true);
    }
}
