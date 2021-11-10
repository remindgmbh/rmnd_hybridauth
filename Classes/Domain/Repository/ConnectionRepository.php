<?php

declare(strict_types=1);

namespace Remind\RmndHybridauth\Domain\Repository;

use Remind\RmndHybridauth\Domain\Model\Connection;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Hybridauth connections
 */
class ConnectionRepository extends Repository
{
    /**
     *
     * @param string $provider
     * @param string $identifier
     * @param int $pid
     * @return Connection|null
     */
    public function findConnection(string $provider, string $identifier, int $pid = 0): ?Connection
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = [
            $query->equals('provider', $provider),
            $query->equals('identifier', $identifier),
        ];

        if ($pid > 0) {
            // @todo test
            $constraints[] = $query->equals('feUser.pid', $pid);
        }

        $connections = $query->matching($query->logicalAnd($constraints))->execute();

        return $connections->getFirst();
    }
}
