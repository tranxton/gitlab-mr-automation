<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\Task\Infrastructure\Repositories;

use Tranxton\GitlabMrAutomation\Modules\Task\Domain\Entities\Task;
use GuzzleHttp\Client;

class TaskRepository
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $apiUrl;
    /**
     * @var string
     */
    private $taskNameUriTemplate;
    /**
     * @var string
     */
    private $taskTransitionUriTemplate;
    /**
     * @var SignatureGenerator
     */
    private $signatureGenerator;

    public function __construct(
        Client $client,
        SignatureGenerator $signatureGenerator,
        string $apiUrl,
        string $taskNameUriTemplate,
        string $taskTransitionUriTemplate
    ) {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
        $this->taskNameUriTemplate = $taskNameUriTemplate;
        $this->taskTransitionUriTemplate = $taskTransitionUriTemplate;
        $this->signatureGenerator = $signatureGenerator;
    }

    public function getSummary(string $taskId): string
    {
        $options = [
            'headers' => [
                'X-DIP-Signature' => $this->signatureGenerator->make($taskId),
            ],
        ];

        $response = $this->client->get($this->getTaskNameApiUrl($taskId), $options);

        return $response->getBody()->getContents();
    }

    public function setTransition(Task $task): bool
    {
        return $this->getTaskTransitionApiUrl($task->getId()) !== '';
    }

    private function getTaskNameApiUrl(string $taskId): string
    {
        return sprintf($this->taskNameUriTemplate, $this->apiUrl, $taskId);
    }

    private function getTaskTransitionApiUrl(string $taskId): string
    {
        return sprintf($this->taskTransitionUriTemplate, $this->apiUrl, $taskId);
    }
}
