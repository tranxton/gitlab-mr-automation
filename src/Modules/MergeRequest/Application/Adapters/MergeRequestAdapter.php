<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Adapters;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Dto\MergeRequestDetails;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Dto\MergeRequestUpdatePayload;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Exceptions\MergeRequestRuntimeException;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\MergeRequestPort;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Infrastructure\Repositories\MergeRequestRepository;
use GuzzleHttp\Exception\RequestException;
use JMS\Serializer\Serializer;

class MergeRequestAdapter implements MergeRequestPort
{
    /**
     * @var MergeRequestRepository
     */
    private $repository;
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(MergeRequestRepository $repository, Serializer $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function updateDetails(MergeRequestUpdatePayload $mergeRequest): MergeRequestDetails
    {
        try {
            $details = [
                'title' => $mergeRequest->getTitle(),
                'description' => $mergeRequest->getDescription(),
            ];

            $response = $this->repository->updateDetails(
                $mergeRequest->getProjectId(),
                $mergeRequest->getMrIid(),
                $details
            );
        } catch (RequestException $e) {
            throw new MergeRequestRuntimeException(sprintf('Failed to update MR description: %s', $e->getMessage()), 0, $e);
        }

        /** @var MergeRequestDetails */
        return $this->serializer->deserialize($response, MergeRequestDetails::class, 'json');
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(string $projectId, string $mrIid): MergeRequestDetails
    {
        try {
            $response = $this->repository->getDetails($projectId, $mrIid);
        } catch (RequestException $e) {
            throw new MergeRequestRuntimeException(sprintf('Failed to fetch MR details: %s', $e->getMessage()), 0, $e);
        }

        /** @var MergeRequestDetails */
        return $this->serializer->deserialize($response, MergeRequestDetails::class, 'json');
    }
}
