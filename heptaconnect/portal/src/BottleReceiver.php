<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappedDatasetEntityStruct;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiveContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappedDatasetEntityCollection;

class BottleReceiver extends ReceiverContract
{
    public function receive(
        MappedDatasetEntityCollection $mappedDatasetEntities,
        ReceiveContextInterface $context,
        ReceiverStackInterface $stack
    ): iterable {
        /** @var MappedDatasetEntityStruct $mappedEntity */
        foreach ($mappedDatasetEntities as $mappedEntity) {
            $mapping = $mappedEntity->getMapping();
            $entity = $mappedEntity->getDatasetEntity();
            $portal = $context->getPortal($mapping);

            if (!$portal instanceof BottlePortal) {
                $context->markAsFailed($mapping, new \Exception('Invalid portal'));

                continue;
            }

            $id = $mapping->getExternalId() ?? $entity->getPrimaryKey();
            $mapping->setExternalId($id);
            $statKey = 'bottleStats.receive.' . ($mapping->getExternalId() ?? '');
            $context->getStorage($mapping)->set($statKey, ($context->getStorage($mapping)->get($statKey) ?? 0) + 1);

            yield $mapping;
        }

        yield from $stack->next($mappedDatasetEntities, $context);
    }

    public function supports(): array
    {
        return [Bottle::class];
    }
}
