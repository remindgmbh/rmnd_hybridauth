<?php
namespace Remind\RmndHybridauth\Domain\Repository;

use Remind\RmndHybridauth\Domain\Model\Connection;
use TYPO3\CMS\Extbase\Persistence\Repository;

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

        if($pid > 0) {
            // @todo test
            $constraints[] = $query->equals('feUser.pid', $pid);
        }

        $connections = $query->matching($query->logicalAnd($constraints))->execute();

        return $connections->getFirst();
    }
}
