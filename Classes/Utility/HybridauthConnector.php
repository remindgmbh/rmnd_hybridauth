<?php

declare(strict_types=1);

namespace Remind\RmndHybridauth\Utility;

use Exception;
use Hybridauth\Hybridauth;
use Hybridauth\User\Profile;
use Remind\RmndHybridauth\Domain\Model\Connection;
use Remind\RmndHybridauth\Domain\Repository\ConnectionRepository;
use Remind\RmndHybridauth\Login\ProviderSettings;
use Remind\RmndHybridauth\Login\UserMapperInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Connect with hybridauth to providers and create and connect to fe_users
 *
 * @todo maybe use dependency injection
 * @todo check visibility on class members
 */
class HybridauthConnector
{
    /**
     *
     * @var UserMapperInterface
     */
    private ?UserMapperInterface $userMapper = null;

    /**
     *
     * @var ConnectionRepository
     */
    private ?ConnectionRepository $connectionRepository = null;

    /**
     *
     * @var ObjectManager
     */
    private ?ObjectManager $objectManager = null;

    /**
     *
     * @var PersistenceManager
     */
    private ?PersistenceManager $persistenceManager = null;

    /**
     * Init repositories and persistenceManager
     *
     * @todo maybe use traits or stuff
     *
     * @param UserMapperInterface $userMapper
     */
    public function __construct(UserMapperInterface $userMapper)
    {
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->connectionRepository = $this->objectManager->get(ConnectionRepository::class);
        $this->userMapper = $userMapper;
    }

    /**
     *
     * @param ProviderSettings $providerSettings
     * @param string $callback
     * @return Connection
     */
    public function connect(ProviderSettings $providerSettings, string $callback): Connection
    {
        $providerIdentifier = $providerSettings->getHybridauthIdentifier();
        $providerConfig = $providerSettings->getHybridauthConfiguration();
        $providerConfig['enabled'] = true;
        $providerConfig['callback'] = $callback;

        $config = [
            'providers' => [
                $providerIdentifier => $providerConfig
            ]
        ];

        $hybridauth = new Hybridauth($config);

        /*
         * This will redirect the user to the provider (eg facebook) page on first call.
         * This same method will be called on redirect back from provider
         * to receive the data (without redirect).
         */
        $adapter = $hybridauth->authenticate($providerIdentifier);

        $profile = $adapter->getUserProfile();

        /* Load or create connection and fe_user */
        $connection = $this->getConnectionWithUser($providerSettings, $profile, true);

        /* Update lastValidation field (needed for hashLogin) */
        $connection->setLastValidation(time());
        $this->connectionRepository->update($connection);
        $this->persistenceManager->persistAll();

        return $connection;
    }

    /**
     *
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @return Connection|null
     * @throws Exception
     */
    protected function getConnection(ProviderSettings $providerSettings, Profile $profile): ?Connection
    {
        /* Absolutely needed, not sure if this even can happen */
        if (empty($profile->identifier)) {
            throw new Exception('Profile loaded but identifier is empty.');
        }

        $provider = $providerSettings->getName();
        $userPid = $providerSettings->getUserPid();
        $connection = $this->connectionRepository->findConnection($provider, $profile->identifier, $userPid);

        return $connection;
    }

    /**
     *
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @param bool $createIfNotExisting
     * @return Connection|null
     * @throws Exception
     */
    protected function getConnectionWithUser(
        ProviderSettings $providerSettings,
        Profile $profile,
        bool $createIfNotExisting = true
    ): ?Connection {

        $connection = $this->getConnection($providerSettings, $profile);
        $user = null;

        if (!empty($connection) && !empty($connection->getFeUser())) {
            $this->updateUser($connection, $providerSettings, $profile);
            return $connection;
        }

        if (!$createIfNotExisting) {
            return null;
        }

        /* Connection exists but user was moved or deleted */
        if (!empty($connection) && empty($connection->getFeUser())) {
            /* Create new user and add existing connection. */
            $user = $this->createUser($providerSettings, $profile);
            $connection->setFeUser($user);

            $this->connectionRepository->update($connection);

            $this->persistenceManager->persistAll();

            return $connection;
        }

        /* Nothing found, so create the whole thing */
        return $this->createConnectionWithUser($providerSettings, $profile);
    }

    /**
     * Create connection and link user
     *
     * @todo signalslot
     * @todo persistAll?
     *
     * @param ProviderSettings $providerSettings
     * @param FrontendUser $user
     * @param Profile $profile
     * @return Connection
     */
    protected function createConnection(
        ProviderSettings $providerSettings,
        FrontendUser $user,
        Profile $profile
    ): Connection {
        $connection = new Connection();
        $connection->setFeUser($user);
        $connection->setProvider($providerSettings->getName());
        $connection->setIdentifier($profile->identifier);
        $connection->setPid($user->getPid());

        $this->connectionRepository->add($connection);
        $this->persistenceManager->persistAll();

        return $connection;
    }

    /**
     *
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @return FrontendUser
     */
    protected function createUser(ProviderSettings $providerSettings, Profile $profile): FrontendUser
    {
        $frontendUser = $this->userMapper->createUser($providerSettings, $profile);
        $this->persistenceManager->persistAll();

        return $frontendUser;
    }

    /**
     *
     * @param Connection $connection
     * @param ProviderSettings $providerSettings
     * @param Profile $profile
     * @return void
     */
    protected function updateUser(Connection $connection, ProviderSettings $providerSettings, Profile $profile): void
    {
        if (!$providerSettings->getIsUserUpdateEnabled()) {
            return;
        }

        $this->userMapper->updateUser($connection, $providerSettings, $profile);
        $this->persistenceManager->persistAll();
    }

    /**
     *
     * @param string $provider
     * @param Profile $profile
     * @return Connection
     */
    protected function createConnectionWithUser(ProviderSettings $providerSettings, Profile $profile): Connection
    {
        /* Create user */
        $frontendUser = $this->createUser($providerSettings, $profile);

        /* Create connection for frontend user */
        $connection = $this->createConnection($providerSettings, $frontendUser, $profile);

        return $connection;
    }
}
