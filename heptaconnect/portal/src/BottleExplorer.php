<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Contract\ExplorerInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\ExplorerStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExploreContextInterface;

class BottleExplorer implements ExplorerInterface
{
    public function explore(ExploreContextInterface $context, ExplorerStackInterface $stack): iterable
    {
        $portalNode = $context->getPortalNode();

        if (!$portalNode instanceof BottlePortal) {
            return $stack->next($context);
        }

        /** @var Bottle $bottle */
        foreach ($portalNode->getBottleStorage() as $bottle) {
            yield $bottle;
        }

        return $stack->next($context);
    }

    public function supports(): string
    {
        return Bottle::class;
    }
}
