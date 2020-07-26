<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappedDatasetEntityStruct;
use Heptacom\HeptaConnect\Portal\Base\MappingCollection;

class BottleEmitter implements EmitterInterface
{
    public function emit(
        MappingCollection $mappings,
        EmitContextInterface $context,
        EmitterStackInterface $stack
    ): iterable {
        /** @var MappingInterface $mapping */
        foreach ($mappings as $mapping) {
            $portalNode = $context->getPortalNode($mapping);

            if (!$portalNode instanceof BottlePortal) {
                continue;
            }

            $data = iterable_to_array($portalNode->getBottleStorage()->filter(fn (Bottle $b) => $b->getPrimaryKey() === $mapping->getExternalId()));

            if (\count($data) === 0) {
                continue;
            }

            yield new MappedDatasetEntityStruct($mapping, clone current($data));
        }

        yield from $stack->next($mappings, $context);
    }

    public function supports(): array
    {
        return [Bottle::class];
    }
}
