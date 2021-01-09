<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteAndRecreateDBWithStructureAndDataCommand extends Command
{
    // app-clean-db : (on choisit le nom de cmd) la commande que on tape dans CMD pour executer ce fichier
    // DeleteAndRecreateDBWithStructureAndDataCommand
    protected static $defaultName = 'app:clean-db';

    protected function configure(): void
    {
        $this
            ->setDescription('Supprimer et recrée la base de donnees avec structure et jeux de fixtures')
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->section("Supprussion de la base de donnees puis creation d'une nouvelle avec structure et données pré-remplies.");

        $this->runSymfonyCommand($input, $output, 'doctrine:database:drop', true);

        $this->runSymfonyCommand($input, $output, 'doctrine:database:create');

        $this->runSymfonyCommand($input, $output, 'doctrine:migrations:migrate');

        $this->runSymfonyCommand($input, $output, 'doctrine:fixtures:load');
       
        $io->success('Succes CMD: RAS => DATABASE propre et prête :) COOOLL !! ');

        return Command::SUCCESS;
    }

    private function runSymfonyCommand(
        InputInterface $input, 
        OutputInterface $output,
        string $command,
        bool $forceOption = false
    ): void
    {
        $application = $this->getApplication();

        if(!$application) {
            throw new \LogicException("Y a pas, NO application :( ");
        }

        $command = $application->find($command);

        if($forceOption){
            $input = new ArrayInput(['--force' => true ]); 
        }

        $input->setInteractive(false);

        $command->run($input, $output);
    }
}
