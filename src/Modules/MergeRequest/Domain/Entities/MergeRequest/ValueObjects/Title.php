<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects;

class Title
{
    /**
     * @var string
     */
    private const DRAFT_PATTERN = '/^(Draft:|WIP:)/su';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isDraft(): bool
    {
        return preg_match(self::DRAFT_PATTERN, $this->getValue()) === 1;
    }
}
