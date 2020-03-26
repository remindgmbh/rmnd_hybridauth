<?php

namespace Remind\RmndHybridauth\Utility;

use Exception;
use Hybridauth\Adapter\AdapterInterface;
use Hybridauth\Hybridauth;
use Remind\RmndHybridauth\Login\ProviderSettings;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class HybridauthConnecter
{

    /**
     *
     * @param ProviderSettings $providerSettings
     * @param string $callback
     * @return array
     */

    public function connect(ProviderSettings $providerSettings, string $callback): ?AdapterInterface
    {
        $providerIdentifier = $providerSettings->getHybridauthIdentifier();
        $providerConfig = $providerSettings->getHybridauthConfiguration();
        $providerConfig['enabled'] = true;
        $providerConfig['callback'] = $callback;

        $config = [
            'providers' => [
                $providerIdentifier => $providerConfig
            ]
        ];

        $hybridauth = new Hybridauth( $config );

        $adapter = $hybridauth->authenticate($providerIdentifier);

        return $adapter;
    }
}
