<?php

declare(strict_types=1);

namespace Remind\RmndHybridauth\Login;

use Hybridauth\User\Profile;
use Remind\RmndHybridauth\Domain\Model\Connection;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * UserMapperInterface
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
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @return FrontendUser
     */
    public function createUser(ProviderSettings $providerSettings, Profile $profile): FrontendUser;

    /**
     *
     * @param Connection $connection
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @return void
     */
    public function updateUser(Connection $connection, ProviderSettings $providerSettings, Profile $profile): void;
}
