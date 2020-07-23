<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\DatasetEntity;
use Heptacom\HeptaConnect\Dataset\Base\Translatable\TranslatableString;

class Label extends DatasetEntity
{
    protected TranslatableString $text;

    protected string $color = '#000000';

    public function __construct()
    {
        parent::__construct();
        $this->text = new TranslatableString();
    }

    public function getText(): TranslatableString
    {
        return $this->text;
    }

    public function setText(TranslatableString $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
