<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\DatasetEntityCollection;

class BottleContentCollection extends DatasetEntityCollection
{
    protected function getT(): string
    {
        return BottleContent::class;
    }
}
