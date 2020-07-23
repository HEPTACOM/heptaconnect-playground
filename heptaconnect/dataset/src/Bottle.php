<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\DatasetEntity;

class Bottle extends DatasetEntity
{
    protected Volume $capacity;

    protected LabelCollection $labels;

    protected Cap $cap;

    protected BottleShape $shape;

    public function __construct()
    {
        parent::__construct();
        $this->capacity = new Volume();
        $this->labels = new LabelCollection();
        $this->cap = new Cap();
        $this->shape = new BottleShape();
    }

    public function getCapacity(): Volume
    {
        return $this->capacity;
    }

    public function setCapacity(Volume $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getLabels(): LabelCollection
    {
        return $this->labels;
    }

    public function setLabels(LabelCollection $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    public function getCap(): Cap
    {
        return $this->cap;
    }

    public function setCap(Cap $cap): self
    {
        $this->cap = $cap;

        return $this;
    }

    public function getShape(): BottleShape
    {
        return $this->shape;
    }

    public function setShape(BottleShape $shape): self
    {
        $this->shape = $shape;

        return $this;
    }
}
