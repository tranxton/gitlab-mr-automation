<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\Task\Infrastructure\Repositories;

class SignatureGenerator
{
    /**
     * @var string
     */
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function make(string $taskId): string
    {
        return hash_hmac('sha256', $taskId, $this->token);
    }
}
