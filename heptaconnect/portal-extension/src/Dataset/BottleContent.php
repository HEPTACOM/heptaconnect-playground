<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\DatasetEntity;
use Heptacom\HeptaConnect\Playground\Dataset\Volume;

class BottleContent extends DatasetEntity
{
    protected Volume $content;

    public function __construct()
    {
        parent::__construct();
        $this->content = new Volume();
    }

    public function getContent(): Volume
    {
        return $this->content;
    }

    public function setContent(Volume $content): self
    {
        $this->content = $content;

        return $this;
    }
}
