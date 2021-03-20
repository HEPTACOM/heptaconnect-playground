<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Playground\Dataset\Volume;
use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappedDatasetEntityStruct;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappingCollection;

class HalfFullHalfEmptyBottleEmitter extends EmitterContract
{
    public function emit(
        MappingCollection $mappings,
        EmitContextInterface $context,
        EmitterStackInterface $stack
    ): iterable {
        /** @var MappedDatasetEntityStruct $mappedEntity */
        foreach ($stack->next($mappings, $context) as $key => $mappedEntity) {
            $portal = $context->getPortal($mappedEntity->getMapping());

            if (!$portal instanceof BottlePortal) {
                yield $key => $mappedEntity;
                continue;
            }

            $bottle = $mappedEntity->getDatasetEntity();

            if (!$bottle instanceof Bottle) {
                yield $key => $mappedEntity;
                continue;
            }

            $config = $context->getConfig($mappedEntity->getMapping()) ?? [];
            $config['contentFactor'] ??= 0.5;
            $content = new Dataset\BottleContent();
            $content->setContent(
                (new Volume())
                    ->setUnit(Volume::UNIT_LITER)
                    ->setAmount($bottle->getCapacity()->getAmount() * $config['contentFactor'])
            );
            $bottle->attach($content);

            yield $key => $mappedEntity;
        }
    }

    public function supports(): string
    {
        return Bottle::class;
    }
}
