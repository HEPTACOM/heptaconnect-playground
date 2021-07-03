<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExploreContextInterface;

class BottleExplorer extends ExplorerContract
{
    private BottleApiClient $bottleApiClient;

    public function __construct(BottleApiClient $bottleApiClient)
    {
        $this->bottleApiClient = $bottleApiClient;
    }

    public function supports(): string
    {
        return Bottle::class;
    }

    protected function run(ExploreContextInterface $context): iterable
    {
        /** @var Bottle $bottle */
        foreach ($this->bottleApiClient->getBottles() as $bottle) {
            $statKey = 'bottleStats.explore.' . ($bottle->getPrimaryKey() ?? '');
            $context->getStorage()->set($statKey, ($context->getStorage()->get($statKey) ?? 0) + 1);

            yield $bottle;
        }
    }
}
