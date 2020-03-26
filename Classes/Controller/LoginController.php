<?php

namespace Remind\RmndHybridauth\Controller;

use Remind\RmndHybridauth\Login\ProviderSettings;
use Remind\RmndHybridauth\Login\ProviderSettingsLoader;
use Remind\RmndHybridauth\Utility\HybridauthConnecter;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class LoginController extends ActionController
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
            $this->forward('noProviderError');
        }

        $this->uriBuilder->setAddQueryString(true);
        $this->uriBuilder->setCreateAbsoluteUri(true);
        $this->uriBuilder->setTargetPageType($GLOBALS['TSFE']->type);
        $this->uriBuilder->setUseCacheHash(false);
        $callback = $this->uriBuilder->uriFor('auth', $this->request->getArguments());

        $hybridauthConnecter = new HybridauthConnecter();
        $adapter = $hybridauthConnecter->connect($providerSettings, $callback);
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

        if(!$this->request->hasArgument(self::ARGUMENT_PROVIDER)) {
            return null;
        }

        $argumentProvider = $this->request->getArgument(self::ARGUMENT_PROVIDER);

        if(!\is_string($argumentProvider) || empty($argumentProvider)) {
            return null;
        }

        $provider = ProviderSettingsLoader::getSingleProviderSettings($argumentProvider, $this->settings, self::SETTINGS_PROVIDERS);

        /* Return null if provider is not active */
        if(!$provider->getIsActive()) {
            return null;
        }

        return $provider;
    }
}
