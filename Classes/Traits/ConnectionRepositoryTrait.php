<?php

declare(strict_types=1);

namespace Remind\RmndHybridauth\Traits;

use Remind\RmndHybridauth\Domain\Repository\ConnectionRepository;

/**
 * ConnectionRepositoryTrait
 *
 * @todo naming is inconsistent; should be ConnectionRepositoryInjectionTrait
 */
trait ConnectionRepositoryTrait
{
    /**
     *
     * @var ConnectionRepository
     */
    protected ?ConnectionRepository $connectionRepository = null;

    /**
     * Symfony auto injection
     *
     * @param ConnectionRepository $connectionRepository
     * @return void
     */
    public function injectConnectionRepository(ConnectionRepository $connectionRepository): void
    {
        $this->connectionRepository = $connectionRepository;
    }
}
