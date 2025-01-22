<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Dto;

use JMS\Serializer\Annotation as Serializer;

class MergeRequestDetails
{
    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $title;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $description;

    /**
     * @Serializer\Type("array")
     *
     * @var array<int, array{name: string}>
     */
    private $reviewers;

    /**
     * @Serializer\Type("array")
     *
     * @var array<int, array{name: string}>
     */
    private $assignees;

    /**
     * @param array<int, array{name: string}> $reviewers
     * @param array<int, array{name: string}> $assignees
     */
    public function __construct(string $title, string $description, array $reviewers, array $assignees)
    {
        $this->title = $title;
        $this->description = $description;
        $this->reviewers = $reviewers;
        $this->assignees = $assignees;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array<int, array{name: string}>
     */
    public function getReviewers(): array
    {
        return $this->reviewers;
    }

    /**
     * @return array<int, array{name: string}>
     */
    public function getAssignees(): array
    {
        return $this->assignees;
    }
}
