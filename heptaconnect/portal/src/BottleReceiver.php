<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiveContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;

class BottleReceiver extends ReceiverContract
{
    public function supports(): string
    {
        return Bottle::class;
    }

    protected function run(
        MappingInterface $mapping,
        DatasetEntityContract $entity,
        ReceiveContextInterface $context
    ): void {
        $mapping->setExternalId($mapping->getExternalId() ?? $entity->getPrimaryKey());
        $statKey = 'bottleStats.receive.' . ($mapping->getExternalId() ?? '');
        $context->getStorage($mapping)->set($statKey, ($context->getStorage($mapping)->get($statKey) ?? 0) + 1);
    }
}
