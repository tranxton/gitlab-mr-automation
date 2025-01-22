<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Dto;

class MergeRequestUpdatePayload
{
    /**
     * @var string
     */
    private $projectId;
    /**
     * @var string
     */
    private $mrIid;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;

    public function __construct(
        string $projectId,
        string $mrIid,
        string $title,
        string $description
    ) {
        $this->projectId = $projectId;
        $this->mrIid = $mrIid;
        $this->title = $title;
        $this->description = $description;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getMrIid(): string
    {
        return $this->mrIid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
