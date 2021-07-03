<?php
declare(strict_types=1);

use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Playground\Portal\BottleApiClient;
use Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface;

FlowComponent::explorer(Bottle::class)
    ->run(static function (BottleApiClient $client, PortalStorageInterface $storage): iterable {
        /** @var Bottle $bottle */
        foreach ($client->getBottles() as $bottle) {
            $statKey = 'bottleStats.explore.' . ($bottle->getPrimaryKey() ?? '');
            $storage->set($statKey, ($storage->get($statKey) ?? 0) + 1);

            yield $bottle;
        }
    });
FlowComponent::emitter(Bottle::class)
    ->run(static function (string $id, BottleApiClient $client, PortalStorageInterface $storage): ?Bottle {
        $data = iterable_to_array($client->getBottles()->filter(static fn (Bottle $b) => $b->getPrimaryKey() === $id));

        if (\count($data) === 0) {
            return null;
        }

        /** @var Bottle $entity */
        $entity = clone current($data);

        $statKey = 'bottleStats.emit.' . ($entity->getPrimaryKey() ?? '');
        $storage->set($statKey, ($storage->get($statKey) ?? 0) + 1);

        return $entity;
    });
FlowComponent::receiver(Bottle::class)
    ->run(static function (Bottle $bottle, PortalStorageInterface $storage): void {
        $bottle->setPrimaryKey($bottle->getPrimaryKey() ?? bin2hex(random_bytes(16)));
        $statKey = 'bottleStats.receive.' . $bottle->getPrimaryKey();
        $storage->set($statKey, ($storage->get($statKey) ?? 0) + 1);
    });
