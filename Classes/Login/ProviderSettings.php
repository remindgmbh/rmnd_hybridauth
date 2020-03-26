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
}
