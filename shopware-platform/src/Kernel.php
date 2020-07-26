<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\ShopwarePlatform;

class Kernel extends \Shopware\Core\Kernel
{
    protected function initializeDatabaseConnectionVariables(): void
    {
        if ($_SERVER['INSTALL'] ?? false) {
            return;
        }

        parent::initializeDatabaseConnectionVariables();
    }
}
