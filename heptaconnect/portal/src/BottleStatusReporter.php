<?php


namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReportingContextInterface;
use Psr\Log\LoggerInterface;

class BottleStatusReporter extends StatusReporterContract
{

    private BottleHealthService $service;
    private BottlePortal $portal;
    private LoggerInterface $logger;

    public function __construct(BottlePortal $portal, BottleHealthService $service, LoggerInterface $logger)
    {
        $this->service = $service;
        $this->portal = $portal;
        $this->logger = $logger;
    }

    public function supportsTopic(): string
    {
        return self::TOPIC_HEALTH;
    }

    protected function run(StatusReportingContextInterface $context): array
    {
        $result = [$this->supportsTopic() => $this->service->checkHealth()];
        return $result;
    }

}
