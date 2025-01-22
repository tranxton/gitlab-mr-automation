<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Infrastructure\Repositories;

use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;

class MergeRequestTemplateRepository
{
    public function exists(string $path): bool
    {
        return (new Filesystem())->exists($path);
    }

    public function getContent(string $path): ?string
    {
        $file = new SplFileObject($path, 'r');
        $description = $file->fread($file->getSize());

        if ($description === false) {
            return null;
        }

        return $description;
    }
}
