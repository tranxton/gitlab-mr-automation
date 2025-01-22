<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Infrastructure\Commands\Adapters;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto\CodeReviewPreparationRequest;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto\CodeReviewPreparationResponse;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\PrepareForCodeReviewPort;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\PrepareForCodeReview;

class PrepareForCodeReviewAdapter implements PrepareForCodeReviewPort
{
    /**
     * @var PrepareForCodeReview
     */
    private $prepareForCodeReview;

    public function __construct(PrepareForCodeReview $prepareForCodeReview)
    {
        $this->prepareForCodeReview = $prepareForCodeReview;
    }

    public function update(CodeReviewPreparationRequest $request): CodeReviewPreparationResponse
    {
        return $this->prepareForCodeReview->run($request);
    }
}
