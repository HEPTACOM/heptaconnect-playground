<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension;

use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\Base\Contract\PortalNodeExtensionInterface;
use Heptacom\HeptaConnect\Portal\Base\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\Support\AbstractPortalNodeExtension;

class BottleContent extends AbstractPortalNodeExtension implements PortalNodeExtensionInterface
{
    public function getEmitterDecorators(): EmitterCollection
    {
        return new EmitterCollection([
            new HalfFullHalfEmptyBottleEmitter(),
        ]);
    }

    public function supports(): string
    {
        return BottlePortal::class;
    }
}
