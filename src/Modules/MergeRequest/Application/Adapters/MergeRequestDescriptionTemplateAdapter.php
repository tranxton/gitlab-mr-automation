<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Adapters;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequestTemplate\Exceptions\MergeRequestTemplateRuntimeException;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\MergeRequestTemplate\MergeRequestDescriptionTemplatePort;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Infrastructure\Repositories\MergeRequestTemplateRepository;

class MergeRequestDescriptionTemplateAdapter implements MergeRequestDescriptionTemplatePort
{
    /**
     * @var MergeRequestTemplateRepository
     */
    private $repository;

    public function __construct(MergeRequestTemplateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function exists(string $path): bool
    {
        return $this->repository->exists($path);
    }

    public function getContent(string $path): ?string
    {
        if (!$this->exists($path)) {
            throw new MergeRequestTemplateRuntimeException(sprintf('Template file %s does not exist.', $path));
        }

        return $this->repository->getContent($path);
    }
}
