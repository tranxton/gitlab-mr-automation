<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driven\Task\ValueObjects;

class Transition
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setReview(): self
    {
        $this->value = 'Ревью';

        return $this;
    }
}
