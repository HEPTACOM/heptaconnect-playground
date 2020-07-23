<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Portal\Base\Contract\PortalNodeInterface;
use Heptacom\HeptaConnect\Portal\Base\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\ExplorerCollection;
use Heptacom\HeptaConnect\Portal\Base\ReceiverCollection;

class BottlePortal implements PortalNodeInterface
{
    public function getExplorers(): ExplorerCollection
    {
        return new ExplorerCollection();
    }

    public function getEmitters(): EmitterCollection
    {
        return new EmitterCollection();
    }

    public function getReceivers(): ReceiverCollection
    {
        return new ReceiverCollection();
    }
}
