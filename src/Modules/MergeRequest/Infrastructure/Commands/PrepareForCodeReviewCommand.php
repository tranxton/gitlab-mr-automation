<?php

declare(strict_types=1);

namespace Tranxton\GitlabMrAutomation\Modules\MergeRequest\Infrastructure\Commands;

use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Dto\CodeReviewPreparationRequest;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\Exceptions\PrepareForCodeReviewException;
use Tranxton\GitlabMrAutomation\Modules\MergeRequest\Application\Ports\Driving\UpdateMergeRequestDescription\PrepareForCodeReviewPort;
use InvalidArgumentException;
use JMS\Serializer\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PrepareForCodeReviewCommand extends Command
{
    protected static $defaultName = 'merge-request:prepare-for-code-review';

    /**
     * @var PrepareForCodeReviewPort
     */
    private $updateDescriptionPort;
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        PrepareForCodeReviewPort $updateDescriptionPort,
        Serializer $serializer,
        $name = null
    ) {
        parent::__construct($name);

        $this->updateDescriptionPort = $updateDescriptionPort;
        $this->serializer = $serializer;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Updates the GitLab MR description based on a Twig template if not set.')
            ->addOption('branch', null, InputOption::VALUE_REQUIRED, 'Current Git branch')
            ->addOption('project_id', null, InputOption::VALUE_REQUIRED, 'The GitLab project ID')
            ->addOption('mr_iid', null, InputOption::VALUE_REQUIRED, 'The Merge Request internal ID')
            ->addOption('template_file', null, InputOption::VALUE_REQUIRED, 'Path to the template file')
            ->addOption('jira_task_url', null, InputOption::VALUE_REQUIRED, 'The Jira task URL')
            ->addOption('gitlab_user_profile_url', null, InputOption::VALUE_REQUIRED, 'The GitLab user profile URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $response = $this->updateDescriptionPort->update($this->getValidatedRequest($input));
        } catch (InvalidArgumentException|PrepareForCodeReviewException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        foreach ($response->getMessages() as $message) {
            $io->success($message);
        }

        return $response->getStatusCode();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getValidatedRequest(InputInterface $input): CodeReviewPreparationRequest
    {
        $inputs = [
            'template_file' => $input->getOption('template_file'),
            'project_id' => $input->getOption('project_id'),
            'branch' => $input->getOption('branch'),
            'mr_iid' => $input->getOption('mr_iid'),
            'jira_task_url' => $input->getOption('jira_task_url'),
            'gitlab_user_profile_url' => $input->getOption('gitlab_user_profile_url'),
        ];

        if (in_array(null, $inputs, true)) {
            throw new InvalidArgumentException(sprintf('Required options: %s', implode(', ', array_keys($inputs))));
        }

        $inputs['template_file'] = is_string($inputs['template_file']) ? $inputs['template_file'] : '';
        $inputs['template_file'] = sprintf('%s/%s', dirname(__DIR__, 6), $inputs['template_file']);

        /** @var CodeReviewPreparationRequest */
        return $this->serializer->deserialize(
            (string) json_encode($inputs),
            CodeReviewPreparationRequest::class,
            'json'
        );
    }
}
