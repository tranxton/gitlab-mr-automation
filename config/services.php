<?php

declare(strict_types=1);

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->defaults()
        ->public()
        ->autowire()
        ->autoconfigure(false);

    $services
        ->set(SerializerBuilder::class)
        ->factory([SerializerBuilder::class, 'create'])
        ->private();

    $services
        ->set(Serializer::class)
        ->factory([new Reference(SerializerBuilder::class), 'build']);

    $configurator->import(__DIR__ . '/modules/merge_request.php');
    $configurator->import(__DIR__ . '/modules/task.php');
};
