<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension;

use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;

class BottleContent extends PortalExtensionContract
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
