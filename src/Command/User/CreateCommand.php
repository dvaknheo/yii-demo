<?php

namespace App\Command\User;

use App\Entity\User;
use Cycle\ORM\Transaction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Cycle\Command\CycleDependencyPromise;

class CreateCommand extends Command
{
    private CycleDependencyPromise $promise;

    protected static $defaultName = 'user/create';

    public function __construct(CycleDependencyPromise $promise)
    {
        $this->promise = $promise;
        parent::__construct();
        $this->doInit($promise);
    }
    protected function doInit($input_promise)
    {
        global $promise;
        
        $promise = $input_promise;
        
        $path = realpath(__DIR__.'/../../..');
        $options=[];
        $options['path'] = $path;
        $options['skip_setting_file'] = true;
        $options['skip_404_handler'] = true;
        $options['skip_exception_check'] = true;
        $options['handle_all_exception'] = false;
        $options['is_debug'] = true;
        
        App::G()->init($options);
    }

    public function configure(): void
    {
        $this
            ->setDescription('Creates a user')
            ->setHelp('This command allows you to create a user')
            ->addArgument('login', InputArgument::REQUIRED, 'Login')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $login = $input->getArgument('login');
        $password = $input->getArgument('password');
        try {
            UserService::G()->create($login, $password);
            $io->success('User created');
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
