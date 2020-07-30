<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\PortalExtension;

use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BottleContent extends PortalExtensionContract
{
    public function getEmitterDecorators(): EmitterCollection
    {
        return new EmitterCollection([
            new HalfFullHalfEmptyBottleEmitter(),
        ]);
    }

    public function extendConfiguration(OptionsResolver $template): OptionsResolver
    {
        return parent::extendConfiguration($template)
            ->setDefined('contentFactor')
            ->setDefault('contentFactor', 0.5)
            ->setAllowedTypes('contentFactor', 'float');
    }

    public function supports(): string
    {
        return BottlePortal::class;
    }
}
