<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension\Dataset;

use Heptacom\HeptaConnect\Dataset\Base\Contract\AttachableInterface;
use Heptacom\HeptaConnect\Playground\Dataset\Volume;

class BottleContent implements AttachableInterface
{
    protected Volume $content;

    protected function initialize(): void
    {
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
