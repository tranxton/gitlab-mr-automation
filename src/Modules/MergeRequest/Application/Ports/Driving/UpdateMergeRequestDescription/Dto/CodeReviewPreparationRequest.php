<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Exceptions\PrepareForCodeReviewException;
use JMS\Serializer\Annotation as Serializer;

class CodeReviewPreparationRequest
{
    private const TASK_ID_PATTERN = '/[A-Z]{2,}-\d{1,9}/u';

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $branch;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $projectId;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $mrIid;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $templateFile;

    /**
     * @throws PrepareForCodeReviewException
     */
    public function __construct(
        string $branch,
        string $projectId,
        string $mrIid,
        string $templateFile
    ) {
        $this->branch = $branch;
        $this->projectId = $projectId;
        $this->mrIid = $mrIid;
        $this->templateFile = $templateFile;
    }

    /**
     * @throws PrepareForCodeReviewException
     */
    private function extractTaskId(string $branch): string
    {
        preg_match(self::TASK_ID_PATTERN, $branch, $matches);
        $taskId = $matches[0] ?? null;

        if ($taskId === null) {
            throw new PrepareForCodeReviewException(sprintf('Task ID not found in branch name: "%s". Expected format: XXX-XXXXXX.', $branch));
        }

        return $taskId;
    }

    /**
     * @throws PrepareForCodeReviewException
     */
    public function getBranch(): string
    {
        return $this->extractTaskId($this->branch);
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getMrIid(): string
    {
        return $this->mrIid;
    }

    public function getTemplateFile(): string
    {
        return $this->templateFile;
    }
}
