<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\ShopwarePlatform\Console\Command;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasEntity;
use Heptacom\HeptaConnect\Playground\Portal\BottlePortal;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\PortalNodeKeyCollection;
use Heptacom\HeptaConnect\Portal\LocalShopwarePlatform\Portal as LocalShopwarePlatformPortal;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Create\PortalNodeCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Create\PortalNodeCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Create\PortalNodeCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Get\PortalNodeGetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Get\PortalNodeGetCriteria;
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

    private PortalNodeCreateActionInterface $portalNodeCreateAction;

    private PortalNodeGetActionInterface $portalNodeGetAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        EntityRepositoryInterface $aliasRepository,
        PortalNodeCreateActionInterface $portalNodeCreateAction,
        PortalNodeGetActionInterface $portalNodeGetAction
    ) {
        parent::__construct();
        $this->aliasRepository = $aliasRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeCreateAction = $portalNodeCreateAction;
        $this->portalNodeGetAction = $portalNodeGetAction;
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
        $portalNodeKeys = new PortalNodeKeyCollection();
        $portalNodeKeyAlias = [];

        /** @var KeyAliasEntity $alias */
        foreach ($this->aliasRepository->search($criteria, $context) as $alias) {
            $storageKey = $this->storageKeyGenerator->deserialize($alias->getOriginal());

            if (!$storageKey instanceof PortalNodeKeyInterface) {
                continue;
            }

            $portalNodeKeys->push([$storageKey]);
            $portalNodeKeyAlias[$this->storageKeyGenerator->serialize($storageKey)] = $alias->getAlias();
        }

        foreach ($this->portalNodeGetAction->get(new PortalNodeGetCriteria($portalNodeKeys)) as $portalNode) {
            $serializedPortalNodeKey = $this->storageKeyGenerator->serialize($portalNode->getPortalNodeKey());
            $alias = $portalNodeKeyAlias[$serializedPortalNodeKey] ?? null;

            unset($portalNodeKeyAlias[$serializedPortalNodeKey]);

            if (\in_array($alias, self::DEMO_DATA['portal-nodes'][$portalNode->getPortalClass()] ?? [])) {
                unset($createPortalNodes[$alias]);
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
