<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\DatasetEntity;

class Volume extends DatasetEntity
{
    public const UNIT_LITER = 'LITER';

    public const UNIT_CUBIC_METER = 'CUBIC_METER';

    protected float $amount = 0.;

    protected string $unit = self::UNIT_LITER;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
