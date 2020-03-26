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
     * @return array
     */

    public function connect(ProviderSettings $providerSettings): ?AdapterInterface
    {
        $providerIdentifier = $providerSettings->getHybridauthIdentifier();
        $providerConfig = $providerSettings->getHybridauthConfiguration();
        $providerConfig['active'] = true;

        $config = [
            'providers' => [
                $providerIdentifier => $providerConfig
            ]
        ];

        $hybridauth = new Hybridauth( $config );

        try {
            $adapter = $hybridauth->authenticate($providerIdentifier);
        } catch (Exception $ex) {
            return null;
        }

        return $adapter;
    }
}
