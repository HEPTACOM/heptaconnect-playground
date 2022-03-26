<?php

declare(strict_types=1);

use Heptacom\HeptaConnect\Core\Bridge\PortalNode\Configuration\Config;

Config::replace('bottle', Config::helper()->env([
    'black' => 'PORTAL_BOTTLE_BLACK',
]));
