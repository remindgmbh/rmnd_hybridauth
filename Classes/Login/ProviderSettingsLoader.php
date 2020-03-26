<?php

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
    const FIELD_ACTIVE = 'active';

    /**
     * Identifier of provider in HybridAuth library
     * @var string
     */
    const FIELD_HYBRIDAUTH_IDENTIFIER = 'identifier';

    /**
     * Parameters for hybridauth provider
     * @var string
     */
    const FIELD_HYBRIDAUTH_CONFIGURATION = 'hybridauthConfiguration';

    /**
     *
     * @param array $configuration extension typoscript settings
     * @param string $field subfield of given configuration array
     * @return array ProviderSettings[]
     */
    static public function getProviderSettings(array $configuration, string $field = ''): array
    {
        /* Use subfield of given array as configuration */
        if(!empty($field)) {
            if(empty($configuration[$field])) {
                return [];
            }

            /* Just use subfield if set */
            $configuration = $configuration[$field];
        }

        $providers = [];
        foreach($configuration as $providerName => $providerConfiguration) {
            $provider = new ProviderSettings();
            $provider->setName($providerName);

            if(!empty($providerConfiguration[self::FIELD_ACTIVE])) {
                $provider->setIsActive(true);
            }

            if(!empty($providerConfiguration[self::FIELD_HYBRIDAUTH_IDENTIFIER])) {
                $identifier = (string)$providerConfiguration[self::FIELD_HYBRIDAUTH_IDENTIFIER];
                $provider->setHybridauthIdentifier($identifier);
            }

            if(\is_array($providerConfiguration[self::FIELD_HYBRIDAUTH_CONFIGURATION])) {
                $configuration = $providerConfiguration[self::FIELD_HYBRIDAUTH_CONFIGURATION];
                $provider->setHybridauthConfiguration($configuration);
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
    static public function getSingleProviderSettings(string $name, array $configuration, string $field = ''): ProviderSettings
    {
        $providers = self::getProviderSettings($configuration, $field);

        /* If provider isn't configured return deactivated provider */
        if(empty($providers[$name])) {
            $provider = new ProviderSettings();
            $provider->setName($name);
            $provider->setIsActive(false);
            return $provider;
        }

        return $providers[$name];
    }
}
