<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequestTemplate;

interface MergeRequestDescriptionTemplatePort
{
    public function exists(string $path): bool;

    public function getContent(string $path): ?string;
}
