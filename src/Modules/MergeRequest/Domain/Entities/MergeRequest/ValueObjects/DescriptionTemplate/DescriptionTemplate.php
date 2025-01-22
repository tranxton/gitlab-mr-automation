<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\DescriptionTemplate;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\Exceptions\MergeRequestRuntimeException;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\MergeRequest\ValueObjects\DescriptionTemplate\Enums\PatternEnum;

class DescriptionTemplate
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->setValue($value);
    }

    private function setValue(string $value): void
    {
        if ($this->isNotTemplate($value)) {
            throw new MergeRequestRuntimeException('The passed template file has not a valid template.');
        }

        $this->value = $value;
    }

    private function isNotTemplate(string $value): bool
    {
        return preg_match(PatternEnum::TEMPLATE_PATTERN, $value) === 0;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
