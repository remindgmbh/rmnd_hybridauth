<?php

namespace Remind\RmndHybridauth\Controller;

use Remind\RmndHybridauth\Login\ProviderSettings;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class LoginController extends \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController
{
    /**
     * Action argument with selected provider
     * @var string
     */
    const ARGUMENT_PROVIDER = 'provider';

    /**
     * TypoScript object with provider settings
     * @var string
     */
    const SETTINGS_PROVIDERS = 'providers';

    /**
     * Authenticate with given provider
     * @return void
     */
    public function authAction(): void
    {
        $providerSettings = $this->getProviderSettings();

        if($providerSettings === null) {
            $this->forward('noProviderErrorAction');
        }

        $hybridauthConnecter = new \Remind\RmndHybridauth\Utility\HybridauthConnecter();
        $hybridauthConnecter->connect($providerSettings);
    }

    /**
     * Display error if given provider is not configured or active
     * @return void
     */
    public function noProviderErrorAction(): void
    {

    }

    /**
     *
     * @return ProviderSettings|null
     */
    protected function getProviderSettings(): ?ProviderSettings
    {

        if(!$this->arguments->hasArgument(self::ARGUMENT_PROVIDER)) {
            return null;
        }

        $argumentProvider = $this->arguments->getArgument(self::ARGUMENT_PROVIDER);

        if(!\is_string($argumentProvider) || empty($argumentProvider)) {
            return null;
        }

        $provider = \Remind\RmndHybridauth\Login\ProviderSettingsLoader::getSingleProviderSettings($argumentProvider, $this->settings, self::SETTINGS_PROVIDERS);

        /* Return null if provider is not active */
        if(!$provider->getIsActive()) {
            return null;
        }

        return $provider;
    }
}
