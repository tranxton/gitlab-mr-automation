<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Dto\MergeRequestDetails;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Dto\MergeRequestUpdatePayload;

interface MergeRequestPort
{
    public function updateDetails(MergeRequestUpdatePayload $mergeRequest): MergeRequestDetails;

    public function getDetails(string $projectId, string $mrIid): MergeRequestDetails;
}
