<?php

namespace Remind\RmndHybridauth\Service;

use Remind\RmndHybridauth\Controller\LoginController;
use Remind\RmndHybridauth\Domain\Model\Connection;
use Remind\RmndHybridauth\Domain\Repository\ConnectionRepository;
use Remind\RmndHybridauth\Login\HashGenerator;
use TYPO3\CMS\Core\Authentication\AbstractAuthenticationService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class AfterAuthLoginService extends AbstractAuthenticationService
{

    /**
     * Time that a login hash is valid
     * @var int
     */
    const LOGIN_HASH_EXPIRATION_TIME = 60;

    /**
     * Keeps class name.
     *
     * @var string
     */
    public $prefixId = 'tx_rmndhybridauth_authed';

    /**
     * Keeps extension key.
     *
     * @var string
     */
    public $extKey = 'rmnd_hybridauth';

    /**
     *
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     *
     * @var ConnectionRepository
     */
    protected $connectionRepository = null;

    /**
     * Init functions
     * @return bool
     */
    public function init(): bool
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->connectionRepository = $this->objectManager->get(ConnectionRepository::class);

        return parent::init();
    }

    /**
     * called in service getUserFE, user has still to be authed.
     *
     * @return array|null User or null
     */
    public function getUser(): ?array
    {
        /* only for login try and if password is given */
        if ($this->login['status'] !== 'login') {
            return null;
        }

        $user = $this->getUserFromArguments();

        if (empty($user)) {
            return null;
        }


        return $user;
    }


    /**
     * authUserFE service
     * 100 try with other services
     * 200 = authed
     * @param array|null $user
     * @return int
     */
    public function authUser(?array $user): int
    {
        if (empty($user)) {
            /* Not logged in */
            return 100;
        }

        /* Field set on get user if successfully logged in */
        if ($this->validateLoginCredentials()) {
            /* Successfully logged in */
            return 200;
        }

        /* Not logged in */
        return 100;
    }

    /**
     * Check if imported user
     * @param array $loginData
     * @return array UserArray
     */
    protected function getUserFromArguments(): array
    {
        $connection = $this->getConnectionFromArguments();

        if(empty($connection)) {
            return [];
        }

        $feUser = $connection->getFeUser();

        if(empty($feUser)) {
            return [];
        }

        $user = $this->getRawFeUser($feUser);

        if(empty($user)) {
            return [];
        }

        return $user;
    }

    /**
     *
     * @return array
     */
    protected function getConnectionFromArguments(): ?Connection
    {
        /* Get rmnd_hybridauth login plugin arguments */
        $arguments = GeneralUtility::_GP('tx_rmndhybridauth_login');
        if(empty($arguments)) {
            return [];
        }

        /* Check if connection exists */
        if(empty($arguments[LoginController::ARGUMENT_CONNECTION_UID])) {
            return [];
        }

        /* Check if login token is given */
        if(empty($arguments[LoginController::ARGUMENT_LOGIN_TOKEN])) {
            return [];
        }

        $connectionUid = (int)$arguments[LoginController::ARGUMENT_CONNECTION_UID];

        /*
         * We have to work with a raw result else the fe_users connection will break.
         * The mapping for extbase stuff does not work at this point.
         */
        $connection = $this->connectionRepository->findByUid($connectionUid);

        return $connection;
    }

    /**
     *
     * @return string
     */
    protected function getLoginTokenFromArguments(): string
    {
        /* Get rmnd_hybridauth login plugin arguments */
        $arguments = GeneralUtility::_GP('tx_rmndhybridauth_login');

        if(empty($arguments)) {
            return '';
        }

        /* Check if connection exists */
        if(empty($arguments[LoginController::ARGUMENT_LOGIN_TOKEN])) {
            return '';
        }

        return (string) $arguments[LoginController::ARGUMENT_LOGIN_TOKEN];
    }

    /**
     *
     * @return bool
     */
    protected function validateLoginCredentials(): bool
    {
        $loginToken = $this->getLoginTokenFromArguments();
        $connection = $this->getConnectionFromArguments();

        if(empty($connection)) {
            return false;
        }

        $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');

        /* Validate hash */
        $loginHashMatching = (HashGenerator::isValidLogin(
            $ip,
            $loginToken,
            $connection->getLoginHash())
        );

        $isHashExpired = (\time() > ($connection->getLastValidation() + self::LOGIN_HASH_EXPIRATION_TIME));

        $isValid = ($loginHashMatching && !$isHashExpired);

        if($isValid) {
            /* Only one login possible, after that invalidate login hash */
            $this->invalidateLoginHash($connection);
        }

        return $isValid;
    }

    /**
     *
     * @param Connection $connection
     * @return void
     */
    protected function invalidateLoginHash(Connection $connection): void
    {
        $connection->setLoginHash('');
        $this->connectionRepository->update($connection);
        $persistenceManager = $this->objectManager->get(PersistenceManager::class);
        $persistenceManager->persistAll();
    }

    /**
     * Map raw sql result data to Connection entity
     *
     * @param array $rawData
     * @return Connection|null
     */
    protected function mapConnection(array $rawData): ?Connection
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /* @var $dataMapper DataMapper */
        $dataMapper = $objectManager->get(DataMapper::class);

        /* Only possible to map multple */
        $mappedConnections = $dataMapper->map(Connection::class, [$rawData]);

        /* Mapping failed */
        if(empty($mappedConnections)) {
            return null;
        }

        /* Get the mapped entity from result array */
        $mappedConnection = $mappedConnections[0];

        return $mappedConnection;
    }

    /**
     * Get fe_users data by executing sql query with raw result
     *
     * @todo is it possible to "unmap" object.
     *
     * @param FrontendUser $feUser
     * @return array
     */
    protected function getRawFeUser(FrontendUser $feUser): array
    {
        /* @var $queryBuilder QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        /* */
        $feUserResult = $queryBuilder
            ->select('*')
            ->from('fe_users')->where(
                $queryBuilder->expr()->eq('uid', $feUser->getUid())
            )
            ->setMaxResults(1)
            ->execute();

        /* Get first and only result */
        $user = $feUserResult->fetch();

        /* May be null */
        if(empty($user)) {
            return [];
        }

        return $user;
    }
}
