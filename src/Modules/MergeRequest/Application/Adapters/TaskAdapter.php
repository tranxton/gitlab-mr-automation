<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Adapters;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\Exceptions\TaskRuntimeException;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\TaskPort;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects\TaskId;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects\TaskSummary;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects\Transition;
use Tranxton\GitlabMrAutomation\Modules\Task\Domain\Entities\Task;
use Tranxton\GitlabMrAutomation\Modules\Task\Infrastructure\Repositories\TaskRepository;

class TaskAdapter implements TaskPort
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getSummary(TaskId $taskId): TaskSummary
    {
        /** @var array{fields?: array{summary?: string|mixed}} $response */
        $response = json_decode($this->taskRepository->getSummary($taskId->getValue()), true);

        if (!isset($response['fields']['summary']) || !is_string($response['fields']['summary'])) {
            throw new TaskRuntimeException('DIP returned non-valid response. Can\'t extract task summary.');
        }

        return new TaskSummary($response['fields']['summary']);
    }

    public function setTransition(TaskId $taskId, Transition $transition): bool
    {
        return $this->taskRepository->setTransition(new Task($taskId->getValue(), $transition->getValue()));
    }
}
