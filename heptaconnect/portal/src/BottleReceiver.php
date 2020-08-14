<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface;
use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\Exception\UnexpectedPortalNodeException;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiveContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;

class BottleReceiver extends ReceiverContract
{
    public function supports(): array
    {
        return [Bottle::class];
    }

    protected function run(
        PortalContract $portal,
        MappingInterface $mapping,
        DatasetEntityInterface $entity,
        ReceiveContextInterface $context
    ): void {
        if (!$portal instanceof BottlePortal) {
            throw new UnexpectedPortalNodeException($portal);
        }

        $mapping->setExternalId($mapping->getExternalId() ?? $entity->getPrimaryKey());
        $statKey = 'bottleStats.receive.' . ($mapping->getExternalId() ?? '');
        $context->getStorage($mapping)->set($statKey, ($context->getStorage($mapping)->get($statKey) ?? 0) + 1);
    }
}
