<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiveContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;

class BottleReceiver extends ReceiverContract
{
    public function supports(): string
    {
        return Bottle::class;
    }

    protected function run(
        DatasetEntityContract $entity,
        ReceiveContextInterface $context
    ): void {
        $entity->setPrimaryKey($entity->getPrimaryKey() ?? $this->generatePrimaryKey());
        $statKey = 'bottleStats.receive.' . $entity->getPrimaryKey();
        $context->getStorage()->set($statKey, ($context->getStorage()->get($statKey) ?? 0) + 1);
    }

    private function generatePrimaryKey(): string
    {
        return bin2hex(random_bytes(16));
    }
}
