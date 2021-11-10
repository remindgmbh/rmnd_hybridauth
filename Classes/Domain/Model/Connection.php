<?php

declare(strict_types=1);

namespace Remind\RmndHybridauth\Domain\Model;

/***
 *
 * This file is part of the "REMIND - Hybridauth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Marco Wegner <m.wegner@remind.de>
 *
 ***/

/**
 *
 */
class Connection extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     *
     * @var string
     */
    protected $identifier = '';

    /**
     *
     * @var string
     */
    protected $provider = '';

    /**
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $feUser = null;

    /**
     *
     * @var string
     */
    protected $loginHash = '';

    /**
     * Last check with hybridauth provider
     * @var int
     */
    protected $lastValidation = 0;

    /**
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     *
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser|null
     */
    public function getFeUser(): ?\TYPO3\CMS\Extbase\Domain\Model\FrontendUser
    {
        return $this->feUser;
    }

    /**
     *
     * @param string $identifier
     * @return void
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     *
     * @param string $provider
     * @return void
     */
    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    /**
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser|null $feUser
     * @return void
     */
    public function setFeUser(?\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser): void
    {
        $this->feUser = $feUser;
    }

    /**
     *
     * @return string
     */
    public function getLoginHash(): string
    {
        return $this->loginHash;
    }

    /**
     *
     * @param string $loginHash
     * @return void
     */
    public function setLoginHash(string $loginHash): void
    {
        $this->loginHash = $loginHash;
    }

    /**
     *
     * @return int
     */
    public function getLastValidation(): int
    {
        return $this->lastValidation;
    }

    /**
     *
     * @param int $lastValidation
     * @return void
     */
    public function setLastValidation(int $lastValidation): void
    {
        $this->lastValidation = $lastValidation;
    }
}
