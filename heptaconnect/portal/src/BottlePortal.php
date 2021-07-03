<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BottlePortal extends PortalContract
{
    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined('black')
            ->setDefault('black', '#000000')
            ->setAllowedTypes('black', 'string')
            ->setDefined('white')
            ->setDefault('white', '#ffffff')
            ->setAllowedTypes('white', 'string');
    }
}
