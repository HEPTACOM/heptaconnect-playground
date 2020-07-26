<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension;

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Playground\Dataset\Volume;
use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\Base\Contract\EmitterStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface;
use Heptacom\HeptaConnect\Portal\Base\MappedDatasetEntityStruct;
use Heptacom\HeptaConnect\Portal\Base\MappingCollection;

class HalfFullHalfEmptyBottleEmitter implements EmitterInterface
{
    public function emit(
        MappingCollection $mappings,
        EmitContextInterface $context,
        EmitterStackInterface $stack
    ): iterable {
        /** @var MappedDatasetEntityStruct $mappedEntity */
        foreach ($stack->next($mappings, $context) as $key => $mappedEntity) {
            $portalNode = $context->getPortalNode($mappedEntity->getMapping());

            if (!$portalNode instanceof BottlePortal) {
                yield $key => $mappedEntity;
                continue;
            }

            $bottle = $mappedEntity->getDatasetEntity();

            if (!$bottle instanceof Bottle) {
                yield $key => $mappedEntity;
                continue;
            }

            $content = new Dataset\BottleContent();
            $content->setContent(
                (new Volume())
                    ->setUnit(Volume::UNIT_LITER)
                    ->setAmount($bottle->getCapacity()->getAmount() * 0.5)
            );
            $bottle->attach($content);

            yield $key => $mappedEntity;
        }
    }

    public function supports(): array
    {
        return [
            Bottle::class,
        ];
    }
}
