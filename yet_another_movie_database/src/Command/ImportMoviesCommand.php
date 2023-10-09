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
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Movie;
use Psr\Log\LoggerInterface;


#[AsCommand(
    name: 'ImportMoviesCommand',
    description: 'Add a short description for your command',
)]
class ImportMoviesCommand extends Command
{

    public function __construct(private EntityManagerInterface $entityManager, private LoggerInterface $loggerInterface)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger =  $this->loggerInterface;
        $entityManagerInterface = $this->entityManager;
        try {
            $tmdbApiKey = $_ENV['TMDB_API_KEY'];

            $client = new \GuzzleHttp\Client();

            /*$response = $client->request('GET', 'https://api.themoviedb.org/3/movie/popular', [
                'headers' => [
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxZWQ2ZDE0NTI1ZGJjOWQyNGI2YmJlMjYwY2M0ZTk5MyIsInN1YiI6IjY1MWM3NTgwZWE4NGM3MDBjYTA5NjU1NiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.I-HWi6i5iL7Xybc2CzMH097wTxmEvESABu1PCJU_KAg',
                    'accept' => 'application/json',
                ],
            ]);*/

            $response = $client->request('GET', 'https://api.themoviedb.org/3/discover/movie', [
                'headers' => [
                  'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxZWQ2ZDE0NTI1ZGJjOWQyNGI2YmJlMjYwY2M0ZTk5MyIsInN1YiI6IjY1MWM3NTgwZWE4NGM3MDBjYTA5NjU1NiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.I-HWi6i5iL7Xybc2CzMH097wTxmEvESABu1PCJU_KAg',
                  'accept' => 'application/json',
                ],
              ]);

            $moviesData = json_decode($response->getBody(),true);

            foreach ($moviesData['results'] as $movieData) {
                // Create a Movie entity and populate it with data
                $movie = new Movie();
                $movie->setTitle($movieData['title']);
                $movie->setDirector('Director Name'); // Replace with actual director data
                $movie->setReleaseYear( $movieData['release_date']);
                $movie->setDescription($movieData['overview']);
                $output->writeln($movie->getTitle());
                // Persist the movie to the database
                $entityManagerInterface->persist($movie);
                $entityManagerInterface->flush();

            }
            $output->writeln('test');


            // Flush changes to the database

           // echo $response->getBody();
            /* $client = new Client();

             // Fetching a list of popular movies
             $response = $client->get("https://api.themoviedb.org/3/movie/popular?api_key=$tmdbApiKey");
             */
            // Parse and process the API response here, saving movie data to your database.

            $output->writeln('Movies imported successfully.');
        } catch (\Exception $e) {
            $logger->error('Error while importing movies: ' . $e->getMessage());
            $output->writeln('An error occurred during synchronization. Check the logs for details.');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}