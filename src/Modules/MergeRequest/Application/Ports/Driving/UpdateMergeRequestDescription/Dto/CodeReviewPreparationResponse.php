<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto;

class CodeReviewPreparationResponse
{
    /**
     * @var array<int, string>
     */
    private $messages;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @param  array<int, string>  $messages
     */
    public function __construct(array $messages = [], int $statusCode = 0)
    {
        $this->messages = $messages;
        $this->statusCode = $statusCode;
    }

    public function addMessage(string $message): self
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
