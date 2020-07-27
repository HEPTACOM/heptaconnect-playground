<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerInterface;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExploreContextInterface;

class BottleExplorer implements ExplorerInterface
{
    public function explore(ExploreContextInterface $context, ExplorerStackInterface $stack): iterable
    {
        $portal = $context->getPortal();

        if (!$portal instanceof BottlePortal) {
            return $stack->next($context);
        }

        /** @var Bottle $bottle */
        foreach ($portal->getBottleStorage() as $bottle) {
            yield $bottle;
        }

        return $stack->next($context);
    }

    public function supports(): string
    {
        return Bottle::class;
    }
}
