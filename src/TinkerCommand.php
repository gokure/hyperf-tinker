<?php

declare(strict_types=1);

namespace Gokure\HyperfTinker;

use Psy\Shell;
use Psy\Configuration;
use Psy\VersionUpdater\Checker;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Command\Command as HyperfCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TinkerCommand extends HyperfCommand
{
    /**
     * Hyperf commands to include in the tinker shell.
     *
     * @var array
     */
    protected $commandWhitelist = [
        'migrate', 'migrate:install',
    ];

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('tinker');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Interact with your application');
        $this->addArgument('include', InputArgument::IS_ARRAY, 'Include file(s) before starting tinker');
        $this->addOption('execute', null, InputOption::VALUE_OPTIONAL, 'Execute the given code using Tinker');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->getApplication()->setCatchExceptions(false);

        $config = Configuration::fromInput($this->input);
        $config->setUpdateCheck(Checker::NEVER);
        $config->setUsePcntl(false);

        $config->getPresenter()->addCasters(
            $this->getCasters()
        );

        if ($this->input->getOption('execute')) {
            $config->setRawOutput(true);
        }

        $shell = new Shell($config);
        $shell->addCommands($this->getCommands());
        $shell->setIncludes($this->input->getArgument('include'));

        $env = function_exists('\Hyperf\Support\env') ? 'Hyperf\Support\env' : 'env';
        $path = $env('COMPOSER_VENDOR_DIR', BASE_PATH . DIRECTORY_SEPARATOR . 'vendor');

        $path .= '/composer/autoload_classmap.php';

        $config = $this->container->get(ConfigInterface::class);

        $loader = ClassAliasAutoloader::register(
            $shell,
            $path,
            $config->get('tinker.alias', []),
            $config->get('tinker.dont_alias', [])
        );

        if ($code = $this->input->getOption('execute')) {
            try {
                $shell->setOutput($this->output);
                $shell->execute($code);
            } finally {
                $loader->unregister();
            }

            return 0;
        }

        try {
            return $shell->run();
        } finally {
            $loader->unregister();
        }
    }

    /**
     * Get artisan commands to pass through to PsySH.
     *
     * @return array
     */
    protected function getCommands(): array
    {
        $commands = [];

        foreach ($this->getApplication()->all() as $name => $command) {
            if (in_array($name, $this->commandWhitelist, true)) {
                $commands[] = $command;
            }
        }

        $config = $this->container->get(ConfigInterface::class);

        foreach ($config->get('tinker.commands', []) as $command) {
            $commands[] = $this->getApplication()->add(
                $this->container->get($command)
            );
        }

        return $commands;
    }

    /**
     * Get an array of Laravel tailored casters.
     *
     * @return array
     */
    protected function getCasters()
    {
        $casters = [
            'Hyperf\Utils\Collection' => 'Gokure\HyperfTinker\TinkerCaster::castCollection',
            'Hyperf\Collection\Collection' => 'Gokure\HyperfTinker\TinkerCaster::castCollection',
            'Hyperf\Utils\Stringable' => 'Gokure\HyperfTinker\TinkerCaster::castStringable',
            'Hyperf\Stringable\Stringable' => 'Gokure\HyperfTinker\TinkerCaster::castStringable',
            'Hyperf\Database\Model\Model' => 'Gokure\HyperfTinker\TinkerCaster::castModel',
        ];

        $config = $this->container->get(ConfigInterface::class);

        return array_merge($casters, (array)$config->get('tinker.casters', []));
    }
}
