<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\Exceptions\MergeRequestException;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Description\Description;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\DescriptionTemplate\DescriptionTemplate;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Title;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\User\User;

class MergeRequest
{
    /**
     * @var Title
     */
    private $title;

    /**
     * @var Description
     */
    private $description;

    /**
     * @var array<int, User>
     */
    private $reviewers;

    /**
     * @var array<int, array{name: string}>
     */
    private $assignees;

    /**
     * @param  array<int, User>  $reviewers
     * @param  array<int, array{name: string}>  $assignees
     */
    public function __construct(Title $title, Description $description, array $reviewers, array $assignees)
    {
        $this->description = $description;
        $this->reviewers = $reviewers;
        $this->assignees = $assignees;
        $this->title = $title;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function setTitle(Title $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    /**
     * @throws MergeRequestException
     */
    public function prepareForCodeReview(Title $title, DescriptionTemplate $template): self
    {
        if ($this->title->isDraft()) {
            throw new MergeRequestException('The merge request is currently in draft state. Please mark it as ready before proceeding.');
        }

        if ($this->hasNoAssignees()) {
            throw new MergeRequestException('At least one assignee must be selected.');
        }

        if ($this->hasNoReviewers()) {
            throw new MergeRequestException('At least one reviewer must be selected.');
        }

        if ($this->description->isEmpty()) {
            $this->description = $this->description->setTemplate($template);
        }

        $this->description = $this->description->extractShortDescription();

        if ($this->description->hasNoTemplate()) {
            $this->description = $this->description->setTemplate($template);
        }

        $this->description = $this->description
            ->removeCustomTaskLink()
            ->attachShortDescription()
            ->extractReviewerChecklist()
            ->extractExistingReviewers()
            ->extractExistingReviewerWithLinks()
            ->removeDefaultReviewersSection()
            ->addNamedReviewersChecklists($this->getReviewers());

        $this->setTitle($title);

        return $this;
    }

    public function hasNoAssignees(): bool
    {
        return $this->getAssignees() === [];
    }

    /**
     * @return array<int, array{name: string}>
     */
    public function getAssignees(): array
    {
        return $this->assignees;
    }

    public function hasNoReviewers(): bool
    {
        return $this->getReviewers() === [];
    }

    /**
     * @return array<int, User>
     */
    public function getReviewers(): array
    {
        return $this->reviewers;
    }
}
