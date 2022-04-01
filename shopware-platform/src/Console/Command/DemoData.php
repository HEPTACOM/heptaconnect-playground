<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\ShopwarePlatform\Console\Command;

use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\LocalShopwarePlatform\Portal as LocalShopwarePlatformPortal;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Create\PortalNodeCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Create\PortalNodeCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Find\PortalNodeAliasFindCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasFindActionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemoData extends Command
{
    private const DEMO_DATA_PORTAL_NODES = [
        BottlePortal::class => ['bottle'],
        LocalShopwarePlatformPortal::class => ['shopware'],
    ];

    protected static $defaultName = 'playground:demo-data';

    private PortalNodeCreateActionInterface $portalNodeCreateAction;

    private PortalNodeAliasFindActionInterface $portalNodeAliasFindAction;

    public function __construct(
        PortalNodeCreateActionInterface $portalNodeCreateAction,
        PortalNodeAliasFindActionInterface $portalNodeAliasFindAction
    ) {
        parent::__construct();
        $this->portalNodeCreateAction = $portalNodeCreateAction;
        $this->portalNodeAliasFindAction = $portalNodeAliasFindAction;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $creates = [];

        foreach (self::DEMO_DATA_PORTAL_NODES as $portalClass => $portalNodeAliases) {
            foreach ($portalNodeAliases as $portalNodeAlias) {
                $creates[$portalNodeAlias] = new PortalNodeCreatePayload($portalClass, $portalNodeAlias);
            }
        }

        $aliases = \array_merge(...\array_values(self::DEMO_DATA_PORTAL_NODES));

        foreach ($this->portalNodeAliasFindAction->find(new PortalNodeAliasFindCriteria($aliases)) as $foundAlias) {
            unset($creates[$foundAlias->getAlias()]);
        }

        if ($creates !== []) {
            $this->portalNodeCreateAction->create(new PortalNodeCreatePayloads(\array_values($creates)));
        }

        return 0;
    }
}
