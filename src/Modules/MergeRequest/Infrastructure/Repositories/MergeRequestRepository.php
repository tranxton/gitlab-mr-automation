<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Infrastructure\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MergeRequestRepository
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $apiUrlTemplate;
    /**
     * @var string
     */
    private $apiUrl;

    public function __construct(Client $client, string $apiUrl, string $apiUrlTemplate)
    {
        $this->client = $client;
        $this->apiUrlTemplate = $apiUrlTemplate;
        $this->apiUrl = $apiUrl;
    }

    /**
     * @throws RequestException
     */
    public function getDetails(string $projectId, string $mrIid): string
    {
        $response = $this->client->get($this->getApiUrl($projectId, $mrIid));

        return $response->getBody()->getContents();
    }

    private function getApiUrl(string $projectId, string $mrIid): string
    {
        return sprintf($this->apiUrlTemplate, $this->apiUrl, $projectId, $mrIid);
    }

    /**
     * @param  array<string, string>  $details
     *
     * @throws RequestException
     */
    public function updateDetails(string $projectId, string $mrIid, array $details): string
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => $details,
        ];

        $response = $this->client->put($this->getApiUrl($projectId, $mrIid), $options);

        return $response->getBody()->getContents();
    }
}
