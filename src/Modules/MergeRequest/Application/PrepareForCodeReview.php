<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Factories\MergeRequestFactory;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Dto\MergeRequestUpdatePayload;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\MergeRequestPort;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequestTemplate\MergeRequestDescriptionTemplatePort;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\TaskPort;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects\TaskId;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects\Transition;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto\CodeReviewPreparationRequest;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto\CodeReviewPreparationResponse;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Exceptions\PrepareForCodeReviewException;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\Enums\TemplateEnum;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\MergeRequest;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\DescriptionTemplate\DescriptionTemplate;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Title;

class PrepareForCodeReview
{
    /**
     * @var MergeRequestDescriptionTemplatePort
     */
    private $mergeRequestDescriptionTemplatePort;

    /**
     * @var MergeRequestFactory
     */
    private $mergeRequestFactory;

    /**
     * @var MergeRequestPort
     */
    private $mergeRequestPort;

    /**
     * @var TaskPort
     */
    private $taskPort;

    public function __construct(
        MergeRequestDescriptionTemplatePort $mergeRequestTemplatePort,
        MergeRequestFactory $mergeRequestFactory,
        MergeRequestPort $mergeRequestPort,
        TaskPort $taskPort
    ) {
        $this->mergeRequestDescriptionTemplatePort = $mergeRequestTemplatePort;
        $this->mergeRequestFactory = $mergeRequestFactory;
        $this->mergeRequestPort = $mergeRequestPort;
        $this->taskPort = $taskPort;
    }

    /**
     * @throws PrepareForCodeReviewException
     */
    public function run(CodeReviewPreparationRequest $request): CodeReviewPreparationResponse
    {
        $response = new CodeReviewPreparationResponse();

        $this->prepareMergeRequestForReview($request);
        $response->addMessage('The MR has been prepared for code review.');

        $this->taskPort->setTransition(new TaskId($request->getBranch()), (new Transition())->setReview());
        $response->addMessage('The task status has been successfully updated.');

        return $response;
    }

    /**
     * @throws PrepareForCodeReviewException
     */
    private function prepareMergeRequestForReview(CodeReviewPreparationRequest $request): void
    {
        $mergeRequestTemplate = $this->getMergeRequestDescriptionTemplate($request->getTemplateFile());
        $mergeRequest = $this->getMergeRequest($request);
        $taskTitle = $this->getTaskTitle(new TaskId($request->getBranch()));
        $mergeRequest->prepareForCodeReview($taskTitle, $mergeRequestTemplate);

        $this->mergeRequestPort->updateDetails(
            new MergeRequestUpdatePayload(
                $request->getProjectId(),
                $request->getMrIid(),
                $mergeRequest->getTitle()->getValue(),
                $mergeRequest->getDescription()->getValue()
            )
        );
    }

    /**
     * @throws PrepareForCodeReviewException
     */
    private function getMergeRequestDescriptionTemplate(string $path): DescriptionTemplate
    {
        $mrTemplateContent = $this->mergeRequestDescriptionTemplatePort->getContent($path);
        if ($mrTemplateContent === null) {
            throw new PrepareForCodeReviewException(sprintf('Could not read template file %s', $path));
        }

        return new DescriptionTemplate($mrTemplateContent);
    }

    /**
     * @throws PrepareForCodeReviewException
     */
    private function getMergeRequest(CodeReviewPreparationRequest $request): MergeRequest
    {
        $mrDetails = $this->mergeRequestPort->getDetails($request->getProjectId(), $request->getMrIid());

        return $this->mergeRequestFactory->make(
            $mrDetails,
            $request->getJiraTaskUrl(),
            $request->getGitlabUserProfileUrl()
        );
    }

    private function getTaskTitle(TaskId $taskId): Title
    {
        $taskSummary = $this->taskPort->getSummary($taskId);
        $title = sprintf(TemplateEnum::MERGE_REQUEST_TITLE, $taskId->getValue(), $taskSummary->getValue());

        return new Title($title);
    }
}
