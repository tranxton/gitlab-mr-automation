<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects\TaskId;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects\TaskSummary;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects\Transition;

interface TaskPort
{
    public function getSummary(TaskId $taskId): TaskSummary;

    public function setTransition(TaskId $taskId, Transition $transition): bool;
}
