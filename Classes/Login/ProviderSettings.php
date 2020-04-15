<?php

namespace Remind\RmndHybridauth\Login;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class ProviderSettings
{
    /**
     * Name in typoscript settings
     * @var string
     */
    private $name = '';

    /**
     * Is provider configured
     * @var bool
     */
    private $isActive = false;

    /**
     * Name to load provider from hybridauth
     * @var string
     */
    private $hybridauthIdentifier = '';

    /**
     *
     * @var array
     */
    private $hybridauthConfiguration = [];

    /**
     *
     * @var int
     */
    private $userPid = 0;

    /**
     *
     * @var int
     */
    private $userGroup = 0;

    /**
     *
     * @var int
     */
    private $redirectPidAfterLogin = 0;

    /**
     *
     * @var int
     */
    private $redirectPidAfterError = 0;

    /**
     * Update user with provider data after login
     * @var bool
     */
    private $isUserUpdateEnabled = false;

    /**
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     *
     * @return string
     */
    public function getHybridauthIdentifier(): string
    {
        return $this->hybridauthIdentifier;
    }

    /**
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     *
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param string $hybridauthIdentifier
     * @return void
     */
    public function setHybridauthIdentifier(string $hybridauthIdentifier): void
    {
        $this->hybridauthIdentifier = $hybridauthIdentifier;
    }

    /**
     *
     * @return array
     */
    public function getHybridauthConfiguration(): array
    {
        return $this->hybridauthConfiguration;
    }

    /**
     *
     * @param array $hybridauthConfiguration
     * @return void
     */
    public function setHybridauthConfiguration(array $hybridauthConfiguration): void
    {
        $this->hybridauthConfiguration = $hybridauthConfiguration;
    }

    /**
     *
     * @return int
     */
    public function getUserPid(): int
    {
        return $this->userPid;
    }

    /**
     *
     * @param int $userPid
     * @return void
     */
    public function setUserPid(int $userPid): void
    {
        $this->userPid = $userPid;
    }

    /**
     *
     * @return int
     */
    public function getRedirectPidAfterLogin(): int
    {
        return $this->redirectPidAfterLogin;
    }

    /**
     *
     * @return int
     */
    public function getRedirectPidAfterError(): int
    {
        return $this->redirectPidAfterError;
    }

    /**
     *
     * @param int $redirectPidAfterLogin
     * @return void
     */
    public function setRedirectPidAfterLogin(int $redirectPidAfterLogin): void
    {
        $this->redirectPidAfterLogin = $redirectPidAfterLogin;
    }

    /**
     *
     * @param int $redirectPidAfterError
     * @return void
     */
    public function setRedirectPidAfterError(int $redirectPidAfterError): void
    {
        $this->redirectPidAfterError = $redirectPidAfterError;
    }

    /**
     *
     * @return int
     */
    public function getUserGroup(): int
    {
        return $this->userGroup;
    }

    /**
     *
     * @param int $userGroup
     * @return void
     */
    public function setUserGroup(int $userGroup): void
    {
        $this->userGroup = $userGroup;
    }

    /**
     *
     * @return bool
     */
    public function getIsUserUpdateEnabled(): bool
    {
        return $this->isUserUpdateEnabled;
    }

    /**
     *
     * @param bool $isUserUpdateEnabled
     * @return void
     */
    public function setIsUserUpdateEnabled(bool $isUserUpdateEnabled): void
    {
        $this->isUserUpdateEnabled = $isUserUpdateEnabled;
    }


}
