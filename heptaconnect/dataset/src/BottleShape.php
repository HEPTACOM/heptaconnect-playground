<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\DatasetEntity;

class BottleShape extends DatasetEntity
{
    public const TYPE_ROUND = 'ROUND';

    public const TYPE_ANGULAR = 'ANGULAR';

    protected string $type = self::TYPE_ROUND;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
