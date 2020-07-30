<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappedDatasetEntityStruct;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappingCollection;

class BottleEmitter extends EmitterContract
{
    public function emit(
        MappingCollection $mappings,
        EmitContextInterface $context,
        EmitterStackInterface $stack
    ): iterable {
        /** @var MappingInterface $mapping */
        foreach ($mappings as $mapping) {
            $portal = $context->getPortal($mapping);

            if (!$portal instanceof BottlePortal) {
                continue;
            }

            $data = iterable_to_array($portal->getBottleStorage($context->getConfig($mapping) ?? [])->filter(fn (Bottle $b) => $b->getPrimaryKey() === $mapping->getExternalId()));

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
