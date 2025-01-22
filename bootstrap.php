<?php

declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

return (new class() {
    /**
     * @throws Exception
     */
    public function init(): Application
    {
        $this->loadEnv();

        $container = $this->loadConfigs(new ContainerBuilder());
        $container->compile();

        return $this->loadCommands($container, new Application());
    }

    private function loadEnv(): void
    {
        $path = __DIR__ . '/.env';
        if (file_exists(__DIR__ . '/.env.local')) {
            $path = __DIR__ . '/.env.local';
        }

        (new Dotenv())->load($path);
    }

    /**
     * @throws Exception
     */
    private function loadConfigs(ContainerBuilder $container): ContainerBuilder
    {
        $configDirectory = __DIR__ . '/config';

        $configLoader = (new PhpFileLoader($container, new FileLocator($configDirectory)));

        $configFiles = new DirectoryIterator($configDirectory);

        /** @var SplFileInfo $configFile */
        foreach ($configFiles as $configFile) {
            if ($configFile->isFile() && $configFile->getExtension() === 'php') {
                $configLoader->load($configFile->getRealPath());
            }
        }

        return $container;
    }

    /**
     * @throws Exception
     */
    private function loadCommands(ContainerBuilder $container, Application $application): Application
    {
        $commandNames = array_keys($container->findTaggedServiceIds('gitlab.command'));

        foreach ($commandNames as $commandName) {
            /** @var Command $command */
            $command = $container->get($commandName);
            $application->add($command);
        }

        return $application;
    }
})->init();
