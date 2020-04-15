<?php
namespace Remind\RmndHybridauth\Traits;

use Remind\RmndHybridauth\Domain\Repository\ConnectionRepository;


/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
trait ConnectionRepositoryTrait
{
    /**
     *
     * @var ConnectionRepository
     */
    protected $connectionRepository = null;

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
