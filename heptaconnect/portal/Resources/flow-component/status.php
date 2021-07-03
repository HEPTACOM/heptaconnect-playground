<?php
declare(strict_types=1);

use Heptacom\HeptaConnect\Playground\Portal\BottleHealthService;
use Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;

FlowComponent::statusReporter(StatusReporterContract::TOPIC_HEALTH, static fn (BottleHealthService $service): array => [
    StatusReporterContract::TOPIC_HEALTH => true,
    'message' => $this->service->checkHealth(),
]);
