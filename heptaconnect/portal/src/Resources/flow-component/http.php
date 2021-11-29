<?php
declare(strict_types=1);

use Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

FlowComponent::httpHandler('hello-world')
    ->get(static fn (ResponseInterface $r, StreamFactoryInterface $sf): ResponseInterface =>
        $r->withStatus(200)->withBody($sf->createStream('hello world'))
    );
