<?php

namespace Remind\RmndHybridauth\Login;

use Hybridauth\User\Profile;
use Remind\RmndHybridauth\Domain\Model\Connection;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
interface UserMapperInterface
{
    /**
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager);

    /**
     *
     * @param \Remind\RmndHybridauth\Login\ProviderSettings $providerSettings
     * @param Profile $profile
     * @return FrontendUser
     */
    public function createUser(ProviderSettings $providerSettings, Profile $profile): FrontendUser;

    /**
     *
     * @param Connection $connection
     * @param \Remind\RmndHybridauth\Login\ProviderSettings $providerSettings
     * @param Profile $profile
     * @return void
     */
    public function updateUser(Connection $connection, ProviderSettings $providerSettings, Profile $profile): void;
}
