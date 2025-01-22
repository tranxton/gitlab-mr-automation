<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\DescriptionTemplate\Enums;

class PatternEnum
{
    public const TEMPLATE_PATTERN = '/## Краткое описание проделанной работы[\s\S]*?## Автор/u';
}
