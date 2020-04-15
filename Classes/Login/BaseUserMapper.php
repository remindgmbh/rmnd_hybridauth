<?php

namespace Remind\RmndHybridauth\Login;

use Hybridauth\User\Profile;
use Remind\RmndHybridauth\Domain\Model\Connection;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class BaseUserMapper implements UserMapperInterface
{
    /**
     *
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     *
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository = null;

    /**
     *
     * @var FrontendUserGroupRepository
     */
    protected $frontendUserGroupRepository = null;

    /**
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->init();
    }

    /**
     *
     * @return void
     */
    protected function init(): void
    {
        $this->frontendUserRepository = $this->objectManager->get(FrontendUserRepository::class);
        $this->frontendUserGroupRepository = $this->objectManager->get(FrontendUserGroupRepository::class);
    }

    /**
     *
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @return FrontendUser
     */
    public function createUser(ProviderSettings $providerSettings, Profile $profile): FrontendUser
    {
        $frontendUser = new FrontendUser();
        $this->updateBaseUserInformation($frontendUser, $providerSettings, $profile);
        $this->frontendUserRepository->add($frontendUser);
    }

    /**
     * This is called when creating new users
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @return void
     */
    protected function updateBaseUserInformation(FrontendUser $frontendUser, ProviderSettings $providerSettings, Profile $profile): void
    {
        /*
         * @todo optional(ts) use email as username if no other user with adress exists
         */
        $frontendUser->setUsername($providerSettings->getName() . '-' . $profile->identifier);
        $frontendUser->setPassword($this->generateRandomPassword());

        $frontendUser->setEmail($profile->emailVerified);
        $frontendUser->setFirstName($profile->firstName);
        $frontendUser->setLastName($profile->lastName);

        $frontendUser->setPid($providerSettings->getUserPid());
        $usergroup = $this->getFrontendUserGroup($providerSettings->getUserGroup());
        if(!empty($usergroup) && !$frontendUser->getUsergroup()->contains($usergroup)) {
            $frontendUser->addUsergroup($usergroup);
        }
    }

    /**
     *
     * FrontendUser already existed and user logged in again with provider.
     * Most information will not be updated by default,
     * to give the possibility to change them in frontend.
     *
     * @param \Remind\RmndHybridauth\Login\Connection $connection
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @return void
     */
    public function updateUser(Connection $connection, ProviderSettings $providerSettings, Profile $profile): void
    {
        /* Only update email */
        $connection->getFeUser()->setEmail($profile->emailVerified);
        $this->frontendUserRepository->update($connection->getFeUser());
    }

    /**
     *
     * @param int $uid
     * @return FrontendUserGroup|null
     */
    protected function getFrontendUserGroup(int $uid): ?FrontendUserGroup
    {
        $usergroup = $this->frontendUserGroupRepository->findByUid($uid);
        return $usergroup;
    }

    /**
     * Generate random password for newly created users. Not hashed or whatsoever.
     * @todo hash password or make sure user can never be logged in with this password.
     * @return string
     */
    protected function generateRandomPassword(): string
    {
        $pass = \substr(\md5(\openssl_random_pseudo_bytes(20)), -50);
        return $pass;
    }


}
