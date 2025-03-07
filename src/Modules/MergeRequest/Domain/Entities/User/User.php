<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Domain\Entities\User;

final class User
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $username;

    public function __construct(string $name, string $username)
    {
        $this->name = $name;
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
