<?php

namespace App\Command;

use App\Entity\Movie;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ImportMoviesCommand',
    description: 'Imports popular movies from The Movie Database API',
)]
class ImportMoviesCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private LoggerInterface $loggerInterface)
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
        $logger = $this->loggerInterface;
        $entityManagerInterface = $this->entityManager;

        try {
            $tmdbApiKey = $_ENV['TMDB_API_KEY'];
            $tmdbAccessToken = $_ENV['TMDB_ACCESS_TOKEN'];
            $client = new \GuzzleHttp\Client();
    
            $response = $client->request('GET', 'https://api.themoviedb.org/3/discover/movie', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tmdbAccessToken,
                    'accept' => 'application/json',
                ],
            ]);
    
            $moviesData = json_decode($response->getBody(), true);
            $movieRepository = $entityManagerInterface->getRepository(Movie::class);
            $batchSize = 20;
    
            foreach ($moviesData['results'] as $index => $movieData) {
                // Check if the movie already exists in the database to avoid duplicates
                $existingMovie = $movieRepository->findOneBy(['title' => $movieData['title']]);
    
                if (!$existingMovie) {
                    // Create a Movie entity and populate it with data
                    $movie = new Movie();
                    $movie->setTitle($movieData['title']);
                    $movie->setReleaseDate($movieData['release_date']);
                    $movie->setDescription($movieData['overview']);
                    
                    // Make a separate API request to get the director for each movie
                    $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/' . $movieData['id'] . '/credits?language=en-US', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $tmdbAccessToken,
                            'accept' => 'application/json',
                        ],
                    ]);
                    
                    $crew = json_decode($response->getBody(), true);
                    
                    // Loop through the crew array
                    foreach ($crew['crew'] as $crewMember) {
                        if ($crewMember['job'] === "Director") {
                            $crewName = $crewMember['name'];
                            $movie->setDirector($crewName);
                        }
                    }
                    
                    // Persist the movie to the database
                    $entityManagerInterface->persist($movie);
    
                    if (($index % $batchSize) === 0) {
                        $entityManagerInterface->flush();
                        $entityManagerInterface->clear(); // Detaches all objects from Doctrine!
                    }
                }
            }
    
            // Flush changes to the database once, after importing all movies
            $entityManagerInterface->flush();
    
            $output->writeln('Movies imported successfully.');
        } catch (\Exception $e) {
            $logger->error('Error while importing movies: ' . $e->getMessage());
            $output->writeln('An error occurred during synchronization. Check the logs for details.');
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}