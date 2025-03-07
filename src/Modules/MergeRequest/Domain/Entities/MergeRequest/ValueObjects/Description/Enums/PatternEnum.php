<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Description\Enums;

class PatternEnum
{
    /**
     * @var string
     */
    public const SHORT_DESCRIPTION_SECTION = '(## Краткое описание проделанной работы)([\s\S]*?)(## Автор)';

    /**
     * @var string
     */
    public const JIRA_TASK_ID = '[A-Z]{2,}-\d{1,9}';

    /**
     * @var string
     */
    public const REVIEWERS_DEFAULT_SECTION = '## Ревьювер\s\d+(?!:)([\s\S]*?)(?=\n## Ревьювер|$)';

    /**
     * @var string
     */
    public const REVIEWER_CHECKLIST_SECTION = '## Ревьювер.*?(###.*?)(?=\n## Ревьювер|$)';

    /**
     * @var string
     */
    public const NAMED_REVIEWER_SECTION = '## Ревьювер\s\d+:\s([А-Яа-яЁёA-Za-z]+\s[А-Яа-яЁёA-Za-z]+(\s[А-Яа-яЁёA-Za-z]+)?)[\s\S]*?(?=## Ревьювер|$)';

    /**
     * @var string
     */
    public const NAMED_REVIEWER_WITH_LINK_SECTION = '## Ревьювер\s\d+:\s\[(.*?)\][\s\S]*?(?=## Ревьювер|$)';
}
