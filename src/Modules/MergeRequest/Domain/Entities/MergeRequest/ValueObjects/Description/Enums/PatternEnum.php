<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Description\Enums;

class PatternEnum
{
    /**
     * @var string
     */
    public const SHORT_DESCRIPTION_SECTION = '/(## Краткое описание проделанной работы)([\s\S]*?)(## Автор)/u';

    /**
     * @var string
     */
    public const TASK_LINK = '/https:\/\/jira\.dats\.tech\/browse\/[A-Z]{2,}-\d{1,9}/u';

    /**
     * @var string
     */
    public const REVIEWERS_DEFAULT_SECTION = '/## Ревьювер\s\d+(?!:)([\s\S]*?)(?=\n## Ревьювер|$)/u';

    /**
     * @var string
     */
    public const REVIEWER_CHECKLIST_SECTION = '/## Ревьювер.*?(###.*?)(?=\n## Ревьювер|$)/su';

    /**
     * @var string
     */
    public const NAMED_REVIEWER_SECTION = '/## Ревьювер\s\d+:\s([А-Яа-яЁёA-Za-z]+\s[А-Яа-яЁёA-Za-z]+)[\s\S]*?(?=## Ревьювер|$)/u';
}
