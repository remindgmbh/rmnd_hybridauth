<?php

declare(strict_types=1);

namespace Remind\RmndHybridauth\Controller;

use Exception;
use ReflectionClass;
use Remind\RmndHybridauth\Domain\Model\Connection;
use Remind\RmndHybridauth\Login\BaseUserMapper;
use Remind\RmndHybridauth\Login\HashGenerator;
use Remind\RmndHybridauth\Login\ProviderSettings;
use Remind\RmndHybridauth\Login\ProviderSettingsLoader;
use Remind\RmndHybridauth\Login\UserMapperInterface;
use Remind\RmndHybridauth\Traits\ConnectionRepositoryTrait;
use Remind\RmndHybridauth\Utility\ExtensionSettingsUtility;
use Remind\RmndHybridauth\Utility\HybridauthConnector;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * LoginController
 */
class LoginController extends ActionController
{
    /**
     * Get connectionRepository
     */
    use ConnectionRepositoryTrait;

    /**
     * Action argument with selected provider
     * @var string
     */
    public const ARGUMENT_PROVIDER = 'provider';

    /**
     * Login token argument for fe_login
     * @var string
     */
    public const ARGUMENT_LOGIN_TOKEN = 'token';

    /**
     * connection uid argument for fe_login
     * @var string
     */
    public const ARGUMENT_CONNECTION_UID = 'connection';

    /**
     * TypoScript object with provider settings
     * @var string
     */
    public const SETTINGS_PROVIDERS = 'providers';

    /**
     * TypoScript setting for error page (only used when no provider selected)
     * @var string
     */
    public const SETTINGS_ERROR_PID = 'errorPid';

    /**
     * Class which maps provider information to fe_users
     * @var string
     */
    public const SETTINGS_USER_MAPPER_CLASS = 'userMapperClass';

    /**
     * @var string
     */
    public const TRANSLATION_FILE = 'LLL:EXT:rmnd_hybridauth/Resources/Private/Language/locallang.xlf:';

    /**
     * Authenticate with given provider
     *
     * @return void
     */
    public function authAction(): void
    {
        $providerSettings = $this->getProviderSettings();
        $errorPid = (int) $this->settings[self::SETTINGS_ERROR_PID];

        if ($providerSettings === null) {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    self::TRANSLATION_FILE . 'error.provider_not_configured',
                    'rmnd_hybridauth'
                ),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('authError', null, null, $this->getAfterLoginArguments(), $errorPid, 0, 303);
        }

        $loggedIn = $this->login($providerSettings);

        if (!$loggedIn) {
            $errorPidRedirect = $providerSettings->getRedirectPidAfterError() ?? $errorPid;
            $this->redirect('authError', null, null, $this->getAfterLoginArguments(), $errorPidRedirect, 0, 303);
        }

