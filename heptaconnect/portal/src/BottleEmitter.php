<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface;

class BottleEmitter extends EmitterContract
{
    public function supports(): string
    {
        return Bottle::class;
    }

    protected function run(
        MappingInterface $mapping,
        EmitContextInterface $context
    ): ?DatasetEntityContract {
        $container = $context->getContainer($mapping);
        /** @var BottlePortal $portal */
        $portal = $container->get('portal');
        $data = iterable_to_array($portal->getBottleStorage($context->getConfig($mapping) ?? [])->filter(fn (Bottle $b) => $b->getPrimaryKey() === $mapping->getExternalId()));

        if (\count($data) === 0) {
            return null;
        }

        /** @var Bottle $entity */
        $entity = clone current($data);

        $statKey = 'bottleStats.emit.' . ($entity->getPrimaryKey() ?? '');
        $context->getStorage($mapping)->set($statKey, ($context->getStorage($mapping)->get($statKey) ?? 0) + 1);

        return $entity;
    }
}
