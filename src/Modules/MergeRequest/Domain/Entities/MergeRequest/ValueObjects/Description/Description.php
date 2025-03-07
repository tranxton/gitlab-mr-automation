<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Description;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\Exceptions\MergeRequestRuntimeException;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Description\Enums\PatternEnum as DescriptionPatternEnum;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Description\Enums\TemplateEnum;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\DescriptionTemplate\DescriptionTemplate;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\DescriptionTemplate\Enums\PatternEnum as DescriptionTemplatePatternEnum;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\User\User;

class Description
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string|null
     */
    private $reviewerChecklist;

    /**
     * @var string
     */
    private $shortDescription;

    /**
     * @var array<int, string>|null
     */
    private $existingReviewers;

    /**
     * @var string
     */
    private $jiraTaskUrl;

    /**
     * @var string
     */
    private $gitlabUserProfileUrl;

    public function __construct(string $value, string $jiraTaskUrl, string $gitlabUserProfileUrl)
    {
        $this->value = $value;
        $this->jiraTaskUrl = $jiraTaskUrl;
        $this->gitlabUserProfileUrl = $gitlabUserProfileUrl;
    }

    public function extractShortDescription(): self
    {
        $self = clone $this;

        if ($self->isEmpty()) {
            $self->shortDescription = '';

            return $self;
        }

        if ($self->hasNoTemplate()) {
            $shortDescription = $self->getValue();
        } else {
            preg_match(sprintf('/%s/u', DescriptionPatternEnum::SHORT_DESCRIPTION_SECTION), $self->getValue(), $matches);
            if (!isset($matches[2])) {
                throw new MergeRequestRuntimeException('Cannot extract short description.');
            }

            $shortDescription = $matches[2];
        }

        $self->shortDescription = trim($shortDescription);

        return $self;
    }

    public function isEmpty(): bool
    {
        return $this->getValue() === '';
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function hasNoTemplate(): bool
    {
        return !$this->hasTemplate();
    }

    public function hasTemplate(): bool
    {
        return preg_match(DescriptionTemplatePatternEnum::TEMPLATE_PATTERN, $this->getValue()) === 1;
    }

    public function extractReviewerChecklist(): self
    {
        $self = clone $this;

        preg_match(sprintf('/%s/su', DescriptionPatternEnum::REVIEWER_CHECKLIST_SECTION), $self->getValue(), $matches);
        if (!isset($matches[1])) {
            throw new MergeRequestRuntimeException('Cannot extract reviewer checklists.', 0);
        }

        $self->reviewerChecklist = trim($matches[1]);

        return $self;
    }

    public function setTemplate(DescriptionTemplate $template): self
    {
        $self = clone $this;

        $self->setValue($template->getValue());

        return $self;
    }

    private function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @deprecated use extractExistingReviewerWithLinks instead
     */
    public function extractExistingReviewers(): self
    {
        $self = clone $this;

        preg_match_all(
            sprintf('/%s/u', DescriptionPatternEnum::NAMED_REVIEWER_SECTION),
            $self->getValue(),
            $reviewersInformation,
            PREG_SET_ORDER
        );

        $existingReviewers = array_map(static function (array $reviewerInformation): string {
            if (!isset($reviewerInformation[1])) {
                throw new MergeRequestRuntimeException('Cannot find review name in named reviewer checklist.');
            }

            return trim($reviewerInformation[1]);
        }, $reviewersInformation);
        $self->existingReviewers = array_merge($self->existingReviewers ?? [], $existingReviewers);

        return $self;
    }
    public function extractExistingReviewerWithLinks(): self
    {
        $self = clone $this;

        preg_match_all(
            sprintf('/%s/u', DescriptionPatternEnum::NAMED_REVIEWER_WITH_LINK_SECTION),
            $self->getValue(),
            $reviewersInformation,
            PREG_SET_ORDER
        );

        $existingReviewers = array_map(static function (array $reviewerInformation): string {
            if (!isset($reviewerInformation[1])) {
                throw new MergeRequestRuntimeException('Cannot find review name in named reviewer checklist.');
            }

            return trim($reviewerInformation[1]);
        }, $reviewersInformation);
        $self->existingReviewers = array_merge($self->existingReviewers ?? [], $existingReviewers);

        return $self;
    }

    public function removeDefaultReviewersSection(): self
    {
        $self = clone $this;

        $withoutReviewersSection = preg_replace(sprintf('/%s/u', DescriptionPatternEnum::REVIEWERS_DEFAULT_SECTION), '',
            $self->getValue());
        $self->setValue(trim((string) $withoutReviewersSection));

        return $self;
    }

    /**
     * @param  array<int, User>  $reviewers
     */
    public function addNamedReviewersChecklists(array $reviewers): self
    {
        $self = clone $this;

        if (!is_string($self->reviewerChecklist)) {
            throw new MergeRequestRuntimeException('Cannot add named reviewer checklists because the reviewer checklist is empty.', 0);
        }

        if (!is_array($self->existingReviewers)) {
            throw new MergeRequestRuntimeException('Cannot add named reviewer checklists because the existing reviewer list was not extracted.', 0);
        }

        /**
         * @var array<int, User> $newReviewers
         */
        $newReviewers = array_filter($reviewers, static function (User $reviewer) use ($self){
            return !in_array($reviewer->getName(), $self->existingReviewers, true);
        });
        $reviewerIndex = count($this->existingReviewers ?? []);

        foreach ($newReviewers as $newReviewer) {
            $self = $self->addValue(sprintf(
                TemplateEnum::NAMED_REVIEWER_SUBHEADER,
                ++$reviewerIndex,
                $newReviewer->getName(),
                sprintf("%s/%s", $this->gitlabUserProfileUrl, $newReviewer->getUsername()),
                $self->reviewerChecklist
            ));
        }

        return $self;
    }

    private function addValue(string $value): self
    {
        $self = clone $this;

        $self->value .= $value;

        return $self;
    }

    public function removeCustomTaskLink(): self
    {
        $self = clone $this;

        $updatedShortDescription = preg_replace(
            sprintf('/%s%s/u', preg_quote($this->jiraTaskUrl, '/'), DescriptionPatternEnum::JIRA_TASK_ID),
            '',
            $this->getShortDescription()
        );

        if (!is_string($updatedShortDescription)) {
            throw new MergeRequestRuntimeException('Cannot remove custom task link from short description.');
        }

        $self->shortDescription = trim($updatedShortDescription);

        return $self;
    }

    private function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function attachShortDescription(): self
    {
        $self = clone $this;

        $self->value = (string) preg_replace(
            sprintf('/%s/u', DescriptionPatternEnum::SHORT_DESCRIPTION_SECTION),
            sprintf(TemplateEnum::SHORT_DESCRIPTION_CONTENT, $self->shortDescription),
            $self->getValue()
        );

        return $self;
    }
}