        $this->redirect(
            'loginComplete',
            null,
            null,
            $this->getAfterLoginArguments(),
            $providerSettings->getRedirectPidAfterLogin(),
            0,
            303
        );
    }

    /**
     *
     * @param ProviderSettings $providerSettings
     * @return bool is user logged in?
     */
    protected function login(ProviderSettings $providerSettings): bool
    {
        /* Current url without extra parameter, this action will be called after redirect again */
        $callbackUrl = $this->getCallbackUri();

        /* Get mapper for provider information to fe_user */
        $userMapper = $this->getUserMapper();
        $hybridauthConnector = new HybridauthConnector($userMapper);

        try {
            /* Try connecting by redirecting when not already connected */
            $connection = $hybridauthConnector->connect($providerSettings, $callbackUrl);

            if (empty($connection)) {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        self::TRANSLATION_FILE . 'error.user_not_created',
                        'rmnd_hybridauth'
                    ),
                    '',
                    AbstractMessage::ERROR
                );

                return false;
            }

            /*
             * Hybridauth didn't redirect and the connector created a fe_users record
             */

            /* Use loginService or directly login in controller */
            $isUseLoginService = ExtensionSettingsUtility::isUseAfterAuthLoginService();

            if ($isUseLoginService) {
                /* Create auth token and redirect to current page but with another action */
                $this->redirectToAfterAuthUri($connection, $providerSettings);
                return true;
            }

            /* Login service is not activated, so directly login user here (dirty method) */
            $isLoggedIn = $this->loginUser($connection->getFeUser());
        } catch (Exception $ex) {
            $this->addFlashMessage(
                LocalizationUtility::translate(self::TRANSLATION_FILE . 'error.login_exception', 'rmnd_hybridauth'),
                '',
                AbstractMessage::ERROR
            );

            return false;
        }

        if (!$isLoggedIn) {
            $this->addFlashMessage(
                LocalizationUtility::translate(self::TRANSLATION_FILE . 'error.typo3_login_failed', 'rmnd_hybridauth'),
                '',
                AbstractMessage::ERROR
            );
        }

        return $isLoggedIn;
    }

    /**
     *
     * @return UserMapperInterface
     */
    protected function getUserMapper(): UserMapperInterface
    {
        if (empty($this->settings[self::SETTINGS_USER_MAPPER_CLASS])) {
            return new BaseUserMapper($this->objectManager);
        }

        $class = $this->settings[self::SETTINGS_USER_MAPPER_CLASS];

        $userMapper = new $class($this->objectManager);

        return $userMapper;
    }

    /**
     *
     * @param Connection $connection
     * @param ProviderSettings $providerSettings
     * @return void
     */
    protected function redirectToAfterAuthUri(Connection $connection, ProviderSettings $providerSettings): void
    {
        $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        $token = HashGenerator::generateRandomHash(30);
        /* Generate hash with ip and random token */
        $loginHash = HashGenerator::generateLoginHash($ip, $token);
        $connection->setLoginHash($loginHash);

        /* Save login hash to database, for comparison in auth service */
        $this->connectionRepository->update($connection);

        /* Persist login hash */
        $this->objectManager->get(PersistenceManager::class)->persistAll();

        /* Generate target url */
        $redirectUri = $this->getAfterAuthUri($connection, $token);

        /* Redirect, so the auth service kicks in and logs in user */
        HttpUtility::redirect(
            $redirectUri,
            HttpUtility::HTTP_STATUS_303
        );
    }

    /**
     * Same method used as by femanager
     *
     * @todo check if login can fail
     * @param FrontendUser $user
     * @return void
     */
    protected function loginUser(FrontendUser $user): bool
    {
        /* @todo better solution than globals? */
        /* @var $tsfe TypoScriptFrontendController */
        $tsfe = $GLOBALS['TSFE'];

        $tsfe->fe_user->checkPid = false;
        $info = $tsfe->fe_user->getAuthInfoArray();
        $extraWhere = ' AND uid = ' . (int)$user->getUid();

        $userData = $tsfe->fe_user->fetchUserRecord($info['db_user'], $user->getUsername(), $extraWhere);

        if (empty($userData)) {
            return false;
        }

        $tsfe->fe_user->createUserSession($userData);
        $tsfe->fe_user->user = $tsfe->fe_user->fetchUserSession();
        $tsfe->fe_user->setAndSaveSessionData('ses', true);

        $reflection = new ReflectionClass($tsfe->fe_user);
        $setSessionCookie = $reflection->getMethod('setSessionCookie');
        $setSessionCookie->setAccessible(true);
        $setSessionCookie->invoke($tsfe->fe_user);

        return true;
    }

    /**
     * Rebuild current url. This is the easiest solution to prevent mistakes.
     * This url needs to be exactly the url set in facebook app login settings.
     * @return string
     */
    protected function getCallbackUri(): string
    {
        $this->uriBuilder->reset();
        $this->uriBuilder->setAddQueryString(false);
        $this->uriBuilder->setCreateAbsoluteUri(true);
        $this->uriBuilder->setTargetPageType($GLOBALS['TSFE']->type);
        $this->uriBuilder->setUseCacheHash(false);
        /* Add arguments like provider to redirect */
        $arguments = $this->request->getArguments();
        $callback = $this->uriBuilder->uriFor('auth', $arguments);
        return $callback;
    }

    /**
     * Rebuild current url but call other action and add login parameter
     * @return string
     */
    protected function getAfterAuthUri(Connection $connection, string $token): string
    {
        $this->uriBuilder->reset();
        $this->uriBuilder->setAddQueryString(false);
        $this->uriBuilder->setCreateAbsoluteUri(true);
        $this->uriBuilder->setTargetPageType($GLOBALS['TSFE']->type);
        $this->uriBuilder->setUseCacheHash(false);

        /* Service only is called with logintype set */
        $arguments = [
            'logintype' => 'login'
        ];

        $this->uriBuilder->setArguments($arguments);
        /* Add arguments like provider to redirect */
        $actionArguments = $this->request->getArguments();

        $actionArguments[self::ARGUMENT_LOGIN_TOKEN] = $token;
        $actionArguments[self::ARGUMENT_CONNECTION_UID] = $connection->getUid();

        $redirectUri = $this->uriBuilder->uriFor('afterAuth', $actionArguments);

        return $redirectUri;
    }

    /**
     * Check if fe_user object is set in globals
     *
     * @todo is this a reliable option to check if user is logged in
     * @todo move to utility
     * @return bool
     */
    protected function isUserLoggedIn(): bool
    {
        /* @var $tsfe TypoScriptFrontendController */
        $tsfe = $GLOBALS['TSFE'];

        if (empty($tsfe)) {
            return false;
        }

        if (empty($tsfe->fe_user)) {
            return false;
        }

        if (empty($tsfe->fe_user->user)) {
            return false;
        }

        return true;
    }

    /**
     * Redirect here to let the authentication magic work and then redirect
     * @return void
     */
    public function afterAuthAction(): void
    {
        $loggedIn = $this->isUserLoggedIn();
        $providerSettings = $this->getProviderSettings();
        $errorPid = (int)$this->settings[self::SETTINGS_ERROR_PID];

        if ($providerSettings === null) {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    self::TRANSLATION_FILE . 'error.provider_not_configured',
                    'rmnd_hybridauth'
                ),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('authError', null, null, $this->getAfterLoginArguments(), $errorPid, 0, 303);
        }

        if (!$loggedIn) {
            $this->addFlashMessage(
                LocalizationUtility::translate(self::TRANSLATION_FILE . 'error.typo3_login_failed', 'rmnd_hybridauth'),
                '',
                AbstractMessage::ERROR
            );

            $errorPidRedirect = $providerSettings->getRedirectPidAfterError() ?? $errorPid;
            $this->redirect('authError', null, null, $this->getAfterLoginArguments(), $errorPidRedirect, 0, 303);
        }

        $this->redirect(
            'loginComplete',
            null,
            null,
            $this->getAfterLoginArguments(),
            $providerSettings->getRedirectPidAfterLogin(),
            0,
            303
        );
    }

    /**
     * Action that shows information after successful login
     * @return void
     */
    public function loginCompleteAction(): void
    {
        $this->view->assign('arguments', $this->getAfterLoginArguments());
    }

    /**
     * Output error flashMessages after unsuccessul provider connect
     * @return void
     */
    public function authErrorAction(): void
    {
        $this->view->assign('arguments', $this->getAfterLoginArguments());
    }

    /**
     *
     * @return ProviderSettings|null
     */
    protected function getProviderSettings(): ?ProviderSettings
    {

        if (!$this->request->hasArgument(self::ARGUMENT_PROVIDER)) {
            return null;
        }

        $argumentProvider = $this->request->getArgument(self::ARGUMENT_PROVIDER);

        if (!is_string($argumentProvider) || empty($argumentProvider)) {
            return null;
        }

        $provider = ProviderSettingsLoader::getSingleProviderSettings(
            $argumentProvider,
            $this->settings,
            self::SETTINGS_PROVIDERS
        );

        /* Return null if provider is not active */
        if (!$provider->getIsActive()) {
            return null;
        }

        return $provider;
    }

    /**
     * Get arguments that can be used after login or failed login attempt
     * @return array
     */
    protected function getAfterLoginArguments(): array
    {
        $arguments = [];

        if ($this->request->hasArgument(self::ARGUMENT_PROVIDER)) {
            $arguments[self::ARGUMENT_PROVIDER] = $this->request->getArgument(self::ARGUMENT_PROVIDER);
        }

        if ($this->request->hasArgument(self::ARGUMENT_CONNECTION_UID)) {
            $arguments[self::ARGUMENT_CONNECTION_UID] = $this->request->getArgument(self::ARGUMENT_CONNECTION_UID);
        }

        return $arguments;
    }
}
