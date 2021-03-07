<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;

class Cap extends DatasetEntityContract
{
    public const TYPE_CROWN_CORK = 'CROWN_CORK';

    public const TYPE_FLIP_TOP = 'FLIP_TOP';

    public const TYPE_SCREW = 'SCREW';

    protected string $type = self::TYPE_CROWN_CORK;

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
