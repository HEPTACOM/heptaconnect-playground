<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface;

class BottleEmitter extends EmitterContract
{
    private BottlePortal $portal;

    public function __construct(BottlePortal $portal)
    {
        $this->portal = $portal;
    }

    public function supports(): string
    {
        return Bottle::class;
    }

    protected function run(
        string $externalId,
        EmitContextInterface $context
    ): ?DatasetEntityContract {
        $data = iterable_to_array($this->portal->getBottleStorage($context->getConfig() ?? [])->filter(fn (Bottle $b) => $b->getPrimaryKey() === $externalId));

        if (\count($data) === 0) {
            return null;
        }

        /** @var Bottle $entity */
        $entity = clone current($data);

        $statKey = 'bottleStats.emit.' . ($entity->getPrimaryKey() ?? '');
        $context->getStorage()->set($statKey, ($context->getStorage()->get($statKey) ?? 0) + 1);

        return $entity;
    }
}
