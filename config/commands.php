<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->defaults()
        ->private()
        ->autowire()
        ->autoconfigure(false);

    $services
        ->load('Gitlab\\Commands\\Modules\\', __DIR__ . '/../src/Modules/*/Infrastructure/Commands/*Command.php')
        ->tag('gitlab.command')
        ->tag('console.command')
        ->public();
};
