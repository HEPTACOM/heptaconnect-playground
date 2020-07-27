<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiveContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappedDatasetEntityCollection;
use Ramsey\Uuid\Uuid;

class BottleReceiver implements ReceiverInterface
{
    public function receive(
        MappedDatasetEntityCollection $mappedDatasetEntities,
        ReceiveContextInterface $context,
        ReceiverStackInterface $stack
    ): iterable {
        foreach ($mappedDatasetEntities as $mappedEntity) {
            $mapping = $mappedEntity->getMapping();
            $portal = $context->getPortal($mapping);

            if (!$portal instanceof BottlePortal) {
                $context->markAsFailed($mapping, new \Exception('Invalid portal'));

                continue;
            }

            $id = $mapping->getExternalId() ?? Uuid::uuid4()->getHex();
            $mapping->setExternalId($id);

            yield $mapping;
        }

        yield from $stack->next($mappedDatasetEntities, $context);
    }

    public function supports(): array
    {
        return [Bottle::class];
    }
}
