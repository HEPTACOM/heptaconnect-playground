<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExploreContextInterface;

class BottleExplorer extends ExplorerContract
{
    public function supports(): string
    {
        return Bottle::class;
    }

    protected function run(ExploreContextInterface $context): iterable
    {
        /** @var BottlePortal $portal */
        $portal = $context->getContainer()->get('portal');

        /** @var Bottle $bottle */
        foreach ($portal->getBottleStorage($context->getConfig() ?? []) as $bottle) {
            $statKey = 'bottleStats.explore.' . ($bottle->getPrimaryKey() ?? '');
            $context->getStorage()->set($statKey, ($context->getStorage()->get($statKey) ?? 0) + 1);

            yield $bottle;
        }
    }
}
