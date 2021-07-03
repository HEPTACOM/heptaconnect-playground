<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Playground\Dataset\Volume;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface;

class HalfFullHalfEmptyBottleEmitter extends EmitterContract
{
    private float $configContentFactor;

    public function __construct(float $configContentFactor)
    {
        $this->configContentFactor = $configContentFactor;
    }

    /**
     * @param Bottle $entity
     */
    protected function extend(DatasetEntityContract $entity, EmitContextInterface $context): DatasetEntityContract
    {
        $content = new Dataset\BottleContent();
        $content->setContent(
            (new Volume())
                ->setUnit(Volume::UNIT_LITER)
                ->setAmount($entity->getCapacity()->getAmount() * $this->configContentFactor)
        );
        $entity->attach($content);

        return $entity;
    }

    public function supports(): string
    {
        return Bottle::class;
    }
}
