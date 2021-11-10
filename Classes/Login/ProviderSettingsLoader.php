<?php

declare(strict_types=1);

namespace Remind\RmndHybridauth\Login;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class ProviderSettingsLoader
{
    /**
     * Is provider active
     * @var string
     */
    public const FIELD_ACTIVE = 'active';

    /**
     * Identifier of provider in HybridAuth library
     * @var string
     */
    public const FIELD_HYBRIDAUTH_IDENTIFIER = 'identifier';

    /**
     * Parameters for hybridauth provider
     * @var string
     */
    public const FIELD_HYBRIDAUTH_CONFIGURATION = 'hybridauthConfiguration';

    /**
     * Where do users which are connected to provider get stored
     * @var string
     */
    public const FIELD_USER_PID = 'userPid';

    /**
     * Which group do new users get assigned
     * @var string
     */
    public const FIELD_USER_GROUP = 'userGroup';

    /**
     * Redirect to pid after login
     * @var string
     */
    public const FIELD_REDIRECT_PID_AFTER_LOGIN = 'redirectPidAfterLogin';

    /**
     * Redirect to pid after error
     * @var string
     */
    public const FIELD_REDIRECT_PID_AFTER_ERROR = 'redirectPidAfterError';

    /**
     * Update user with provider data after login
     * @var string
     */
    public const FIELD_IS_UPDATE_USER = 'isUserUpdateEnabled';

    /**
     *
     * @param array $configuration extension typoscript settings
     * @param string $field subfield of given configuration array
     * @return array ProviderSettings[]
     */
    public static function getProviderSettings(array $configuration, string $field = ''): array
    {
        /* Use subfield of given array as configuration */
        if (!empty($field)) {
            if (empty($configuration[$field])) {
                return [];
            }

            /* Just use subfield if set */
            $configuration = $configuration[$field];
        }

        $providers = [];
        foreach ($configuration as $providerName => $providerConfiguration) {
            $provider = new ProviderSettings();
            $provider->setName($providerName);

            if (!empty($providerConfiguration[self::FIELD_ACTIVE])) {
                $provider->setIsActive(true);
            }

            if (!empty($providerConfiguration[self::FIELD_HYBRIDAUTH_IDENTIFIER])) {
                $identifier = (string)$providerConfiguration[self::FIELD_HYBRIDAUTH_IDENTIFIER];
                $provider->setHybridauthIdentifier($identifier);
            }

            if (!empty($providerConfiguration[self::FIELD_USER_PID])) {
                $pid = (int)$providerConfiguration[self::FIELD_USER_PID];
                $provider->setUserPid($pid);
            }

            if (!empty($providerConfiguration[self::FIELD_USER_GROUP])) {
                $group = (int)$providerConfiguration[self::FIELD_USER_GROUP];
                $provider->setUserGroup($group);
            }

            if (!empty($providerConfiguration[self::FIELD_REDIRECT_PID_AFTER_LOGIN])) {
                $loginSucessPid = (int)$providerConfiguration[self::FIELD_REDIRECT_PID_AFTER_LOGIN];
                $provider->setRedirectPidAfterLogin($loginSucessPid);
            }

            if (!empty($providerConfiguration[self::FIELD_REDIRECT_PID_AFTER_ERROR])) {
                $loginErrorPid = (int)$providerConfiguration[self::FIELD_REDIRECT_PID_AFTER_ERROR];
                $provider->setRedirectPidAfterError($loginErrorPid);
            }

            if (\is_array($providerConfiguration[self::FIELD_HYBRIDAUTH_CONFIGURATION])) {
                $configuration = $providerConfiguration[self::FIELD_HYBRIDAUTH_CONFIGURATION];
                $provider->setHybridauthConfiguration($configuration);
            }

            if (!empty($providerConfiguration[self::FIELD_IS_UPDATE_USER])) {
                $isUpdateUser = (bool)$providerConfiguration[self::FIELD_IS_UPDATE_USER];
                $provider->setIsUserUpdateEnabled($isUpdateUser);
            }

            $providers[$providerName] = $provider;
        }

        return $providers;
    }

    /**
     *
     * @param string $name
     * @param array $configuration
     * @param string $field
     * @return ProviderSettings
     */
    public static function getSingleProviderSettings(
        string $name,
        array $configuration,
        string $field = ''
    ): ProviderSettings {
        $providers = self::getProviderSettings($configuration, $field);

        /* If provider isn't configured return deactivated provider */
        if (empty($providers[$name])) {
            $provider = new ProviderSettings();
            $provider->setName($name);
            $provider->setIsActive(false);
            return $provider;
        }

        return $providers[$name];
    }
}
