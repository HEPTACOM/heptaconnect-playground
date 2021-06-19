<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReportingContextInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class BottleStatusReporter extends StatusReporterContract implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private BottleHealthService $service;

    public function __construct(BottleHealthService $service)
    {
        $this->service = $service;
    }

    public function supportsTopic(): string
    {
        return self::TOPIC_HEALTH;
    }

    protected function run(StatusReportingContextInterface $context): array
    {
        return [
            $this->supportsTopic() => true,
            'message' => $this->service->checkHealth(),
        ];
    }
}
