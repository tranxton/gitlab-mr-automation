<?php

declare(strict_types=1);

use Gitlab\Commands\Modules\Task\Infrastructure\Repositories\SignatureGenerator;
use Gitlab\Commands\Modules\Task\Infrastructure\Repositories\TaskRepository;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $parameters = $configurator->parameters();

    $parameters->set('jira.api_url.task_name.template', '%%s/populate/jira/issue/%%s');
    $parameters->set('jira.api_url.task_transition.template', '%%s/action/jira/issue/%%s/transition');

    $services
        ->defaults()
        ->private()
        ->autowire()
        ->autoconfigure(false);

    $services
        ->set(TaskRepository::class)
        ->arg('$apiUrl', getenv('JIRA_API_URL'))
        ->arg('$taskNameUriTemplate', '%jira.api_url.task_name.template%')
        ->arg('$taskTransitionUriTemplate', '%jira.api_url.task_transition.template%')
        ->arg(
            '$signatureGenerator',
            (new Definition(SignatureGenerator::class))->setArgument('$token', getenv('JIRA_API_TOKEN'))
        )
        ->arg('$client', (new Definition(GuzzleHttp\Client::class))->setArgument('$config', [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]));
};
