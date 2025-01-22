<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\Description\Enums;

class TemplateEnum
{
    /**
     * @var string
     */
    public const SHORT_DESCRIPTION_CONTENT = "\$1\n\n%s\n\n\$3";
}
