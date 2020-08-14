<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExploreContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\Exception\UnexpectedPortalNodeException;

class BottleExplorer extends ExplorerContract
{
    public function supports(): string
    {
        return Bottle::class;
    }

    protected function run(PortalContract $portal, ExploreContextInterface $context): iterable
    {
        if (!$portal instanceof BottlePortal) {
            throw new UnexpectedPortalNodeException($portal);
        }

        /** @var Bottle $bottle */
        foreach ($portal->getBottleStorage($context->getConfig() ?? []) as $bottle) {
            $statKey = 'bottleStats.explore.' . $bottle->getPrimaryKey();
            $context->getStorage()->set($statKey, ($context->getStorage()->get($statKey) ?? 0) + 1);

            yield $bottle;
        }
    }
}
