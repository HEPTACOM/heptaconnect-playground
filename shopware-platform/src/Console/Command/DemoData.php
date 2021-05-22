<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\ShopwarePlatform\Console\Command;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasEntity;
use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\LocalShopwarePlatform\Portal as LocalShopwarePlatformPortal;
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

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        EntityRepositoryInterface $aliasRepository,
        PortalNodeRepositoryContract $portalNodeRepository
    ) {
        parent::__construct();
        $this->aliasRepository = $aliasRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeRepository = $portalNodeRepository;
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

        foreach ($createPortalNodes as $alias => $portalClass) {
            $portalNodeKey = $this->portalNodeRepository->create($portalClass);

            $this->aliasRepository->create([[
                'id' => Uuid::randomHex(),
                'alias' => $alias,
                'original' => $this->storageKeyGenerator->serialize($portalNodeKey),
            ]], $context);
        }

        return 0;
    }
}
