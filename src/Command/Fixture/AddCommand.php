<?php

namespace App\Command\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Cycle\Command\CycleDependencyPromise;

use DuckPhp\App;
use MY\Service\FixtureService;

use Faker\Factory;

class AddCommand extends Command
{
    protected static $defaultName = 'fixture/add';

    private CycleDependencyPromise $promise;

    private const DEFAULT_COUNT = 10;

    public function __construct(CycleDependencyPromise $promise)
    {
        $this->promise = $promise;
        parent::__construct();
        FixtureService::SetPromise($promise);
    }
    private function initDuckPhp()
    {
        //*
        $path = realpath(__DIR__.'/../../..');
        $options=[];
        $options['path'] = $path;
        $options['handle_all_exception']=false;
        $options['handle_all_dev_error']=false;
        
        DuckPhp\App::init($options);
    }
    
    public function configure(): void
    {
        $this
            ->setDescription('Add fixtures')
            ->setHelp('This command adds random content')
            ->addArgument('count', InputArgument::OPTIONAL, 'Count', self::DEFAULT_COUNT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $count = (int)$input->getArgument('count');
        // get faker
        if (!class_exists(Factory::class)) {
            $io->error('Faker should be installed. Run `composer install --dev`');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        try {
            FixtureService::G()->run($count);
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        $io->success('Done');
        return ExitCode::OK;
    }
}
