<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\ShopwarePlatform\Console\Command;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasEntity;
use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\LocalShopwarePlatform\Portal as LocalShopwarePlatformPortal;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Create\PortalNodeCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Create\PortalNodeCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Create\PortalNodeCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemoData extends Command
{
    private const DEMO_DATA = [
        'portal-nodes' => [
            BottlePortal::class => ['bottle'],
            LocalShopwarePlatformPortal::class => ['shopware'],
        ],
    ];

    protected static $defaultName = 'playground:demo-data';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private EntityRepositoryInterface $aliasRepository;

    private PortalNodeRepositoryContract $portalNodeRepository;

    private PortalNodeCreateActionInterface $portalNodeCreateAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        EntityRepositoryInterface $aliasRepository,
        PortalNodeRepositoryContract $portalNodeRepository,
        PortalNodeCreateActionInterface $portalNodeCreateAction
    ) {
        parent::__construct();
        $this->aliasRepository = $aliasRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeRepository = $portalNodeRepository;
        $this->portalNodeCreateAction = $portalNodeCreateAction;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $createPortalNodes = [];
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $filters = [];

        foreach (self::DEMO_DATA['portal-nodes'] as $portalClass => $portalNodeAliases) {
            $filters[] = new EqualsAnyFilter('alias', $portalNodeAliases);

            foreach ($portalNodeAliases as $portalNodeAlias) {
                $createPortalNodes[$portalNodeAlias] = $portalClass;
            }
        }

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, $filters));

        /** @var KeyAliasEntity $alias */
        foreach ($this->aliasRepository->search($criteria, $context) as $alias) {
            $storageKey = $this->storageKeyGenerator->deserialize($alias->getOriginal());

            if (!$storageKey instanceof PortalNodeKeyInterface) {
                continue;
            }

            $portalClass = $this->portalNodeRepository->read($storageKey);

            if (\in_array($alias->getAlias(), self::DEMO_DATA['portal-nodes'][$portalClass] ?? [])) {
                unset($createPortalNodes[$alias->getAlias()]);
            }
        }

        $portalNodeCreatePayloads = new PortalNodeCreatePayloads();
        $aliases = [];

        foreach ($createPortalNodes as $alias => $portalClass) {
            $portalNodeCreatePayloads->push([new PortalNodeCreatePayload($portalClass)]);
            $aliases[] = $alias;
        }

        $portalNodeCreateResult = $this->portalNodeCreateAction->create($portalNodeCreatePayloads);

        $aliasInserts = [];

        foreach ($portalNodeCreateResult as $result) {
            $aliasInserts[] = [
                'id' => Uuid::randomHex(),
                'alias' => \array_shift($aliases),
                'original' => $this->storageKeyGenerator->serialize($result),
            ];
        }

        if ($aliasInserts !== []) {
            $this->aliasRepository->create($aliasInserts, $context);
        }

        return 0;
    }
}
