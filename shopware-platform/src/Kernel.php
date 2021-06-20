<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\ShopwarePlatform;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\AbstractIntegration;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle;
use Sourceability\Instrumentation\Bundle\SourceabilityInstrumentationBundle;

class Kernel extends \Shopware\Core\Kernel
{
    public function registerBundles()
    {
        yield from parent::registerBundles();

        yield new Bundle();
        yield new SourceabilityInstrumentationBundle();
        yield new AbstractIntegration(
            true,
            \dirname((new \ReflectionClass(AbstractIntegration::class))->getFileName())
        );
    }

    protected function initializeDatabaseConnectionVariables(): void
    {
        if ($_SERVER['INSTALL'] ?? false) {
            return;
        }

        parent::initializeDatabaseConnectionVariables();
    }
}
