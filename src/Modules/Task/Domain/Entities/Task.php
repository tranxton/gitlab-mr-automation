<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\Task\Domain\Entities;

class Task
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $transition;

    public function __construct(string $id, string $transition)
    {
        $this->id = $id;
        $this->transition = $transition;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTransition(): string
    {
        return $this->transition;
    }
}
