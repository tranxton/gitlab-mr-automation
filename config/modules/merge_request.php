<?php

declare(strict_types=1);

use Gitlab\Commands\Modules\MergeRequest\Application\Adapters\MergeRequestAdapter;
use Gitlab\Commands\Modules\MergeRequest\Application\Adapters\MergeRequestDescriptionTemplateAdapter;
use Gitlab\Commands\Modules\MergeRequest\Application\Adapters\TaskAdapter;
use Gitlab\Commands\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\MergeRequestPort;
use Gitlab\Commands\Modules\MergeRequest\Application\Ports\Driven\MergeRequestTemplate\MergeRequestDescriptionTemplatePort;
use Gitlab\Commands\Modules\MergeRequest\Application\Ports\Driven\Task\TaskPort;
use Gitlab\Commands\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\PrepareForCodeReviewPort;
use Gitlab\Commands\Modules\MergeRequest\Infrastructure\Commands\Adapters\PrepareForCodeReviewAdapter;
use Gitlab\Commands\Modules\MergeRequest\Infrastructure\Repositories\MergeRequestRepository;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $parameters = $configurator->parameters();

    $parameters->set('gitlab.api_url.merge_request.template', '%%s/projects/%%s/merge_requests/%%s');

    $services
        ->defaults()
        ->private()
        ->autowire()
        ->autoconfigure(false);

    $services
        ->set(PrepareForCodeReviewPort::class)
        ->class(PrepareForCodeReviewAdapter::class);

    $services
        ->set(MergeRequestDescriptionTemplatePort::class)
        ->class(MergeRequestDescriptionTemplateAdapter::class);

    $services
        ->set(MergeRequestPort::class)
        ->class(MergeRequestAdapter::class);

    $services
        ->set(TaskPort::class)
        ->class(TaskAdapter::class);

    $services
        ->set(MergeRequestRepository::class)
        ->arg('$apiUrl', getenv('GITLAB_API_URL'))
        ->arg('$apiUrlTemplate', '%gitlab.api_url.merge_request.template%')
        ->arg('$client', (new Definition(GuzzleHttp\Client::class))->setArgument('$config', [
            'headers' => [
                'PRIVATE-TOKEN' => getenv('GITLAB_PERSONAL_ACCESS_TOKEN'),
                'Accept' => 'application/json',
            ],
        ]));
};
