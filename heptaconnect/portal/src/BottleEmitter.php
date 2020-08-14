<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface;
use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\Exception\UnexpectedPortalNodeException;

class BottleEmitter extends EmitterContract
{
    public function supports(): array
    {
        return [Bottle::class];
    }

    protected function run(
        PortalContract $portal,
        MappingInterface $mapping,
        EmitContextInterface $context
    ): ?DatasetEntityInterface {
        if (!$portal instanceof BottlePortal) {
            throw new UnexpectedPortalNodeException($portal);
        }

        $data = iterable_to_array($portal->getBottleStorage($context->getConfig($mapping) ?? [])->filter(fn (Bottle $b) => $b->getPrimaryKey() === $mapping->getExternalId()));

        if (\count($data) === 0) {
            return null;
        }

        /** @var Bottle $entity */
        $entity = clone current($data);

        $statKey = 'bottleStats.emit.' . $entity->getPrimaryKey();
        $context->getStorage($mapping)->set($statKey, ($context->getStorage($mapping)->get($statKey) ?? 0) + 1);

        return $entity;
    }
}
