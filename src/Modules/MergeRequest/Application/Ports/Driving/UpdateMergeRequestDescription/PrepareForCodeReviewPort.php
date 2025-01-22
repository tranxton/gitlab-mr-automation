<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto\CodeReviewPreparationRequest;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto\CodeReviewPreparationResponse;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Exceptions\PrepareForCodeReviewException;

interface PrepareForCodeReviewPort
{
    /**
     * @throws PrepareForCodeReviewException
     */
    public function update(CodeReviewPreparationRequest $request): CodeReviewPreparationResponse;
}
