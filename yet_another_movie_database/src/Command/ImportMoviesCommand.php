<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use GuzzleHttp\Client;


#[AsCommand(
    name: 'ImportMoviesCommand',
    description: 'Add a short description for your command',
)]
class ImportMoviesCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);

        try {
        $tmdbApiKey = $_ENV['TMDB_API_KEY'];
        $client = new Client();

        // Example: Fetching a list of popular movies
        $response = $client->get("https://api.themoviedb.org/3/movie/popular?api_key=$tmdbApiKey");

        // Parse and process the API response here, saving movie data to your database.

        $output->writeln('Movies imported successfully.');
        }
        catch (\Exception $e) {
            $logger->error('Error while importing movies: ' . $e->getMessage());
            $output->writeln('An error occurred during synchronization. Check the logs for details.');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
