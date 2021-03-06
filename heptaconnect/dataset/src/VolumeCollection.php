<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\DatasetEntityCollection;

class VolumeCollection extends DatasetEntityCollection
{
    protected function getT(): string
    {
        return Volume::class;
    }
}
