<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Factories;


use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequest\Dto\MergeRequestDetails;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\MergeRequest;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Description\Description;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Title;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\User\User;

class MergeRequestFactory
{
    public function make(
        MergeRequestDetails $mrDetails,
        string $jiraTaskUrl,
        string $gitlabUserProfileUrl
    ): MergeRequest {
        return new MergeRequest(
            new Title($mrDetails->getTitle()),
            new Description($mrDetails->getDescription(), $jiraTaskUrl, $gitlabUserProfileUrl),
            $this->convertReviewersArrayToObjects($mrDetails->getReviewers()),
            $mrDetails->getAssignees()
        );
    }

    /**
     * @param  array<int, array{name: string, username: string}>  $reviewers
     *
     * @return array<int, User>
     */
    private function convertReviewersArrayToObjects(array $reviewers): array
    {
        $convertedReviewers = [];

        foreach ($reviewers as $reviewer) {
            $convertedReviewers [] = new User($reviewer['name'], $reviewer['username']);
        }

        return $convertedReviewers;
    }
}
